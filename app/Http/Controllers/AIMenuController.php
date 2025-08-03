<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\MenuItem;
use App\Models\Category;
use App\Services\AIFoodRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AIMenuController extends Controller
{
    protected $aiService;

    public function __construct(AIFoodRecognitionService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Recognize food from uploaded image
     */
    public function recognizeFood(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check authorization
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant AI features.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to access the restaurant AI features.');
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $result = $this->aiService->recognizeFood($request->file('image'));
            
            if ($result['success']) {
                // Add suggested price
                $result['suggested_price'] = $this->aiService->suggestPrice($result['category'], $result['food_name']);
                
                return response()->json($result);
            } else {
                return response()->json($result, 400);
            }
        } catch (\Exception $e) {
            Log::error('AI food recognition error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error processing image. Please try again.'
            ], 500);
        }
    }

    /**
     * Store menu item from AI recognition
     */
    public function storeMenuItem(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check authorization
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant AI features.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to access the restaurant AI features.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'ingredients' => 'nullable|string|max:500',
            'allergens' => 'nullable|string|max:500',
            'is_vegetarian' => 'nullable|boolean',
            'is_spicy' => 'nullable|boolean',
            'is_available' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $data = $request->all();
            $data['restaurant_id'] = $restaurant->id;
            
            // Handle boolean fields
            $data['is_vegetarian'] = $request->boolean('is_vegetarian');
            $data['is_spicy'] = $request->boolean('is_spicy');
            $data['is_available'] = $request->boolean('is_available');
            $data['is_featured'] = false; // Default to false
            
            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('menu-items', $imageName, 'public');
                $data['image'] = $imagePath;
            }
            
            // Create menu item
            $menuItem = MenuItem::create($data);
            
            Log::info('AI menu item created', [
                'menu_item_id' => $menuItem->id,
                'restaurant_id' => $restaurant->id,
                'name' => $menuItem->name
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu item added successfully!',
                'menu_item' => $menuItem
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error creating AI menu item: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating menu item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available categories for AI recognition
     */
    public function getCategories($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check authorization
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant AI features.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to access the restaurant AI features.');
        }

        $categories = $restaurant->categories()->where('is_active', true)->get();
        
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * Handle user corrections and save for AI learning
     */
    public function correctRecognition(Request $request, $slug)
    {
        try {
            $request->validate([
                'image_hash' => 'required|string',
                'corrected_food' => 'required|array',
                'user_feedback' => 'nullable|string',
            ]);

            $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
            
            // Check if user can manage this restaurant
            if (!auth()->user()->canManageRestaurant($restaurant)) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
            }

            $aiService = app(AIFoodRecognitionService::class);
            $success = $aiService->learnFromCorrection(
                $request->image_hash,
                $request->corrected_food,
                $request->user_feedback
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thank you! The AI has learned from your correction and will be more accurate next time.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save correction. Please try again.'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('AI correction error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing correction. Please try again.'
            ], 500);
        }
    }
} 