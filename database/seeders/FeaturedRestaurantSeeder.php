<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FeaturedRestaurant;
use App\Models\Restaurant;
use Carbon\Carbon;

class FeaturedRestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = Restaurant::where('is_active', true)->take(3)->get();

        if ($restaurants->count() > 0) {
            // Featured Restaurant 1
            FeaturedRestaurant::create([
                'restaurant_id' => $restaurants[0]->id,
                'title' => 'Try Our New Menu!',
                'description' => 'Discover amazing flavors with our latest additions. Limited time offer!',
                'cta_text' => 'Order Now',
                'badge_text' => 'New',
                'badge_color' => 'green',
                'sort_order' => 1,
                'is_active' => true,
                'featured_from' => Carbon::now(),
                'featured_until' => Carbon::now()->addDays(30),
            ]);

            if ($restaurants->count() > 1) {
                // Featured Restaurant 2
                FeaturedRestaurant::create([
                    'restaurant_id' => $restaurants[1]->id,
                    'title' => 'Popular Choice',
                    'description' => 'Our most loved dishes are waiting for you!',
                    'cta_text' => 'View Menu',
                    'badge_text' => 'Popular',
                    'badge_color' => 'orange',
                    'sort_order' => 2,
                    'is_active' => true,
                    'featured_from' => Carbon::now(),
                    'featured_until' => Carbon::now()->addDays(45),
                ]);
            }

            if ($restaurants->count() > 2) {
                // Featured Restaurant 3
                FeaturedRestaurant::create([
                    'restaurant_id' => $restaurants[2]->id,
                    'title' => 'Special Offer',
                    'description' => 'Get 20% off on your first order!',
                    'cta_text' => 'Get Discount',
                    'badge_text' => 'Limited Time',
                    'badge_color' => 'red',
                    'sort_order' => 3,
                    'is_active' => true,
                    'featured_from' => Carbon::now(),
                    'featured_until' => Carbon::now()->addDays(15),
                ]);
            }
        }
    }
}
