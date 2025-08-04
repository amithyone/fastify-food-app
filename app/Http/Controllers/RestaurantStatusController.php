<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RestaurantStatusController extends Controller
{
    /**
     * Show restaurant status management page
     */
    public function index($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check if user is authorized to manage this restaurant
        if (!Auth::user()->can('manage', $restaurant)) {
            abort(403);
        }

        return view('restaurant.status.index', compact('restaurant'));
    }

    /**
     * Toggle restaurant open/close status
     */
    public function toggleStatus(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check if user is authorized to manage this restaurant
        if (!Auth::user()->can('manage', $restaurant)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $newStatus = $restaurant->toggleStatus();
            
            Log::info('Restaurant status toggled', [
                'restaurant_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name,
                'new_status' => $newStatus ? 'open' : 'closed',
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => $newStatus ? 'Restaurant is now open' : 'Restaurant is now closed',
                'status' => $newStatus ? 'open' : 'closed',
                'status_display' => $restaurant->status_display
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling restaurant status', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating restaurant status'
            ], 500);
        }
    }

    /**
     * Update restaurant business hours
     */
    public function updateBusinessHours(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check if user is authorized to manage this restaurant
        if (!Auth::user()->can('manage', $restaurant)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'auto_open_close' => 'boolean',
            'status_message' => 'nullable|string|max:500'
        ]);

        try {
            $restaurant->update([
                'opening_time' => $request->opening_time,
                'closing_time' => $request->closing_time,
                'auto_open_close' => $request->boolean('auto_open_close'),
                'status_message' => $request->status_message
            ]);

            Log::info('Restaurant business hours updated', [
                'restaurant_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name,
                'opening_time' => $request->opening_time,
                'closing_time' => $request->closing_time,
                'auto_open_close' => $request->boolean('auto_open_close'),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Business hours updated successfully',
                'restaurant' => [
                    'formatted_business_hours' => $restaurant->formatted_business_hours,
                    'status_message' => $restaurant->status_message,
                    'is_currently_open' => $restaurant->isCurrentlyOpen()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating restaurant business hours', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating business hours'
            ], 500);
        }
    }

    /**
     * Get restaurant status
     */
    public function getStatus($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => [
                'is_open' => $restaurant->is_open,
                'is_currently_open' => $restaurant->isCurrentlyOpen(),
                'status_display' => $restaurant->status_display,
                'status_message' => $restaurant->status_message,
                'opening_time' => $restaurant->opening_time?->format('H:i'),
                'closing_time' => $restaurant->closing_time?->format('H:i'),
                'formatted_business_hours' => $restaurant->formatted_business_hours,
                'auto_open_close' => $restaurant->auto_open_close
            ]
        ]);
    }

    /**
     * Set restaurant as open
     */
    public function open($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check if user is authorized to manage this restaurant
        if (!Auth::user()->can('manage', $restaurant)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $restaurant->open();
            
            Log::info('Restaurant opened', [
                'restaurant_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Restaurant is now open',
                'status' => 'open',
                'status_display' => $restaurant->status_display
            ]);
        } catch (\Exception $e) {
            Log::error('Error opening restaurant', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error opening restaurant'
            ], 500);
        }
    }

    /**
     * Set restaurant as closed
     */
    public function close($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check if user is authorized to manage this restaurant
        if (!Auth::user()->can('manage', $restaurant)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $restaurant->close();
            
            Log::info('Restaurant closed', [
                'restaurant_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Restaurant is now closed',
                'status' => 'closed',
                'status_display' => $restaurant->status_display
            ]);
        } catch (\Exception $e) {
            Log::error('Error closing restaurant', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error closing restaurant'
            ], 500);
        }
    }
}
