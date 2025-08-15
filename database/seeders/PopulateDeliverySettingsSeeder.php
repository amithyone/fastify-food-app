<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\RestaurantDeliverySetting;
use App\Models\MenuItem;

class PopulateDeliverySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting delivery settings population...');
        
        // Populate restaurant delivery settings
        $restaurants = Restaurant::whereDoesntHave('deliverySetting')->get();
        $this->command->info("📋 Found {$restaurants->count()} restaurants without delivery settings");
        
        foreach ($restaurants as $restaurant) {
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
            
            $this->command->info("✅ Created delivery settings for: {$restaurant->name}");
        }
        
        // Populate menu item delivery settings
        $menuItems = MenuItem::whereNull('is_available_for_delivery')
            ->orWhereNull('is_available_for_pickup')
            ->orWhereNull('is_available_for_restaurant')
            ->get();
        
        $this->command->info("🍽️  Found {$menuItems->count()} menu items needing delivery settings");
        
        foreach ($menuItems as $menuItem) {
            $menuItem->update([
                'is_available_for_delivery' => $menuItem->is_available_for_delivery ?? true,
                'is_available_for_pickup' => $menuItem->is_available_for_pickup ?? true,
                'is_available_for_restaurant' => $menuItem->is_available_for_restaurant ?? true,
            ]);
            
            $this->command->info("✅ Updated delivery settings for menu item: {$menuItem->name}");
        }
        
        // Summary
        $totalRestaurants = Restaurant::count();
        $restaurantsWithSettings = Restaurant::whereHas('deliverySetting')->count();
        $totalMenuItems = MenuItem::count();
        $menuItemsWithSettings = MenuItem::whereNotNull('is_available_for_delivery')
            ->whereNotNull('is_available_for_pickup')
            ->whereNotNull('is_available_for_restaurant')
            ->count();
        
        $this->command->info("\n📊 Delivery Settings Population Summary:");
        $this->command->info("   Restaurants: {$restaurantsWithSettings}/{$totalRestaurants} have delivery settings");
        $this->command->info("   Menu Items: {$menuItemsWithSettings}/{$totalMenuItems} have delivery settings");
        $this->command->info("\n✨ Delivery settings population completed successfully!");
    }
}
