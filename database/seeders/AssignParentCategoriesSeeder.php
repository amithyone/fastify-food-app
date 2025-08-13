<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class AssignParentCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all parent categories
        $parentCategories = Category::where('type', 'main')->get()->keyBy('slug');
        
        // Get all restaurant categories that don't have a parent
        $restaurantCategories = Category::whereNotNull('restaurant_id')
            ->whereNull('parent_id')
            ->get();

        foreach ($restaurantCategories as $category) {
            // Try to match category name to parent category
            $parentCategory = $this->findMatchingParent($category->name, $parentCategories);
            
            if ($parentCategory) {
                $category->update([
                    'parent_id' => $parentCategory->id,
                    'type' => 'sub'
                ]);
                
                $this->command->info("Assigned '{$category->name}' to parent '{$parentCategory->name}'");
            } else {
                // Default to 'Lunch' if no match found
                $defaultParent = $parentCategories->get('lunch');
                if ($defaultParent) {
                    $category->update([
                        'parent_id' => $defaultParent->id,
                        'type' => 'sub'
                    ]);
                    
                    $this->command->info("Assigned '{$category->name}' to default parent 'Lunch'");
                }
            }
        }

        $this->command->info('Parent categories assigned successfully!');
    }

    private function findMatchingParent($categoryName, $parentCategories)
    {
        $categoryName = strtolower($categoryName);
        
        // Direct matches
        foreach ($parentCategories as $parent) {
            if (str_contains($categoryName, strtolower($parent->name))) {
                return $parent;
            }
        }
        
        // Keyword matches
        $keywordMap = [
            'breakfast' => ['breakfast', 'morning', 'eggs', 'pancake', 'waffle'],
            'lunch' => ['lunch', 'main', 'rice', 'pasta', 'chicken', 'beef', 'fish'],
            'dinner' => ['dinner', 'evening', 'supper'],
            'drinks' => ['drink', 'beverage', 'juice', 'soda', 'water', 'tea', 'coffee'],
            'intercontinental' => ['intercontinental', 'continental', 'western', 'american', 'italian', 'chinese'],
            'starters' => ['starter', 'appetizer', 'snack', 'small'],
            'desserts' => ['dessert', 'sweet', 'ice cream', 'cake'],
            'pastries' => ['pastry', 'bread', 'bun', 'doughnut']
        ];
        
        foreach ($keywordMap as $parentSlug => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($categoryName, $keyword)) {
                    return $parentCategories->get($parentSlug);
                }
            }
        }
        
        return null;
    }
}
