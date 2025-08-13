<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

return new class extends Migration
{
    public function up(): void
    {
        // Get the global main categories
        $globalCategories = Category::where('type', 'main')->whereNull('restaurant_id')->get();
        
        // Create a mapping from old restaurant-specific parent IDs to new global parent IDs
        $parentMapping = [
            1 => $globalCategories->where('name', 'Breakfast')->first()->id ?? null,
            2 => $globalCategories->where('name', 'Lunch')->first()->id ?? null,
            3 => $globalCategories->where('name', 'Dinner')->first()->id ?? null,
            4 => $globalCategories->where('name', 'Drinks')->first()->id ?? null,
            5 => $globalCategories->where('name', 'Desserts')->first()->id ?? null,
        ];
        
        echo "Fixing restaurant category parent relationships...\n";
        
        // Update restaurant sub-categories to point to correct global parents
        foreach ($parentMapping as $oldParentId => $newParentId) {
            if ($newParentId) {
                $updatedCount = Category::where('parent_id', $oldParentId)
                    ->whereNotNull('restaurant_id')
                    ->update(['parent_id' => $newParentId]);
                
                echo "Updated {$updatedCount} categories from parent ID {$oldParentId} to {$newParentId}\n";
            }
        }
        
        // Delete the old restaurant-specific parent categories (IDs 1-5)
        $oldParentCategories = Category::whereIn('id', [1, 2, 3, 4, 5])
            ->where('type', 'main')
            ->whereNotNull('restaurant_id');
        
        $deletedCount = $oldParentCategories->count();
        $oldParentCategories->delete();
        
        echo "Deleted {$deletedCount} old restaurant-specific parent categories\n";
        echo "Restaurant category parent relationships fixed successfully!\n";
    }

    public function down(): void
    {
        echo "This migration cannot be safely reversed.\n";
    }
};
