<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantDeliverySetting;
use App\Models\MenuItem;
use App\Models\MenuItemDeliveryMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RestaurantDeliverySettingController extends Controller
{
    /**
     * Show delivery settings for a restaurant
     */
    public function index($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check if user can manage this restaurant
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $deliverySetting = $restaurant->deliverySetting;
        
        // If no delivery setting exists, create a default one
        if (!$deliverySetting) {
            $deliverySetting = RestaurantDeliverySetting::create([
                'restaurant_id' => $restaurant->id,
                'delivery_mode' => 'flexible',
                'delivery_enabled' => true,
                'pickup_enabled' => true,
                'in_restaurant_enabled' => true,
                'delivery_fee' => 500,
                'delivery_time_minutes' => 30,
                'pickup_time_minutes' => 20,
                'minimum_delivery_amount' => 0
            ]);
        }

        $menuItems = $restaurant->menuItems()->with(['deliveryMethods', 'restaurant'])->get();

        return view('restaurant.delivery-settings.index', compact('restaurant', 'deliverySetting', 'menuItems'));
    }

    /**
     * Update delivery settings
     */
    public function update(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check if user can manage this restaurant
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'delivery_mode' => 'required|in:flexible,fixed',
            'delivery_enabled' => 'boolean',
            'pickup_enabled' => 'boolean',
            'in_restaurant_enabled' => 'boolean',
            'delivery_fee' => 'required|numeric|min:0',
            'minimum_delivery_amount' => 'required|numeric|min:0',
            'delivery_time_minutes' => 'required|integer|min:10',
            'pickup_time_minutes' => 'required|integer|min:5',
            'delivery_notes' => 'nullable|string',
            'pickup_notes' => 'nullable|string'
        ]);

        $deliverySetting = $restaurant->deliverySetting;
        
        if (!$deliverySetting) {
            $deliverySetting = new RestaurantDeliverySetting();
            $deliverySetting->restaurant_id = $restaurant->id;
        }

        // Handle checkbox values properly
        $data = $request->all();
        
        // Ensure boolean fields are properly set
        $data['delivery_enabled'] = $request->has('delivery_enabled') || $request->input('delivery_enabled') === '1';
        $data['pickup_enabled'] = $request->has('pickup_enabled') || $request->input('pickup_enabled') === '1';
        $data['in_restaurant_enabled'] = $request->has('in_restaurant_enabled') || $request->input('in_restaurant_enabled') === '1';
        
        \Log::info('Delivery settings update', [
            'restaurant_id' => $restaurant->id,
            'request_data' => $request->all(),
            'processed_data' => $data
        ]);
        
        $deliverySetting->fill($data);
        $deliverySetting->save();

        return redirect()->route('restaurant.delivery-settings.index', $restaurant->slug)
            ->with('success', 'Delivery settings updated successfully.');
    }

    /**
     * Update menu item delivery methods
     */
    public function updateMenuItemDeliveryMethods(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check if user can manage this restaurant
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'menu_items' => 'required|array',
            'menu_items.*.id' => 'required|exists:menu_items,id',
            'menu_items.*.delivery_methods' => 'required|array',
            'menu_items.*.delivery_methods.*.method' => 'required|in:delivery,pickup,in_restaurant',
            'menu_items.*.delivery_methods.*.enabled' => 'boolean',
            'menu_items.*.delivery_methods.*.additional_fee' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->menu_items as $menuItemData) {
                $menuItem = MenuItem::find($menuItemData['id']);
                
                // Ensure this menu item belongs to the restaurant
                if ($menuItem->restaurant_id !== $restaurant->id) {
                    continue;
                }

                // Delete existing delivery methods for this menu item
                $menuItem->deliveryMethods()->delete();

                // Create new delivery methods
                foreach ($menuItemData['delivery_methods'] as $methodData) {
                    MenuItemDeliveryMethod::create([
                        'menu_item_id' => $menuItem->id,
                        'delivery_method' => $methodData['method'],
                        'enabled' => $methodData['enabled'] ?? false,
                        'additional_fee' => $methodData['additional_fee'] ?? 0,
                        'notes' => $methodData['notes'] ?? null
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Menu item delivery methods updated successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating menu item delivery methods: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get delivery settings for API
     */
    public function apiIndex($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $deliverySetting = $restaurant->deliverySetting;

        if (!$deliverySetting) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery settings not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $deliverySetting
        ]);
    }

    /**
     * Get menu item delivery methods for API
     */
    public function apiMenuItemDeliveryMethods($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $menuItems = $restaurant->menuItems()->with(['deliveryMethods', 'restaurant'])->get();

        return response()->json([
            'success' => true,
            'data' => $menuItems
        ]);
    }

    /**
     * Check if menu item is available for delivery method
     */
    public function checkMenuItemAvailability($slug, $menuItemId, $deliveryMethod)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $menuItem = $restaurant->menuItems()->findOrFail($menuItemId);
        
        $deliveryMethodRecord = $menuItem->deliveryMethods()
            ->where('delivery_method', $deliveryMethod)
            ->first();

        return response()->json([
            'success' => true,
            'available' => $deliveryMethodRecord ? $deliveryMethodRecord->enabled : false,
            'additional_fee' => $deliveryMethodRecord ? $deliveryMethodRecord->additional_fee : 0
        ]);
    }
}
