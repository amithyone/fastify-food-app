<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class GlobalCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Global main categories (restaurant_id = null means global)
        $globalMainCategories = [
            [
                'name' => 'Breakfast',
                'slug' => 'breakfast',
                'type' => 'main',
                'sort_order' => 1,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Lunch',
                'slug' => 'lunch',
                'type' => 'main',
                'sort_order' => 2,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Dinner',
                'slug' => 'dinner',
                'type' => 'main',
                'sort_order' => 3,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Appetizers',
                'slug' => 'appetizers',
                'type' => 'main',
                'sort_order' => 4,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Main Course',
                'slug' => 'main-course',
                'type' => 'main',
                'sort_order' => 5,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Soups & Stews',
                'slug' => 'soups-stews',
                'type' => 'main',
                'sort_order' => 6,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Rice & Pasta',
                'slug' => 'rice-pasta',
                'type' => 'main',
                'sort_order' => 7,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Grilled & BBQ',
                'slug' => 'grilled-bbq',
                'type' => 'main',
                'sort_order' => 8,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Seafood',
                'slug' => 'seafood',
                'type' => 'main',
                'sort_order' => 9,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Vegetarian',
                'slug' => 'vegetarian',
                'type' => 'main',
                'sort_order' => 10,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Desserts',
                'slug' => 'desserts',
                'type' => 'main',
                'sort_order' => 11,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Beverages',
                'slug' => 'beverages',
                'type' => 'main',
                'sort_order' => 12,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Hot Drinks',
                'slug' => 'hot-drinks',
                'type' => 'main',
                'sort_order' => 13,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Cold Drinks',
                'slug' => 'cold-drinks',
                'type' => 'main',
                'sort_order' => 14,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Smoothies & Juices',
                'slug' => 'smoothies-juices',
                'type' => 'main',
                'sort_order' => 15,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Alcoholic Drinks',
                'slug' => 'alcoholic-drinks',
                'type' => 'main',
                'sort_order' => 16,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Combo Deals',
                'slug' => 'combo-deals',
                'type' => 'main',
                'sort_order' => 17,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Kids Menu',
                'slug' => 'kids-menu',
                'type' => 'main',
                'sort_order' => 18,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Specials',
                'slug' => 'specials',
                'type' => 'main',
                'sort_order' => 19,
                'is_active' => true,
                'restaurant_id' => null,
            ],
        ];

        // Create global main categories
        foreach ($globalMainCategories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => $categoryData['slug'], 'restaurant_id' => null],
                $categoryData
            );
        }

        // Get the created main categories for sub-categories
        $breakfast = Category::where('slug', 'breakfast')->whereNull('restaurant_id')->first();
        $lunch = Category::where('slug', 'lunch')->whereNull('restaurant_id')->first();
        $dinner = Category::where('slug', 'dinner')->whereNull('restaurant_id')->first();
        $mainCourse = Category::where('slug', 'main-course')->whereNull('restaurant_id')->first();
        $beverages = Category::where('slug', 'beverages')->whereNull('restaurant_id')->first();

        // Global sub-categories
        $globalSubCategories = [
            // Breakfast sub-categories
            [
                'name' => 'Traditional Nigerian',
                'slug' => 'traditional-nigerian-breakfast',
                'type' => 'sub',
                'parent_id' => $breakfast->id,
                'sort_order' => 1,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Continental',
                'slug' => 'continental-breakfast',
                'type' => 'sub',
                'parent_id' => $breakfast->id,
                'sort_order' => 2,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Healthy Options',
                'slug' => 'healthy-breakfast',
                'type' => 'sub',
                'parent_id' => $breakfast->id,
                'sort_order' => 3,
                'is_active' => true,
                'restaurant_id' => null,
            ],

            // Lunch sub-categories
            [
                'name' => 'Quick Bites',
                'slug' => 'quick-bites-lunch',
                'type' => 'sub',
                'parent_id' => $lunch->id,
                'sort_order' => 1,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Business Lunch',
                'slug' => 'business-lunch',
                'type' => 'sub',
                'parent_id' => $lunch->id,
                'sort_order' => 2,
                'is_active' => true,
                'restaurant_id' => null,
            ],

            // Dinner sub-categories
            [
                'name' => 'Signature Dishes',
                'slug' => 'signature-dishes-dinner',
                'type' => 'sub',
                'parent_id' => $dinner->id,
                'sort_order' => 1,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Fine Dining',
                'slug' => 'fine-dining-dinner',
                'type' => 'sub',
                'parent_id' => $dinner->id,
                'sort_order' => 2,
                'is_active' => true,
                'restaurant_id' => null,
            ],

            // Main Course sub-categories
            [
                'name' => 'Chicken Dishes',
                'slug' => 'chicken-dishes',
                'type' => 'sub',
                'parent_id' => $mainCourse->id,
                'sort_order' => 1,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Beef Dishes',
                'slug' => 'beef-dishes',
                'type' => 'sub',
                'parent_id' => $mainCourse->id,
                'sort_order' => 2,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Fish Dishes',
                'slug' => 'fish-dishes',
                'type' => 'sub',
                'parent_id' => $mainCourse->id,
                'sort_order' => 3,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Vegetarian Main',
                'slug' => 'vegetarian-main',
                'type' => 'sub',
                'parent_id' => $mainCourse->id,
                'sort_order' => 4,
                'is_active' => true,
                'restaurant_id' => null,
            ],

            // Beverages sub-categories
            [
                'name' => 'Coffee',
                'slug' => 'coffee-beverages',
                'type' => 'sub',
                'parent_id' => $beverages->id,
                'sort_order' => 1,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Tea',
                'slug' => 'tea-beverages',
                'type' => 'sub',
                'parent_id' => $beverages->id,
                'sort_order' => 2,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Soft Drinks',
                'slug' => 'soft-drinks-beverages',
                'type' => 'sub',
                'parent_id' => $beverages->id,
                'sort_order' => 3,
                'is_active' => true,
                'restaurant_id' => null,
            ],
            [
                'name' => 'Fresh Juices',
                'slug' => 'fresh-juices-beverages',
                'type' => 'sub',
                'parent_id' => $beverages->id,
                'sort_order' => 4,
                'is_active' => true,
                'restaurant_id' => null,
            ],
        ];

        // Create global sub-categories
        foreach ($globalSubCategories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => $categoryData['slug'], 'restaurant_id' => null],
                $categoryData
            );
        }

        $this->command->info('Global categories seeded successfully!');
        $this->command->info('Created ' . count($globalMainCategories) . ' main categories and ' . count($globalSubCategories) . ' sub-categories.');
    }
}
