<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleRestaurantSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample restaurant
        $restaurant = Restaurant::create([
            'name' => 'Taste of Abuja',
            'slug' => 'taste-of-abuja',
            'description' => 'Authentic Nigerian cuisine with a modern twist. Serving the best local and international dishes in Abuja.',
            'whatsapp_number' => '+234 801 234 5678',
            'phone_number' => '+234 801 234 5679',
            'email' => 'info@tasteofabuja.com',
            'address' => '123 Wuse Zone 2',
            'city' => 'Abuja',
            'state' => 'FCT',
            'postal_code' => '900001',
            'country' => 'Nigeria',
            'currency' => '₦',
            'theme_color' => '#ff6b35',
            'secondary_color' => '#f7931e',
            'is_active' => true,
            'is_verified' => true,
            'business_hours' => [
                'monday' => ['open' => '07:00', 'close' => '22:00'],
                'tuesday' => ['open' => '07:00', 'close' => '22:00'],
                'wednesday' => ['open' => '07:00', 'close' => '22:00'],
                'thursday' => ['open' => '07:00', 'close' => '22:00'],
                'friday' => ['open' => '07:00', 'close' => '23:00'],
                'saturday' => ['open' => '08:00', 'close' => '23:00'],
                'sunday' => ['open' => '08:00', 'close' => '21:00'],
            ],
            'settings' => [
                'delivery_enabled' => true,
                'dine_in_enabled' => true,
                'delivery_fee' => 500,
                'minimum_order' => 1000,
                'auto_accept_orders' => false,
            ],
        ]);

        // Create main categories
        $breakfast = Category::create([
            'name' => 'Breakfast',
            'slug' => 'breakfast',
            'type' => 'main',
            'sort_order' => 1,
            'is_active' => true,
            'restaurant_id' => $restaurant->id,
        ]);

        $lunch = Category::create([
            'name' => 'Lunch',
            'slug' => 'lunch',
            'type' => 'main',
            'sort_order' => 2,
            'is_active' => true,
            'restaurant_id' => $restaurant->id,
        ]);

        $dinner = Category::create([
            'name' => 'Dinner',
            'slug' => 'dinner',
            'type' => 'main',
            'sort_order' => 3,
            'is_active' => true,
            'restaurant_id' => $restaurant->id,
        ]);

        $drinks = Category::create([
            'name' => 'Drinks',
            'slug' => 'drinks',
            'type' => 'main',
            'sort_order' => 4,
            'is_active' => true,
            'restaurant_id' => $restaurant->id,
        ]);

        $desserts = Category::create([
            'name' => 'Desserts',
            'slug' => 'desserts',
            'type' => 'main',
            'sort_order' => 5,
            'is_active' => true,
            'restaurant_id' => $restaurant->id,
        ]);

        // Create sub-categories for Breakfast
        $breakfastSubs = [
            'Traditional Nigerian' => ['sort_order' => 1],
            'Continental' => ['sort_order' => 2],
            'Healthy Options' => ['sort_order' => 3],
        ];

        foreach ($breakfastSubs as $name => $data) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'type' => 'sub',
                'parent_id' => $breakfast->id,
                'sort_order' => $data['sort_order'],
                'is_active' => true,
                'restaurant_id' => $restaurant->id,
            ]);
        }

        // Create sub-categories for Lunch
        $lunchSubs = [
            'Main Dishes' => ['sort_order' => 1],
            'Soups & Stews' => ['sort_order' => 2],
            'Rice & Pasta' => ['sort_order' => 3],
            'Grilled Items' => ['sort_order' => 4],
        ];

        foreach ($lunchSubs as $name => $data) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'type' => 'sub',
                'parent_id' => $lunch->id,
                'sort_order' => $data['sort_order'],
                'is_active' => true,
                'restaurant_id' => $restaurant->id,
            ]);
        }

        // Create sub-categories for Dinner
        $dinnerSubs = [
            'Signature Dishes' => ['sort_order' => 1],
            'Seafood' => ['sort_order' => 2],
            'Meat & Poultry' => ['sort_order' => 3],
            'Vegetarian' => ['sort_order' => 4],
        ];

        foreach ($dinnerSubs as $name => $data) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'type' => 'sub',
                'parent_id' => $dinner->id,
                'sort_order' => $data['sort_order'],
                'is_active' => true,
                'restaurant_id' => $restaurant->id,
            ]);
        }

        // Create sub-categories for Drinks
        $drinksSubs = [
            'Hot Beverages' => ['sort_order' => 1],
            'Cold Beverages' => ['sort_order' => 2],
            'Smoothies & Juices' => ['sort_order' => 3],
            'Alcoholic Drinks' => ['sort_order' => 4],
        ];

        foreach ($drinksSubs as $name => $data) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'type' => 'sub',
                'parent_id' => $drinks->id,
                'sort_order' => $data['sort_order'],
                'is_active' => true,
                'restaurant_id' => $restaurant->id,
            ]);
        }

        // Get all categories for menu items
        $categories = Category::where('restaurant_id', $restaurant->id)->get();
        $breakfastSub = $categories->where('name', 'Traditional Nigerian')->first();
        $lunchMain = $categories->where('name', 'Main Dishes')->first();
        $dinnerSignature = $categories->where('name', 'Signature Dishes')->first();
        $drinksHot = $categories->where('name', 'Hot Beverages')->first();
        $dessertsMain = $desserts;

        // Create sample menu items
        $menuItems = [
            // Breakfast - Traditional Nigerian
            [
                'name' => 'Jollof Rice & Plantain',
                'description' => 'Spicy jollof rice served with fried plantain and grilled chicken',
                'price' => 2500, // in kobo
                'category_id' => $breakfastSub->id,
                'is_available' => true,
                'is_vegetarian' => false,
                'is_spicy' => true,
                'ingredients' => 'Rice, tomatoes, peppers, plantain, chicken, spices',
                'allergens' => 'Contains gluten',
            ],
            [
                'name' => 'Akara & Pap',
                'description' => 'Traditional bean cakes served with corn pap and honey',
                'price' => 1200,
                'category_id' => $breakfastSub->id,
                'is_available' => true,
                'is_vegetarian' => true,
                'is_spicy' => false,
                'ingredients' => 'Beans, onions, peppers, corn flour, honey',
                'allergens' => 'Contains legumes',
            ],
            [
                'name' => 'Yam & Egg Sauce',
                'description' => 'Boiled yam served with spicy egg sauce and vegetables',
                'price' => 1800,
                'category_id' => $breakfastSub->id,
                'is_available' => true,
                'is_vegetarian' => false,
                'is_spicy' => true,
                'ingredients' => 'Yam, eggs, tomatoes, peppers, onions',
                'allergens' => 'Contains eggs',
            ],

            // Lunch - Main Dishes
            [
                'name' => 'Efo Riro with Pounded Yam',
                'description' => 'Traditional spinach stew with pounded yam and assorted meat',
                'price' => 3200,
                'category_id' => $lunchMain->id,
                'is_available' => true,
                'is_vegetarian' => false,
                'is_spicy' => true,
                'ingredients' => 'Spinach, yam, meat, palm oil, peppers',
                'allergens' => 'Contains meat',
            ],
            [
                'name' => 'Amala & Ewedu',
                'description' => 'Yam flour paste with jute leaves soup and goat meat',
                'price' => 2800,
                'category_id' => $lunchMain->id,
                'is_available' => true,
                'is_vegetarian' => false,
                'is_spicy' => true,
                'ingredients' => 'Yam flour, jute leaves, goat meat, peppers',
                'allergens' => 'Contains meat',
            ],
            [
                'name' => 'Egusi Soup with Fufu',
                'description' => 'Ground melon seed soup with cassava fufu and fish',
                'price' => 3500,
                'category_id' => $lunchMain->id,
                'is_available' => true,
                'is_vegetarian' => false,
                'is_spicy' => false,
                'ingredients' => 'Melon seeds, cassava, fish, vegetables',
                'allergens' => 'Contains fish',
            ],

            // Dinner - Signature Dishes
            [
                'name' => 'Abuja Special Suya',
                'description' => 'Grilled beef suya with special spices and groundnut powder',
                'price' => 4200,
                'category_id' => $dinnerSignature->id,
                'is_available' => true,
                'is_vegetarian' => false,
                'is_spicy' => true,
                'ingredients' => 'Beef, groundnut powder, spices, onions',
                'allergens' => 'Contains nuts, meat',
            ],
            [
                'name' => 'Pepper Soup with Catfish',
                'description' => 'Spicy pepper soup with fresh catfish and vegetables',
                'price' => 3800,
                'category_id' => $dinnerSignature->id,
                'is_available' => true,
                'is_vegetarian' => false,
                'is_spicy' => true,
                'ingredients' => 'Catfish, peppers, spices, vegetables',
                'allergens' => 'Contains fish',
            ],
            [
                'name' => 'Nkwobi',
                'description' => 'Spicy cow foot with palm oil and utazi leaves',
                'price' => 4500,
                'category_id' => $dinnerSignature->id,
                'is_available' => true,
                'is_vegetarian' => false,
                'is_spicy' => true,
                'ingredients' => 'Cow foot, palm oil, utazi, spices',
                'allergens' => 'Contains meat',
            ],

            // Drinks - Hot Beverages
            [
                'name' => 'Nigerian Tea',
                'description' => 'Traditional Nigerian tea with milk and sugar',
                'price' => 300,
                'category_id' => $drinksHot->id,
                'is_available' => true,
                'is_vegetarian' => true,
                'is_spicy' => false,
                'ingredients' => 'Tea leaves, milk, sugar',
                'allergens' => 'Contains dairy',
            ],
            [
                'name' => 'Ginger Tea',
                'description' => 'Fresh ginger tea with honey and lemon',
                'price' => 400,
                'category_id' => $drinksHot->id,
                'is_available' => true,
                'is_vegetarian' => true,
                'is_spicy' => false,
                'ingredients' => 'Ginger, honey, lemon, hot water',
                'allergens' => 'None',
            ],
            [
                'name' => 'Coffee',
                'description' => 'Freshly brewed coffee with cream and sugar',
                'price' => 500,
                'category_id' => $drinksHot->id,
                'is_available' => true,
                'is_vegetarian' => true,
                'is_spicy' => false,
                'ingredients' => 'Coffee beans, cream, sugar',
                'allergens' => 'Contains dairy',
            ],

            // Desserts
            [
                'name' => 'Puff Puff',
                'description' => 'Traditional Nigerian doughnuts with sugar glaze',
                'price' => 200,
                'category_id' => $dessertsMain->id,
                'is_available' => true,
                'is_vegetarian' => true,
                'is_spicy' => false,
                'ingredients' => 'Flour, sugar, yeast, oil',
                'allergens' => 'Contains gluten',
            ],
            [
                'name' => 'Chin Chin',
                'description' => 'Crispy fried dough with vanilla and sugar',
                'price' => 300,
                'category_id' => $dessertsMain->id,
                'is_available' => true,
                'is_vegetarian' => true,
                'is_spicy' => false,
                'ingredients' => 'Flour, sugar, vanilla, oil',
                'allergens' => 'Contains gluten',
            ],
            [
                'name' => 'Boli & Groundnut',
                'description' => 'Roasted plantain with groundnut and coconut',
                'price' => 400,
                'category_id' => $dessertsMain->id,
                'is_available' => true,
                'is_vegetarian' => true,
                'is_spicy' => false,
                'ingredients' => 'Plantain, groundnut, coconut',
                'allergens' => 'Contains nuts',
            ],
        ];

        foreach ($menuItems as $item) {
            MenuItem::create(array_merge($item, [
                'restaurant_id' => $restaurant->id,
            ]));
        }

        // Create a restaurant owner user
        $restaurantOwner = User::create([
            'name' => 'Chef Adebayo',
            'email' => 'chef@tasteofabuja.com',
            'password' => Hash::make('password'),
            'phone_number' => '+234 801 234 5678',
        ]);

        // Update restaurant to set the owner
        $restaurant->update(['owner_id' => $restaurantOwner->id]);

        // Create an admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@abujaeat.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $this->command->info('Sample restaurant "Taste of Abuja" created successfully!');
        $this->command->info('Restaurant Owner: chef@tasteofabuja.com / password');
        $this->command->info('Admin User: admin@abujaeat.com / password');
        $this->command->info('Restaurant URL: http://127.0.0.1:8000/menu/taste-of-abuja');
        $this->command->info('Dashboard URL: http://127.0.0.1:8000/restaurant/taste-of-abuja/dashboard');
    }
} 