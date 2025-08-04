<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;

class RestaurantStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = Restaurant::all();
        
        foreach ($restaurants as $restaurant) {
            // Set default values for existing restaurants
            $restaurant->update([
                'is_open' => true, // Default to open
                'opening_time' => '08:00:00', // Default opening time
                'closing_time' => '22:00:00', // Default closing time
                'auto_open_close' => false, // Default to manual control
                'status_message' => null, // No custom message by default
                'weekly_schedule' => null // No weekly schedule by default
            ]);
        }
        
        $this->command->info('Restaurant status settings seeded successfully!');
    }
}
