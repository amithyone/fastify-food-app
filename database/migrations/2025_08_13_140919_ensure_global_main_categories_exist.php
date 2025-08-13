<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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

        echo "Global main categories ensured successfully!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't delete global categories as they might be referenced by restaurants
        echo "Global main categories migration cannot be reversed safely.\n";
    }
};
