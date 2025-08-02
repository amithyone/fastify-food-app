<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index($slug = null)
    {
        if ($slug) {
            // Restaurant-specific menu
            $restaurant = Restaurant::where('slug', $slug)->where('is_active', true)->firstOrFail();
            $categories = $restaurant->categories()->with('menuItems')->where('is_active', true)->get();
            $menuItems = $restaurant->menuItems()->with('category')->where('is_available', true)->get();
            $stories = $restaurant->stories()->active()->ordered()->get();
            
            return view('menu.index', compact('categories', 'menuItems', 'stories', 'restaurant'));
        } else {
            // Default menu (for backward compatibility)
        $categories = Category::with('menuItems')->where('is_active', true)->get();
        $menuItems = MenuItem::with('category')->where('is_available', true)->get();
        $stories = Story::active()->ordered()->get();
        
        return view('menu.index', compact('categories', 'menuItems', 'stories'));
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
            'request_data' => $request->all()
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
            $validated['is_vegetarian'] = $request->has('is_vegetarian') || $request->input('is_vegetarian') === 'on';
            $validated['is_spicy'] = $request->has('is_spicy') || $request->input('is_spicy') === 'on';
            
            if ($request->hasFile('image')) {
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
            }
            
            $menuItem = MenuItem::create($validated);
            
            \Log::info('Menu item created successfully', [
                'menu_item_id' => $menuItem->id,
                'menu_item_name' => $menuItem->name
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
                abort(403, 'Unauthorized access to restaurant categories. You need manager privileges.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to access the restaurant categories.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $validated['restaurant_id'] = $restaurant->id;
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        
        Category::create($validated);
        
        return redirect()->route('restaurant.menu', $slug)->with('success', 'Category created successfully!');
    }

    public function updateCategory(Request $request, $slug, $category)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $category = Category::where('id', $category)->where('restaurant_id', $restaurant->id)->firstOrFail();
        
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant categories. You need manager privileges.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to access the restaurant categories.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        
        $category->update($validated);
        
        return redirect()->route('restaurant.menu', $slug)->with('success', 'Category updated successfully!');
    }

    public function destroyCategory(Request $request, $slug, $category)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $category = Category::where('id', $category)->where('restaurant_id', $restaurant->id)->firstOrFail();
        
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant categories. You need manager privileges.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to access the restaurant categories.');
        }
        
        // Check if category has menu items
        if ($category->menuItems()->count() > 0) {
            return redirect()->route('restaurant.menu', $slug)->with('error', 'Cannot delete category with menu items. Please move or delete the items first.');
        }
        
        $category->delete();
        
        return redirect()->route('restaurant.menu', $slug)->with('success', 'Category deleted successfully!');
    }
}
