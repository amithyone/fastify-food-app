<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request, $restaurantId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $restaurant = Restaurant::findOrFail($restaurantId);
        
        // Check if user has already rated this restaurant
        $existingRating = RestaurantRating::where('user_id', Auth::id())
            ->where('restaurant_id', $restaurantId)
            ->first();

        if ($existingRating) {
            // Update existing rating
            $existingRating->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Rating updated successfully',
                'rating' => $existingRating->fresh(),
                'average_rating' => $restaurant->fresh()->average_rating,
            ]);
        }

        // Create new rating
        $rating = RestaurantRating::create([
            'user_id' => Auth::id(),
            'restaurant_id' => $restaurantId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully',
            'rating' => $rating,
            'average_rating' => $restaurant->fresh()->average_rating,
        ]);
    }

    public function getUserRating($restaurantId)
    {
        $rating = RestaurantRating::where('user_id', Auth::id())
            ->where('restaurant_id', $restaurantId)
            ->first();

        return response()->json([
            'rating' => $rating,
        ]);
    }

    public function getRestaurantRatings($restaurantId)
    {
        $ratings = RestaurantRating::with('user')
            ->where('restaurant_id', $restaurantId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'ratings' => $ratings,
        ]);
    }
}
