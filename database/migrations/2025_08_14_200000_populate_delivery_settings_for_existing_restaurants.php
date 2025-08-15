<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Restaurant;
use App\Models\RestaurantDeliverySetting;
use App\Models\MenuItem;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all restaurants that don't have delivery settings
        $restaurants = Restaurant::whereDoesntHave('deliverySetting')->get();
        
        foreach ($restaurants as $restaurant) {
            // Create default delivery settings for each restaurant
            RestaurantDeliverySetting::create([
                'restaurant_id' => $restaurant->id,
                'delivery_mode' => 'flexible',
                'delivery_enabled' => true,
                'pickup_enabled' => true,
                'in_restaurant_enabled' => true,
                'delivery_fee' => 500,
                'delivery_time_minutes' => 30,
                'pickup_time_minutes' => 20,
                'minimum_delivery_amount' => 0,
                'delivery_notes' => 'Free delivery for orders above ₦2000',
                'pickup_notes' => 'Please have your pickup code ready'
            ]);
        }
        
        // Update all existing menu items to have default delivery settings
        $menuItems = MenuItem::whereNull('is_available_for_delivery')
            ->orWhereNull('is_available_for_pickup')
            ->orWhereNull('is_available_for_restaurant')
            ->get();
        
        foreach ($menuItems as $menuItem) {
            $menuItem->update([
                'is_available_for_delivery' => $menuItem->is_available_for_delivery ?? true,
                'is_available_for_pickup' => $menuItem->is_available_for_pickup ?? true,
                'is_available_for_restaurant' => $menuItem->is_available_for_restaurant ?? true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration only adds data, so down() doesn't need to do anything
        // The data will remain even if migration is rolled back
    }
};
