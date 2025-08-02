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
        'logo' => $restaurant->logo ? \Storage::url($restaurant->logo) : null,
        'banner' => $restaurant->banner ? \Storage::url($restaurant->banner) : null,
        'menu_items_count' => $restaurant->menuItems()->count(),
        'currency' => $restaurant->currency,
        'average_rating' => $restaurant->average_rating,
        'ratings_count' => $restaurant->ratings()->count(),
    ]);
}); 