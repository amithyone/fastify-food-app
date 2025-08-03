<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\RestaurantDeliverySetting;

class RestaurantDeliverySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = Restaurant::all();
        
        foreach ($restaurants as $restaurant) {
            // Check if delivery setting already exists
            if (!$restaurant->deliverySetting) {
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
        }
        
        $this->command->info('Restaurant delivery settings seeded successfully!');
    }
}
