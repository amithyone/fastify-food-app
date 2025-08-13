<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class ParentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parentCategories = [
            [
                'name' => 'Breakfast',
                'slug' => 'breakfast',
                'type' => 'main',
                'sort_order' => 1,
                'is_active' => true,
                'restaurant_id' => null
            ],
            [
                'name' => 'Lunch',
                'slug' => 'lunch',
                'type' => 'main',
                'sort_order' => 2,
                'is_active' => true,
                'restaurant_id' => null
            ],
            [
                'name' => 'Dinner',
                'slug' => 'dinner',
                'type' => 'main',
                'sort_order' => 3,
                'is_active' => true,
                'restaurant_id' => null
            ],
            [
                'name' => 'Drinks',
                'slug' => 'drinks',
                'type' => 'main',
                'sort_order' => 4,
                'is_active' => true,
                'restaurant_id' => null
            ],
            [
                'name' => 'Intercontinental',
                'slug' => 'intercontinental',
                'type' => 'main',
                'sort_order' => 5,
                'is_active' => true,
                'restaurant_id' => null
            ],
            [
                'name' => 'Starters',
                'slug' => 'starters',
                'type' => 'main',
                'sort_order' => 6,
                'is_active' => true,
                'restaurant_id' => null
            ],
            [
                'name' => 'Desserts',
                'slug' => 'desserts',
                'type' => 'main',
                'sort_order' => 7,
                'is_active' => true,
                'restaurant_id' => null
            ],
            [
                'name' => 'Pastries',
                'slug' => 'pastries',
                'type' => 'main',
                'sort_order' => 8,
                'is_active' => true,
                'restaurant_id' => null
            ]
        ];

        foreach ($parentCategories as $categoryData) {
            // First, try to find existing category with this slug and null restaurant_id
            $existingCategory = Category::where('slug', $categoryData['slug'])
                                      ->whereNull('restaurant_id')
                                      ->first();
            
            if ($existingCategory) {
                // Update existing global category
                $existingCategory->update($categoryData);
            } else {
                // Create new global category
                Category::create($categoryData);
            }
        }

        $this->command->info('Parent categories seeded successfully!');
    }
}
