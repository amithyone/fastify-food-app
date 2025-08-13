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
                'is_active' => true
            ],
            [
                'name' => 'Lunch',
                'slug' => 'lunch',
                'type' => 'main',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Dinner',
                'slug' => 'dinner',
                'type' => 'main',
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Drinks',
                'slug' => 'drinks',
                'type' => 'main',
                'sort_order' => 4,
                'is_active' => true
            ],
            [
                'name' => 'Intercontinental',
                'slug' => 'intercontinental',
                'type' => 'main',
                'sort_order' => 5,
                'is_active' => true
            ],
            [
                'name' => 'Starters',
                'slug' => 'starters',
                'type' => 'main',
                'sort_order' => 6,
                'is_active' => true
            ],
            [
                'name' => 'Desserts',
                'slug' => 'desserts',
                'type' => 'main',
                'sort_order' => 7,
                'is_active' => true
            ],
            [
                'name' => 'Pastries',
                'slug' => 'pastries',
                'type' => 'main',
                'sort_order' => 8,
                'is_active' => true
            ]
        ];

        foreach ($parentCategories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        $this->command->info('Parent categories seeded successfully!');
    }
}
