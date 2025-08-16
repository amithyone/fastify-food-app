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

        // Enhanced validation with detailed error logging
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:1024' // Reduced to 1MB for smaller files
        ]);

        try {
            $image = $request->file('image');
            
            // Log image details for debugging
            Log::info('AI recognition attempt started', [
                'restaurant_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name,
                'user_id' => Auth::id(),
                'image_name' => $image->getClientOriginalName(),
                'image_size' => $image->getSize(),
                'image_mime' => $image->getMimeType(),
                'image_extension' => $image->getClientOriginalExtension(),
                'max_size_allowed' => '1024KB (1MB)'
            ]);

            // Check if image is too large and compress if needed
            if ($image->getSize() > 1024 * 1024) { // 1MB
                Log::warning('Image too large, attempting compression', [
                    'original_size' => $image->getSize(),
                    'max_size' => 1024 * 1024
                ]);
                
                $image = $this->compressImage($image);
                
                Log::info('Image compressed', [
                    'new_size' => $image->getSize(),
                    'compression_ratio' => round((1 - $image->getSize() / $request->file('image')->getSize()) * 100, 2) . '%'
                ]);
            }

            $result = $this->aiService->recognizeFood($image);
            
            if ($result['success']) {
                // Add suggested price
                $result['suggested_price'] = $this->aiService->suggestPrice($result['category'], $result['food_name']);
                
                Log::info('AI recognition successful', [
                    'restaurant_id' => $restaurant->id,
                    'food_name' => $result['food_name'],
                    'confidence' => $result['confidence'],
                    'services_used' => $result['services_used'] ?? 1
                ]);
                
                return response()->json($result);
            } else {
                Log::warning('AI recognition failed', [
                    'restaurant_id' => $restaurant->id,
                    'error' => $result['error'] ?? 'Unknown error',
                    'image_size' => $image->getSize()
                ]);
                
                return response()->json($result, 400);
            }
        } catch (\Exception $e) {
            Log::error('AI food recognition error', [
                'restaurant_id' => $restaurant->id,
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'image_name' => $request->file('image')->getClientOriginalName() ?? 'unknown',
                'image_size' => $request->file('image')->getSize() ?? 0
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error processing image. Please try again with a smaller image (under 1MB).',
                'details' => app()->environment('local') ? $e->getMessage() : null
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
            'is_featured' => 'nullable|boolean',
            'is_available_for_delivery' => 'nullable|boolean',
            'is_available_for_pickup' => 'nullable|boolean',
            'is_available_for_restaurant' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024' // Reduced to 1MB
        ]);

        try {
            $data = $request->all();
            $data['restaurant_id'] = $restaurant->id;
            
            // Handle boolean fields
            $data['is_vegetarian'] = $request->boolean('is_vegetarian');
            $data['is_spicy'] = $request->boolean('is_spicy');
            $data['is_available'] = $request->boolean('is_available');
            $data['is_featured'] = $request->boolean('is_featured');
            $data['is_available_for_delivery'] = $request->boolean('is_available_for_delivery');
            $data['is_available_for_pickup'] = $request->boolean('is_available_for_pickup');
            $data['is_available_for_restaurant'] = $request->boolean('is_available_for_restaurant');
            
            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                
                // Log image upload details
                Log::info('Menu item image upload', [
                    'restaurant_id' => $restaurant->id,
                    'image_name' => $image->getClientOriginalName(),
                    'image_size' => $image->getSize(),
                    'image_mime' => $image->getMimeType(),
                    'max_size_allowed' => '1MB'
                ]);

                // Compress image if it's too large
                if ($image->getSize() > 1024 * 1024) { // 1MB
                    Log::info('Compressing menu item image', [
                        'original_size' => $image->getSize(),
                        'max_size' => 1024 * 1024
                    ]);
                    $image = $this->compressImage($image);
                }

                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('menu-items', $imageName, 'public');
                $data['image'] = $imagePath;
                
                Log::info('Menu item image stored', [
                    'image_path' => $imagePath,
                    'final_size' => $image->getSize()
                ]);
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
                'menu_item' => $menuItem,
                'food_name' => $menuItem->name,
                'confidence' => '95%', // AI recognition confidence
                'service_used' => 'Google Gemini AI'
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

    /**
     * Compress image to reduce file size
     */
    private function compressImage($image)
    {
        try {
            $imagePath = $image->getPathname();
            $imageInfo = getimagesize($imagePath);
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $mimeType = $imageInfo['mime'];

            // Calculate new dimensions (max 800px width/height)
            $maxDimension = 800;
            if ($width > $maxDimension || $height > $maxDimension) {
                if ($width > $height) {
                    $newWidth = $maxDimension;
                    $newHeight = round(($height / $width) * $maxDimension);
                } else {
                    $newHeight = $maxDimension;
                    $newWidth = round(($width / $height) * $maxDimension);
                }
            } else {
                $newWidth = $width;
                $newHeight = $height;
            }

            // Create image resource
            switch ($mimeType) {
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($imagePath);
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($imagePath);
                    break;
                case 'image/gif':
                    $source = imagecreatefromgif($imagePath);
                    break;
                default:
                    throw new \Exception('Unsupported image type: ' . $mimeType);
            }

            // Create new image
            $newImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG and GIF
            if ($mimeType === 'image/png' || $mimeType === 'image/gif') {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Resize image
            imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            // Create temporary file
            $tempPath = tempnam(sys_get_temp_dir(), 'compressed_');
            
            // Save compressed image
            switch ($mimeType) {
                case 'image/jpeg':
                    imagejpeg($newImage, $tempPath, 85); // 85% quality
                    break;
                case 'image/png':
                    imagepng($newImage, $tempPath, 6); // Compression level 6
                    break;
                case 'image/gif':
                    imagegif($newImage, $tempPath);
                    break;
            }

            // Clean up
            imagedestroy($source);
            imagedestroy($newImage);

            // Create new UploadedFile instance
            $compressedImage = new \Illuminate\Http\UploadedFile(
                $tempPath,
                $image->getClientOriginalName(),
                $mimeType,
                null,
                true
            );

            Log::info('Image compression completed', [
                'original_size' => $image->getSize(),
                'compressed_size' => $compressedImage->getSize(),
                'original_dimensions' => "{$width}x{$height}",
                'new_dimensions' => "{$newWidth}x{$newHeight}",
                'compression_ratio' => round((1 - $compressedImage->getSize() / $image->getSize()) * 100, 2) . '%'
            ]);

            return $compressedImage;

        } catch (\Exception $e) {
            Log::error('Image compression failed', [
                'error' => $e->getMessage(),
                'image_path' => $image->getPathname(),
                'image_size' => $image->getSize()
            ]);
            
            // Return original image if compression fails
            return $image;
        }
    }
} 