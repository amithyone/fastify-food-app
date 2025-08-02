<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        // Check if user owns this restaurant
        if (Auth::check()) {
            if ($restaurant->owner_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant menu.');
            }
        } else {
            // Allow access for non-authenticated users (for demo purposes)
            // In production, you might want to restrict this
        }
        
        $menuItems = $restaurant->menuItems()->with('category')->get();
        $categories = $restaurant->categories()->get();
        
        return view('restaurant.menu.index', compact('restaurant', 'menuItems', 'categories'));
    }

    public function restaurantCreate($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check()) {
            if ($restaurant->owner_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant menu.');
            }
        } else {
            // Allow access for non-authenticated users (for demo purposes)
            // In production, you might want to restrict this
        }
        
        $categories = $restaurant->categories()->get();
        
        return view('restaurant.menu.create', compact('restaurant', 'categories'));
    }

    public function restaurantStore(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check()) {
            if ($restaurant->owner_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant menu.');
            }
        } else {
            // Allow access for non-authenticated users (for demo purposes)
            // In production, you might want to restrict this
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'is_vegetarian' => 'boolean',
            'is_spicy' => 'boolean',
            'ingredients' => 'nullable|string',
            'allergens' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $validated['restaurant_id'] = $restaurant->id;
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }
        
        MenuItem::create($validated);
        
        return redirect()->route('restaurant.menu', $slug)->with('success', 'Menu item created successfully!');
    }

    public function restaurantEdit($slug, $item)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $menuItem = MenuItem::where('id', $item)->where('restaurant_id', $restaurant->id)->firstOrFail();
        
        if (Auth::check()) {
            if ($restaurant->owner_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant menu.');
            }
        } else {
            // Allow access for non-authenticated users (for demo purposes)
            // In production, you might want to restrict this
        }
        
        $categories = $restaurant->categories()->get();
        
        return view('restaurant.menu.edit', compact('restaurant', 'menuItem', 'categories'));
    }

    public function restaurantUpdate(Request $request, $slug, $item)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $menuItem = MenuItem::where('id', $item)->where('restaurant_id', $restaurant->id)->firstOrFail();
        
        if (Auth::check()) {
            if ($restaurant->owner_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant menu.');
            }
        } else {
            // Allow access for non-authenticated users (for demo purposes)
            // In production, you might want to restrict this
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'is_vegetarian' => 'boolean',
            'is_spicy' => 'boolean',
            'ingredients' => 'nullable|string',
            'allergens' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }
        
        $menuItem->update($validated);
        
        return redirect()->route('restaurant.menu', $slug)->with('success', 'Menu item updated successfully!');
    }

    public function restaurantDestroy($slug, $item)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $menuItem = MenuItem::where('id', $item)->where('restaurant_id', $restaurant->id)->firstOrFail();
        
        if (Auth::check()) {
            if ($restaurant->owner_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant menu.');
            }
        } else {
            // Allow access for non-authenticated users (for demo purposes)
            // In production, you might want to restrict this
        }
        
        $menuItem->delete();
        
        return redirect()->route('restaurant.menu', $slug)->with('success', 'Menu item deleted successfully!');
    }
}
