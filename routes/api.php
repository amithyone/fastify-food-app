<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Restaurant details API
Route::get('/restaurants/{id}/details', function ($id) {
    try {
        $restaurant = \App\Models\Restaurant::findOrFail($id);
        
        return response()->json([
            'id' => $restaurant->id,
            'name' => $restaurant->name,
            'description' => $restaurant->description,
            'cuisine_type' => $restaurant->cuisine_type,
            'address' => $restaurant->address,
            'city' => $restaurant->city,
            'state' => $restaurant->state,
            'whatsapp_number' => $restaurant->whatsapp_number,
            'phone_number' => $restaurant->phone_number,
            'email' => $restaurant->email,
            'opening_hours' => $restaurant->opening_hours,
            'logo' => $restaurant->logo_url,
            'banner' => $restaurant->banner_url,
            'menu_items_count' => $restaurant->menuItems()->count(),
            'currency' => $restaurant->currency,
            'average_rating' => $restaurant->average_rating,
            'ratings_count' => $restaurant->ratings()->count(),
            'slug' => $restaurant->slug,
            'full_address' => $restaurant->full_address,
            'display_name' => $restaurant->display_name,
        ]);
    } catch (\Exception $e) {
        \Log::error('Restaurant details API error', [
            'restaurant_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => 'Failed to load restaurant details',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Featured Restaurants API
Route::get('/featured-restaurants/{id}', function ($id) {
    try {
        $featured = \App\Models\FeaturedRestaurant::findOrFail($id);
        
        return response()->json([
            'id' => $featured->id,
            'restaurant_id' => $featured->restaurant_id,
            'title' => $featured->title,
            'description' => $featured->description,
            'ad_image' => $featured->ad_image,
            'cta_text' => $featured->cta_text,
            'cta_link' => $featured->cta_link,
            'badge_text' => $featured->badge_text,
            'badge_color' => $featured->badge_color,
            'sort_order' => $featured->sort_order,
            'is_active' => $featured->is_active,
            'featured_from' => $featured->featured_from,
            'featured_until' => $featured->featured_until,
            'click_count' => $featured->click_count,
            'impression_count' => $featured->impression_count,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to load promotion details',
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::post('/featured-restaurants', function (Request $request) {
    try {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'ad_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cta_text' => 'nullable|string|max:100',
            'cta_link' => 'nullable|url',
            'badge_text' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'featured_from' => 'nullable|date',
            'featured_until' => 'nullable|date|after:featured_from',
        ]);

        $data = $request->except('ad_image');
        
        // Handle image upload
        if ($request->hasFile('ad_image')) {
            $imagePath = $request->file('ad_image')->store('featured-restaurants', 'public');
            $data['ad_image'] = $imagePath;
        }

        $featured = \App\Models\FeaturedRestaurant::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Promotion created successfully',
            'data' => $featured
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create promotion: ' . $e->getMessage()
        ], 500);
    }
});

Route::put('/featured-restaurants/{id}', function (Request $request, $id) {
    try {
        $featured = \App\Models\FeaturedRestaurant::findOrFail($id);
        
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'ad_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cta_text' => 'nullable|string|max:100',
            'cta_link' => 'nullable|url',
            'badge_text' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
            'featured_from' => 'nullable|date',
            'featured_until' => 'nullable|date|after:featured_from',
        ]);

        $data = $request->except('ad_image');
        
        // Handle image upload
        if ($request->hasFile('ad_image')) {
            // Delete old image if exists
            if ($featured->ad_image) {
                \Storage::disk('public')->delete($featured->ad_image);
            }
            $imagePath = $request->file('ad_image')->store('featured-restaurants', 'public');
            $data['ad_image'] = $imagePath;
        }

        $featured->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Promotion updated successfully',
            'data' => $featured
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update promotion: ' . $e->getMessage()
        ], 500);
    }
});

Route::post('/featured-restaurants/{id}/click', function ($id) {
    try {
        $featured = \App\Models\FeaturedRestaurant::findOrFail($id);
        $featured->incrementClick();
        
        return response()->json([
            'success' => true,
            'message' => 'Click tracked successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to track click: ' . $e->getMessage()
        ], 500);
    }
}); 