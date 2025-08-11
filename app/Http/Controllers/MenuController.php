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
            $menuItems = $restaurant->menuItems()->with('category')->where('is_available', true)->get();
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
                $menuItems = $menuItems->merge($restaurant->menuItems()->with('category')->where('is_available', true)->get());
                $stories = $stories->merge($restaurant->stories()->active()->ordered()->get());
            }
            
            // Remove duplicates
            $categories = $categories->unique('id');
            $menuItems = $menuItems->unique('id');
            $stories = $stories->unique('id');
            
            return view('menu.index', compact('categories', 'menuItems', 'stories', 'restaurants', 'userLocation'));
        }
    }

    public function show($id)
    {
        $menuItem = MenuItem::with('category')->findOrFail($id);
        return view('menu.show', compact('menuItem'));
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        
        if ($query) {
            $menuItems = MenuItem::with('category')
                ->where('is_available', true)
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->get();
        } else {
            $menuItems = MenuItem::with('category')->where('is_available', true)->get();
        }
        
        return response()->json($menuItems);
    }

    public function getMenuItems(Request $request)
    {
        $categoryId = $request->get('category_id');
        
        if ($categoryId && $categoryId !== 'all') {
            $menuItems = MenuItem::with('category')
                ->where('category_id', $categoryId)
                ->where('is_available', true)
                ->get();
        } else {
            $menuItems = MenuItem::with('category')
                ->where('is_available', true)
                ->get();
        }
        
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
            $menuItems = MenuItem::with('category')
                ->where('category_id', $categoryId)
                ->where('is_available', true)
                ->get();
        } else {
            $menuItems = MenuItem::with('category')
                ->where('is_available', true)
                ->get();
        }
        
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
        
        $menuItems = $restaurant->menuItems()->with('category')->get();
        $categories = $restaurant->categories()->get();
        
        return view('restaurant.menu.index', compact('restaurant', 'menuItems', 'categories'));
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
                    // Copy the image to menu-items directory
                    $newFileName = 'menu-items/' . time() . '_' . uniqid() . '.' . pathinfo($restaurantImage->file_path, PATHINFO_EXTENSION);
                    $newPath = Storage::disk('public')->path($newFileName);
                    
                    // Create directory if it doesn't exist
                    $dir = dirname($newPath);
                    if (!file_exists($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    
                    // Copy the file
                    if (Storage::disk('public')->exists($restaurantImage->file_path)) {
                        Storage::disk('public')->copy($restaurantImage->file_path, $newFileName);
                        $validated['image'] = $newFileName;
                        
                        // Mark the image as used
                        $restaurantImage->markAsUsed();
                        
                        \Log::info('Existing image used for menu item', [
                            'restaurant_image_id' => $restaurantImage->id,
                            'new_image_path' => $newFileName,
                            'original_path' => $restaurantImage->file_path
                        ]);
                    } else {
                        \Log::error('Selected image file not found', [
                            'restaurant_image_id' => $restaurantImage->id,
                            'file_path' => $restaurantImage->file_path
                        ]);
                        return response()->json([
                            'success' => false,
                            'message' => 'Selected image file not found'
                        ], 400);
                    }
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
            $validated['is_vegetarian'] = $request->has('is_vegetarian') || $request->input('is_vegetarian') === 'on';
            $validated['is_spicy'] = $request->has('is_spicy') || $request->input('is_spicy') === 'on';
            
            // Set default category if none is provided
            if (empty($validated['category_id'])) {
                $firstCategory = Category::where('restaurant_id', $restaurant->id)->first();
                if ($firstCategory) {
                    $validated['category_id'] = $firstCategory->id;
                }
            }
            
            if ($request->hasFile('image')) {
                \Log::info('Image upload detected for update', [
                    'file_name' => $request->file('image')->getClientOriginalName(),
                    'file_size' => $request->file('image')->getSize(),
                    'file_mime' => $request->file('image')->getMimeType(),
                    'file_extension' => $request->file('image')->getClientOriginalExtension(),
                ]);
                
                try {
                    $validated['image'] = $request->file('image')->store('menu-items', 'public');
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
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'is_active' => 'boolean',
            ]);
            
            $validated['restaurant_id'] = $restaurant->id;
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
            $validated['is_active'] = $validated['is_active'] ?? true;
            
            $category = Category::create($validated);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Category created successfully!',
                    'category' => $category
                ]);
            }
            
            return redirect()->route('restaurant.menu', $slug)->with('success', 'Category created successfully!');
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
                'is_active' => 'boolean',
            ]);
            
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
            $validated['is_active'] = $validated['is_active'] ?? true;
            
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
            // Check if category has menu items
            if ($category->menuItems()->count() > 0) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Cannot delete category with menu items. Please move or delete the items first.'
                    ], 400);
                }
                return redirect()->route('restaurant.menu', $slug)->with('error', 'Cannot delete category with menu items. Please move or delete the items first.');
            }
            
            $category->delete();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Category deleted successfully!'
                ]);
            }
            
            return redirect()->route('restaurant.menu', $slug)->with('success', 'Category deleted successfully!');
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
}
