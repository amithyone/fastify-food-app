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
        // Get all parent category names
        $parentCategoryNames = Category::where('type', 'main')
            ->whereNull('restaurant_id')
            ->pluck('name')
            ->map(function($name) {
                return strtolower($name);
            })
            ->toArray();

        // Fix restaurant categories that have the same name as parent categories
        // These should be main categories, not sub-categories
        Category::whereNotNull('restaurant_id')
            ->whereIn('type', ['sub'])
            ->get()
            ->each(function($category) use ($parentCategoryNames) {
                $categoryName = strtolower($category->name);
                
                // If the category name exactly matches a parent category name, make it a main category
                if (in_array($categoryName, $parentCategoryNames)) {
                    $category->update([
                        'parent_id' => null,
                        'type' => 'main'
                    ]);
                    
                    echo "Fixed category: {$category->name} (ID: {$category->id}) - Made it a main category\n";
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration fixes data, so the down method doesn't need to do anything
        // as we don't want to revert the fix
    }
};
