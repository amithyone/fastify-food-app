<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\Story;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function index(Request $request, $slug = null)
    {
        if ($slug) {
            // Restaurant-specific menu
            $restaurant = Restaurant::where('slug', $slug)->where('is_active', true)->firstOrFail();
            $categories = $restaurant->categories()->with('menuItems')->where('is_active', true)->get();
            $menuItems = $restaurant->menuItems()->with(['category', 'restaurant'])->where('is_available', true)->get();
            
            // Ensure all menu items have the image_url attribute
            $menuItems->each(function ($item) {
                $item->image_url = $item->image_url;
            });
            $stories = $restaurant->stories()->active()->ordered()->get();
            
            return view('menu.index', compact('categories', 'menuItems', 'stories', 'restaurant'));
        } else {
            // Location-based menu for guest users
            $userLocation = $this->locationService->getUserLocation($request);
            $restaurants = $this->locationService->getRestaurantsByLocation(
                $userLocation['city'], 
                $userLocation['state'], 
                $userLocation['country']
            );
            
            // If no restaurants found in user's location, show all active restaurants
            if ($restaurants->isEmpty()) {
                $restaurants = Restaurant::where('is_active', true)->get();
            }
            
            // Get all categories and menu items from restaurants in the location
            $categories = collect();
            $menuItems = collect();
            $stories = collect();
            
            foreach ($restaurants as $restaurant) {
                $categories = $categories->merge($restaurant->categories()->with('menuItems')->where('is_active', true)->get());
                $menuItems = $menuItems->merge($restaurant->menuItems()->with(['category', 'restaurant'])->where('is_available', true)->get());
                $stories = $stories->merge($restaurant->stories()->active()->ordered()->get());
            }
            
            // Remove duplicates
            $categories = $categories->unique('id');
            $menuItems = $menuItems->unique('id');
            $stories = $stories->unique('id');
            
            // Ensure all menu items have the image_url attribute
            $menuItems->each(function ($item) {
                $item->image_url = $item->image_url;
            });
            
            return view('menu.index', compact('categories', 'menuItems', 'stories', 'restaurants', 'userLocation'));
        }
    }

    public function show($id)
    {
        $menuItem = MenuItem::with(['category', 'restaurant'])->findOrFail($id);
        return view('menu.show', compact('menuItem'));
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        
        if ($query) {
            $menuItems = MenuItem::with(['category', 'restaurant'])
                ->where('is_available', true)
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->get();
        } else {
            $menuItems = MenuItem::with(['category', 'restaurant'])->where('is_available', true)->get();
        }
        
        // Ensure all menu items have the image_url attribute
        $menuItems->each(function ($item) {
            $item->image_url = $item->image_url;
        });
        
        return response()->json($menuItems);
    }

    public function getMenuItems(Request $request)
    {
        $categoryId = $request->get('category_id');
        
        if ($categoryId && $categoryId !== 'all') {
            $menuItems = MenuItem::with(['category', 'restaurant'])
                ->where('category_id', $categoryId)
                ->where('is_available', true)
                ->get();
        } else {
            $menuItems = MenuItem::with(['category', 'restaurant'])
                ->where('is_available', true)
                ->get();
        }
        
        // Add image_url to each menu item
        $menuItems->each(function ($item) {
            $item->image_url = $item->image_url;
        });
        
        return response()->json($menuItems);
    }

    public function getCategories()
    {
        $categories = Category::where('is_active', true)->get();
        return response()->json($categories);
    }

    public function items(Request $request)
    {
        $categoryId = $request->get('category_id');
        
        if ($categoryId && $categoryId !== 'all') {
            $menuItems = MenuItem::with(['category', 'restaurant'])
                ->where('category_id', $categoryId)
                ->where('is_available', true)
                ->get();
        } else {
            $menuItems = MenuItem::with(['category', 'restaurant'])
                ->where('is_available', true)
                ->get();
        }
        
        // Add image_url to each menu item
        $menuItems->each(function ($item) {
            $item->image_url = $item->image_url;
        });
        
        return response()->json($menuItems);
    }

    // Restaurant Menu Management Methods
    public function restaurantIndex($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check if user is a manager of this restaurant
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant menu. You need manager privileges.');
            }
        } else {
            // Redirect to login if not authenticated
            return redirect()->route('login')->with('error', 'Please login to access the restaurant menu.');
        }
        
        $menuItems = $restaurant->menuItems()->with(['category', 'restaurant'])->get();
        
        // Ensure all menu items have the image_url attribute
        $menuItems->each(function ($item) {
            $item->image_url = $item->image_url;
        });
        $restaurantCategories = $restaurant->categories()->get();
        
        // Get global main categories (categories with restaurant_id = null)
        $globalCategories = Category::globalMainCategories()->ordered()->get();
        
        // Get all categories available for this restaurant (global + restaurant-specific)
        $allCategories = Category::availableForRestaurant($restaurant->id)
                                ->where('is_active', true)
                                ->ordered()
                                ->get();
        
        // Get existing sub-categories that can be selected by managers
        $existingSubCategories = Category::where('type', 'sub')
                                        ->where('is_active', true)
                                        ->whereNotNull('parent_id')
                                        ->ordered()
                                        ->get();
        
        return view('restaurant.menu.index', compact('restaurant', 'menuItems', 'restaurantCategories', 'globalCategories', 'allCategories', 'existingSubCategories'));
    }

    public function restaurantCreate($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant menu. You need manager privileges.');
            }
        } else {
            // Redirect to login if not authenticated
            return redirect()->route('login')->with('error', 'Please login to access the restaurant menu.');
        }
        
        $categories = $restaurant->categories()->get();
        
        return view('restaurant.menu.create', compact('restaurant', 'categories'));
    }

    public function restaurantStore(Request $request, $slug)
    {
        \Log::info('Menu item creation attempt', [
            'user_id' => Auth::id(),
            'restaurant_slug' => $slug,
            'request_data' => $request->all(),
            'form_fields' => [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'category_id' => $request->input('category_id'),
                'is_available' => $request->has('is_available'),
                'is_featured' => $request->has('is_featured'),
                'is_available_for_delivery' => $request->has('is_available_for_delivery'),
                'is_vegetarian' => $request->has('is_vegetarian'),
                'is_spicy' => $request->has('is_spicy'),
                'ingredients' => $request->input('ingredients'),
                'allergens' => $request->input('allergens'),
                'has_image' => $request->hasFile('image')
            ]
        ]);
        
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant menu. You need manager privileges.');
            }
        } else {
            // Redirect to login if not authenticated
            return redirect()->route('login')->with('error', 'Please login to access the restaurant menu.');
        }
        
        try {
            \Log::info('Raw request data before validation', [
                'price' => $request->input('price'),
                'price_type' => gettype($request->input('price')),
                'all_data' => $request->all()
            ]);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'price' => 'required|numeric|min:0',
                'category_id' => 'nullable|exists:categories,id',
                'is_available' => 'nullable',
                'is_featured' => 'nullable',
                'is_available_for_delivery' => 'nullable',
                'is_available_for_pickup' => 'nullable',
                'is_available_for_restaurant' => 'nullable',
                'is_vegetarian' => 'nullable',
                'is_spicy' => 'nullable',
                'ingredients' => 'nullable|string|max:500',
                'allergens' => 'nullable|string|max:500',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            
            \Log::info('Validation passed', ['validated_data' => $validated]);
            
            $validated['restaurant_id'] = $restaurant->id;
            
            // Set default category if none provided
            if (empty($validated['category_id'])) {
                $defaultCategory = Category::where('restaurant_id', $restaurant->id)->first();
                if ($defaultCategory) {
                    $validated['category_id'] = $defaultCategory->id;
                }
            }
            
            // Set default values for boolean fields
            $validated['is_available'] = $request->has('is_available') || $request->input('is_available') === 'on';
            $validated['is_featured'] = $request->has('is_featured') || $request->input('is_featured') === 'on';
            $validated['is_available_for_delivery'] = $request->has('is_available_for_delivery') || $request->input('is_available_for_delivery') === 'on';
            $validated['is_available_for_pickup'] = $request->has('is_available_for_pickup') || $request->input('is_available_for_pickup') === 'on';
            $validated['is_available_for_restaurant'] = $request->has('is_available_for_restaurant') || $request->input('is_available_for_restaurant') === 'on';
            $validated['is_vegetarian'] = $request->has('is_vegetarian') || $request->input('is_vegetarian') === 'on';
            $validated['is_spicy'] = $request->has('is_spicy') || $request->input('is_spicy') === 'on';
            
            // Handle image upload or selection, otherwise use restaurant default if available
            $imageSource = $request->input('image_source', 'upload');
            
            if ($imageSource === 'upload' && $request->hasFile('image')) {
                \Log::info('Image upload detected', [
                    'file_name' => $request->file('image')->getClientOriginalName(),
                    'file_size' => $request->file('image')->getSize(),
                    'file_mime' => $request->file('image')->getMimeType(),
                    'file_extension' => $request->file('image')->getClientOriginalExtension(),
                ]);
                
                try {
                    $validated['image'] = $request->file('image')->store('menu-items', 'public');
                    \Log::info('Image uploaded successfully', [
                        'image_path' => $validated['image'],
                        'full_url' => Storage::disk('public')->url($validated['image']),
                        'file_exists' => Storage::disk('public')->exists($validated['image'])
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Image upload failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload image: ' . $e->getMessage()
                    ], 500);
                }
            } elseif ($imageSource === 'existing' && $request->input('selected_image_id')) {
                $selectedImageId = $request->input('selected_image_id');
                $restaurantImage = \App\Models\RestaurantImage::where('restaurant_id', $restaurant->id)
                    ->where('id', $selectedImageId)
                    ->first();
                
                if ($restaurantImage) {
                    // Reference the restaurant image directly (no copying)
                    $validated['restaurant_image_id'] = $restaurantImage->id;
                    
                    \Log::info('Restaurant image referenced for menu item', [
                        'restaurant_image_id' => $restaurantImage->id,
                        'original_path' => $restaurantImage->file_path
                    ]);
                } else {
                    \Log::error('Selected image not found', [
                        'selected_image_id' => $selectedImageId,
                        'restaurant_id' => $restaurant->id
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected image not found'
                    ], 400);
                }
            }

            // If no image provided, use restaurant default if available
            if (empty($validated['image']) && $restaurant->default_menu_image) {
                $validated['image'] = $restaurant->default_menu_image;
                \Log::info('Using restaurant default menu image for new item', [
                    'restaurant_id' => $restaurant->id,
                    'image' => $validated['image']
                ]);
            }
            
            $menuItem = MenuItem::create($validated);
            
            \Log::info('Menu item created successfully', [
                'menu_item_id' => $menuItem->id,
                'menu_item_name' => $menuItem->name,
                'menu_item_price' => $menuItem->price,
                'menu_item_category' => $menuItem->category->name ?? 'No category',
                'menu_item_available' => $menuItem->is_available,
                'menu_item_featured' => $menuItem->is_featured,
                'menu_item_available_for_delivery' => $menuItem->is_available_for_delivery,
                'menu_item_vegetarian' => $menuItem->is_vegetarian,
                'menu_item_spicy' => $menuItem->is_spicy,
                'menu_item_ingredients' => $menuItem->ingredients,
                'menu_item_allergens' => $menuItem->allergens,
                'menu_item_image' => $menuItem->image
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu item created successfully!',
                'menu_item' => $menuItem
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Menu item creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create menu item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restaurantEdit($slug, $item)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $menuItem = MenuItem::where('id', $item)->where('restaurant_id', $restaurant->id)->firstOrFail();
        
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant menu. You need manager privileges.');
            }
        } else {
            // Redirect to login if not authenticated
            return redirect()->route('login')->with('error', 'Please login to access the restaurant menu.');
        }
        
        $categories = $restaurant->categories()->get();
        
        return view('restaurant.menu.edit', compact('restaurant', 'menuItem', 'categories'));
    }

    public function restaurantUpdate(Request $request, $slug, $item)
    {
        \Log::info('Menu item update attempt', [
            'user_id' => Auth::id(),
            'restaurant_slug' => $slug,
            'menu_item_id' => $item,
            'request_data' => $request->all()
        ]);
        
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $menuItem = MenuItem::where('id', $item)->where('restaurant_id', $restaurant->id)->firstOrFail();
        
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant menu. You need manager privileges.');
            }
        } else {
            // Redirect to login if not authenticated
            return redirect()->route('login')->with('error', 'Please login to access the restaurant menu.');
        }
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'price' => 'required|numeric|min:0',
                'category_id' => 'nullable|exists:categories,id',
                'is_available' => 'nullable',
                'is_featured' => 'nullable',
                'is_available_for_delivery' => 'nullable',
                'is_available_for_pickup' => 'nullable',
                'is_available_for_restaurant' => 'nullable',
                'is_vegetarian' => 'nullable',
                'is_spicy' => 'nullable',
                'ingredients' => 'nullable|string|max:500',
                'allergens' => 'nullable|string|max:500',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            
            \Log::info('Validation passed for update', ['validated_data' => $validated]);
            
            // Set default values for boolean fields
            $validated['is_available'] = $request->has('is_available') || $request->input('is_available') === 'on';
            $validated['is_featured'] = $request->has('is_featured') || $request->input('is_featured') === 'on';
            $validated['is_available_for_delivery'] = $request->has('is_available_for_delivery') || $request->input('is_available_for_delivery') === 'on';
            $validated['is_available_for_pickup'] = $request->has('is_available_for_pickup') || $request->input('is_available_for_pickup') === 'on';
            $validated['is_available_for_restaurant'] = $request->has('is_available_for_restaurant') || $request->input('is_available_for_restaurant') === 'on';
            $validated['is_vegetarian'] = $request->has('is_vegetarian') || $request->input('is_vegetarian') === 'on';
            $validated['is_spicy'] = $request->has('is_spicy') || $request->input('is_spicy') === 'on';
            
            // Set default category if none is provided
            if (empty($validated['category_id'])) {
                $firstCategory = Category::where('restaurant_id', $restaurant->id)->first();
                if ($firstCategory) {
                    $validated['category_id'] = $firstCategory->id;
                }
            }
            
            // Handle image upload or selection
            $imageSource = $request->input('image_source', 'upload');
            
            if ($imageSource === 'upload' && $request->hasFile('image')) {
                \Log::info('Image upload detected for update', [
                    'file_name' => $request->file('image')->getClientOriginalName(),
                    'file_size' => $request->file('image')->getSize(),
                    'file_mime' => $request->file('image')->getMimeType(),
                    'file_extension' => $request->file('image')->getClientOriginalExtension(),
                ]);
                
                try {
                    $validated['image'] = $request->file('image')->store('menu-items', 'public');
                    // Clear any existing restaurant image reference
                    $validated['restaurant_image_id'] = null;
                    
                    \Log::info('Image uploaded successfully for update', [
                        'image_path' => $validated['image'],
                        'full_url' => Storage::disk('public')->url($validated['image']),
                        'file_exists' => Storage::disk('public')->exists($validated['image'])
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Image upload failed for update', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload image: ' . $e->getMessage()
                    ], 500);
                }
            } elseif ($imageSource === 'existing' && $request->input('selected_image_id')) {
                $selectedImageId = $request->input('selected_image_id');
                $restaurantImage = \App\Models\RestaurantImage::where('restaurant_id', $restaurant->id)
                    ->where('id', $selectedImageId)
                    ->first();
                
                if ($restaurantImage) {
                    // Reference the restaurant image directly (no copying)
                    $validated['restaurant_image_id'] = $restaurantImage->id;
                    // Clear any existing uploaded image
                    $validated['image'] = null;
                    
                    \Log::info('Restaurant image referenced for menu item update', [
                        'restaurant_image_id' => $restaurantImage->id,
                        'original_path' => $restaurantImage->file_path
                    ]);
                } else {
                    \Log::error('Selected image not found for update', [
                        'selected_image_id' => $selectedImageId,
                        'restaurant_id' => $restaurant->id
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected image not found'
                    ], 400);
                }
            }
            
            $menuItem->update($validated);
            
            \Log::info('Menu item updated successfully', [
                'menu_item_id' => $menuItem->id,
                'menu_item_name' => $menuItem->name
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu item updated successfully!',
                'menu_item' => $menuItem
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed for update', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Menu item update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update menu item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restaurantDestroy($slug, $item)
    {
        \Log::info('Menu item deletion attempt', [
            'user_id' => Auth::id(),
            'restaurant_slug' => $slug,
            'menu_item_id' => $item
        ]);
        
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $menuItem = MenuItem::where('id', $item)->where('restaurant_id', $restaurant->id)->firstOrFail();
        
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant menu. You need manager privileges.');
            }
        } else {
            // Redirect to login if not authenticated
            return redirect()->route('login')->with('error', 'Please login to access the restaurant menu.');
        }
        
        try {
            $menuItem->delete();
            
            \Log::info('Menu item deleted successfully', [
                'menu_item_id' => $item,
                'menu_item_name' => $menuItem->name
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu item deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Menu item deletion failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete menu item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adminIndex()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // If user is admin, show all menu items
        if ($user->isAdmin()) {
            $menuItems = MenuItem::with(['category', 'restaurant'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } 
        // If user is a restaurant manager, show only their restaurant's menu items
        elseif ($user->isRestaurantManager()) {
            $restaurants = \App\Models\Manager::getUserRestaurants($user->id);
            $restaurantIds = $restaurants->pluck('id');
            
            $menuItems = MenuItem::with(['category', 'restaurant'])
                ->whereIn('restaurant_id', $restaurantIds)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }
        
        // Otherwise, unauthorized
        else {
            abort(403, 'Unauthorized access to admin menu. You need manager privileges.');
        }
        
        // Ensure all menu items have the image_url attribute
        $menuItems->each(function ($item) {
            $item->image_url = $item->image_url;
        });
        
        return view('menu.admin-index', compact('menuItems'));
    }

    // Category Management Methods
    public function storeCategory(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized access to restaurant categories. You need manager privileges.'], 403);
                }
                abort(403, 'Unauthorized access to restaurant categories. You need manager privileges.');
            }
        } else {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Please login to access the restaurant categories.'], 401);
            }
            return redirect()->route('login')->with('error', 'Please login to access the restaurant categories.');
        }
        
        try {
            \Log::info('Store category request', [
                'request_data' => $request->all(),
                'restaurant_id' => $restaurant->id
            ]);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'required|exists:categories,id', // Make parent_id required for managers
                'use_existing_category' => 'nullable|boolean',
                'existing_category_id' => 'nullable|exists:categories,id',
                'force_create' => 'nullable|boolean', // New field to force create even if similar exists
            ]);
            
            // Check if user is manager (not admin) - managers can only create sub-categories
            $isManager = Auth::user() && !Auth::user()->isAdmin() && \App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager');
            
            if ($isManager && empty($validated['parent_id'])) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Managers can only create sub-categories. Please select a parent category.'
                    ], 422);
                }
                return redirect()->route('restaurant.menu', $slug)->with('error', 'Managers can only create sub-categories. Please select a parent category.');
            }
            
            // If user wants to use an existing sub-category
            if (!empty($validated['use_existing_category']) && !empty($validated['existing_category_id'])) {
                $existingCategory = Category::find($validated['existing_category_id']);
                
                if (!$existingCategory || $existingCategory->type !== 'sub') {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false, 
                            'message' => 'Invalid sub-category selected. Please select a valid sub-category.'
                        ], 422);
                    }
                    return redirect()->route('restaurant.menu', $slug)->with('error', 'Invalid sub-category selected. Please select a valid sub-category.');
                }
                
                // Check if this sub-category is already being used by this restaurant
                $existingUsage = Category::where('restaurant_id', $restaurant->id)
                                        ->where('name', $existingCategory->name)
                                        ->where('parent_id', $existingCategory->parent_id)
                                        ->first();
                
                if ($existingUsage) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => true, 
                            'message' => 'Sub-category "' . $existingCategory->name . '" is already available for your restaurant.',
                            'category' => $existingUsage
                        ]);
                    }
                    return redirect()->route('restaurant.menu', $slug)->with('success', 'Sub-category "' . $existingCategory->name . '" is already available for your restaurant.');
                }
                
                // Share the existing sub-category with this restaurant
                $existingCategory->addRestaurant($restaurant->id);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true, 
                        'message' => 'Sub-category "' . $existingCategory->name . '" is now available for your restaurant!',
                        'category' => $existingCategory
                    ]);
                }
                
                return redirect()->route('restaurant.menu', $slug)->with('success', 'Sub-category "' . $existingCategory->name . '" is now available for your restaurant!');
            }
            
            // Smart sub-category creation with matching
            $categoryName = trim($validated['name']);
            $parentId = $validated['parent_id'];
            $forceCreate = $validated['force_create'] ?? false;
            
            // For managers, always create sub-categories
            $type = 'sub';
            
            // If not forcing creation, check for similar existing sub-categories
            if (!$forceCreate) {
                $similarCategories = Category::findSimilar($categoryName, $parentId, 'sub');
                
                if ($similarCategories->isNotEmpty()) {
                    // Check if any similar sub-category can be shared
                    foreach ($similarCategories as $similarCategory) {
                        if ($similarCategory->canBeUsedByRestaurant($restaurant->id)) {
                            // Restaurant can already use this sub-category
                            if ($request->expectsJson()) {
                                return response()->json([
                                    'success' => true,
                                    'message' => 'Sub-category "' . $similarCategory->name . '" is already available for your restaurant.',
                                    'category' => $similarCategory,
                                    'similar_found' => true
                                ]);
                            }
                            return redirect()->route('restaurant.menu', $slug)->with('success', 'Sub-category "' . $similarCategory->name . '" is already available for your restaurant.');
                        }
                    }
                    
                    // Check if we can share an existing sub-category
                    $exactMatch = $similarCategories->first(function($cat) use ($categoryName) {
                        return strtolower(trim($cat->name)) === strtolower($categoryName);
                    });
                    
                    if ($exactMatch) {
                        // Share the existing sub-category
                        $exactMatch->addRestaurant($restaurant->id);
                        
                        if ($request->expectsJson()) {
                            return response()->json([
                                'success' => true,
                                'message' => 'Sub-category "' . $exactMatch->name . '" is now shared with your restaurant!',
                                'category' => $exactMatch,
                                'shared' => true
                            ]);
                        }
                        return redirect()->route('restaurant.menu', $slug)->with('success', 'Sub-category "' . $exactMatch->name . '" is now shared with your restaurant!');
                    }
                    
                    // Show similar sub-categories to user
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Similar sub-categories found. Please choose one or create a new one.',
                            'similar_categories' => $similarCategories->map(function($cat) {
                                return [
                                    'id' => $cat->id,
                                    'name' => $cat->name,
                                    'is_shared' => $cat->isShared(),
                                    'restaurant_count' => $cat->isShared() ? count($cat->restaurant_ids ?? []) : 1
                                ];
                            }),
                            'suggest_sharing' => true
                        ], 422);
                    }
                    
                    // For non-AJAX requests, redirect with similar categories info
                    return redirect()->route('restaurant.menu', $slug)->with('similar_categories', $similarCategories);
                }
            }
            
            // Create new sub-category (either forced or no similar found)
            $category = Category::findOrCreateShared($categoryName, $restaurant->id, $parentId, 'sub');
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => $category->isShared() ? 
                        'Sub-category "' . $category->name . '" created and is now available for sharing with other restaurants!' :
                        'Sub-category "' . $category->name . '" created successfully!',
                    'category' => $category,
                    'created' => true
                ]);
            }
            
            return redirect()->route('restaurant.menu', $slug)->with('success', 
                $category->isShared() ? 
                'Sub-category "' . $category->name . '" created and is now available for sharing with other restaurants!' :
                'Sub-category "' . $category->name . '" created successfully!'
            );
            
        } catch (\Exception $e) {
            \Log::error('Error creating category: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error creating category: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('restaurant.menu', $slug)->with('error', 'Error creating category: ' . $e->getMessage());
        }
    }

    public function updateCategory(Request $request, $slug, $category)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $category = Category::where('id', $category)->where('restaurant_id', $restaurant->id)->firstOrFail();
        
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized access to restaurant categories. You need manager privileges.'], 403);
                }
                abort(403, 'Unauthorized access to restaurant categories. You need manager privileges.');
            }
        } else {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Please login to access the restaurant categories.'], 401);
            }
            return redirect()->route('login')->with('error', 'Please login to access the restaurant categories.');
        }
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:categories,id',
                'is_active' => 'boolean',
            ]);
            
            // If parent_id is provided, validate it's a global main category
            if (!empty($validated['parent_id'])) {
                $parentCategory = Category::find($validated['parent_id']);
                if (!$parentCategory || $parentCategory->type !== 'main' || $parentCategory->restaurant_id !== null) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false, 
                            'message' => 'Invalid parent category selected. Please select a main category.'
                        ], 422);
                    }
                    return redirect()->route('restaurant.menu', $slug)->with('error', 'Invalid parent category selected. Please select a main category.');
                }
                $validated['type'] = 'sub'; // Category with parent is a sub-category
            } else {
                $validated['type'] = 'main'; // Category without parent is a main category
                $validated['parent_id'] = null; // Ensure parent_id is null
            }
            
            $validated['is_active'] = $validated['is_active'] ?? true;
            
            // Generate unique slug for the category
            $baseSlug = \Illuminate\Support\Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;
            
            // Check if slug already exists (excluding current category)
            while (Category::where('slug', $slug)
                          ->where('restaurant_id', $restaurant->id)
                          ->where('id', '!=', $category->id)
                          ->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            $validated['slug'] = $slug;
            
            $category->update($validated);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Category updated successfully!',
                    'category' => $category->fresh()
                ]);
            }
            
            return redirect()->route('restaurant.menu', $slug)->with('success', 'Category updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Error updating category: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error updating category: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('restaurant.menu', $slug)->with('error', 'Error updating category: ' . $e->getMessage());
        }
    }

    public function destroyCategory(Request $request, $slug, $category)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Find category - either owned by this restaurant or shared with this restaurant
        $category = Category::where('id', $category)
                           ->where(function($query) use ($restaurant) {
                               $query->where('restaurant_id', $restaurant->id)
                                     ->orWhereRaw('JSON_CONTAINS(restaurant_ids, ?)', [json_encode($restaurant->id)]);
                           })
                           ->firstOrFail();
        
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized access to restaurant categories. You need manager privileges.'], 403);
                }
                abort(403, 'Unauthorized access to restaurant categories. You need manager privileges.');
            }
        } else {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Please login to access the restaurant categories.'], 401);
            }
            return redirect()->route('login')->with('error', 'Please login to access the restaurant categories.');
        }
        
        try {
            // Check if category has menu items for this restaurant
            $menuItemsCount = $category->menuItems()->where('restaurant_id', $restaurant->id)->count();
            if ($menuItemsCount > 0) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Cannot delete category that has menu items. Please move or delete the menu items first.'
                    ], 422);
                }
                return redirect()->route('restaurant.menu', $slug)->with('error', 'Cannot delete category that has menu items. Please move or delete the menu items first.');
            }
            
            \Log::info('Category deletion/removal', [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'is_shared' => $category->isShared(),
                'restaurant_ids' => $category->restaurant_ids,
                'restaurant_id' => $category->restaurant_id,
                'can_be_used' => $category->canBeUsedByRestaurant($restaurant->id)
            ]);
            
            // If this is a shared category, remove the restaurant from it
            if ($category->isShared() && $category->canBeUsedByRestaurant($restaurant->id)) {
                $category->removeRestaurant($restaurant->id);
                $message = 'Category "' . $category->name . '" has been removed from your restaurant.';
                \Log::info('Removed restaurant from shared category', [
                    'category_id' => $category->id,
                    'restaurant_id' => $restaurant->id
                ]);
            } else {
                // If this is a restaurant-specific category, delete it entirely
                $category->delete();
                $message = 'Category deleted successfully!';
                \Log::info('Deleted restaurant-specific category', [
                    'category_id' => $category->id
                ]);
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => $message
                ]);
            }
            
            return redirect()->route('restaurant.menu', $slug)->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Error deleting category: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error deleting category: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('restaurant.menu', $slug)->with('error', 'Error deleting category: ' . $e->getMessage());
        }
    }

    public function shareCategory(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access to restaurant categories. You need manager privileges.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Please login to access the restaurant categories.'], 401);
        }
        
        try {
            \Log::info('Share category request', [
                'request_data' => $request->all(),
                'category_id' => $request->input('category_id'),
                'restaurant_id' => $restaurant->id
            ]);
            
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
            ]);
            
            $category = Category::find($validated['category_id']);
            
            if (!$category) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Category not found.'
                ], 404);
            }
            
            // Check if restaurant can already use this category
            if ($category->canBeUsedByRestaurant($restaurant->id)) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Category "' . $category->name . '" is already available for your restaurant.',
                    'category' => $category
                ]);
            }
            
            // Add restaurant to the shared category
            $category->addRestaurant($restaurant->id);
            
            return response()->json([
                'success' => true, 
                'message' => 'Category "' . $category->name . '" is now shared with your restaurant!',
                'category' => $category
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error sharing category: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error sharing category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function unshareCategory(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access to restaurant categories. You need manager privileges.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Please login to access the restaurant categories.'], 401);
        }
        
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
            ]);
            
            $category = Category::find($validated['category_id']);
            
            if (!$category) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Category not found.'
                ], 404);
            }
            
            // Check if category has menu items for this restaurant
            $menuItemsCount = $category->menuItems()->where('restaurant_id', $restaurant->id)->count();
            if ($menuItemsCount > 0) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Cannot remove category that has menu items. Please move or delete the menu items first.'
                ], 422);
            }
            
            // Remove restaurant from the shared category
            $category->removeRestaurant($restaurant->id);
            
            return response()->json([
                'success' => true, 
                'message' => 'Category "' . $category->name . '" has been removed from your restaurant.',
                'category' => $category
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error unsharing category: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error removing category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deactivateCategory(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access to restaurant categories. You need manager privileges.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Please login to access the restaurant categories.'], 401);
        }
        
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
            ]);
            
            $category = Category::find($validated['category_id']);
            
            if (!$category) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Category not found.'
                ], 404);
            }
            
            // Check if category has menu items for this restaurant
            $menuItemsCount = $category->menuItems()->where('restaurant_id', $restaurant->id)->count();
            if ($menuItemsCount > 0) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Cannot deactivate category that has menu items. Please move or delete the menu items first.'
                ], 422);
            }
            
            \Log::info('Deactivating category', [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'restaurant_id' => $restaurant->id,
                'restaurant_ids_before' => $category->restaurant_ids
            ]);
            
            // Remove restaurant from the shared category (same as unshare but different messaging)
            $category->removeRestaurant($restaurant->id);
            
            \Log::info('Category deactivated', [
                'category_id' => $category->id,
                'restaurant_ids_after' => $category->restaurant_ids
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Category "' . $category->name . '" has been removed from your restaurant menu.',
                'category' => $category
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error deactivating category: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error deactivating category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSubcategories(Request $request, $slug, $parentId)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access to restaurant categories.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Please login to access the restaurant categories.'], 401);
        }
        
        try {
            // Get subcategories for the parent category
            $subcategories = Category::where('parent_id', $parentId)
                ->where('type', 'sub')
                ->with(['menuItems' => function($query) use ($restaurant) {
                    $query->where('restaurant_id', $restaurant->id);
                }])
                ->get()
                ->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'restaurant_count' => $category->isShared() ? count($category->restaurant_ids ?? []) : 1,
                        'is_shared' => $category->isShared(),
                        'menu_items_count' => $category->menuItems->count()
                    ];
                });
            
            return response()->json([
                'success' => true,
                'subcategories' => $subcategories
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting subcategories: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error loading subcategories: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleMenuItemStatus(Request $request, $slug, $item)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access to restaurant menu items.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Please login to access the restaurant menu items.'], 401);
        }
        
        try {
            $validated = $request->validate([
                'is_available' => 'required|boolean',
            ]);
            
            $menuItem = MenuItem::where('id', $item)
                ->where('restaurant_id', $restaurant->id)
                ->firstOrFail();
            
            $menuItem->update([
                'is_available' => $validated['is_available']
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Menu item status updated successfully.',
                'menu_item' => $menuItem
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error toggling menu item status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error updating menu item status: ' . $e->getMessage()
            ], 500);
        }
    }
}
