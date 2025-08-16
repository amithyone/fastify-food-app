<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Restaurant;
use App\Models\Category;

// Test 1: Create a new custom category
echo "=== Test 1: Creating a new custom category ===\n";

$restaurant = Restaurant::where('slug', 'mr-good-tastia')->first();
if (!$restaurant) {
    echo "Restaurant not found!\n";
    exit;
}

echo "Restaurant: {$restaurant->name} (ID: {$restaurant->id})\n";

// Simulate form data for custom category
$customCategoryData = [
    'name' => 'Test Custom Category',
    'parent_id' => 69, // Main Course
    'use_existing_category' => '0',
    'force_create' => '0'
];

echo "Form data: " . json_encode($customCategoryData) . "\n";

try {
    // Create the category directly
    $category = new Category();
    $category->name = $customCategoryData['name'];
    $category->parent_id = $customCategoryData['parent_id'];
    $category->restaurant_id = $restaurant->id;
    $category->type = 'sub';
    $category->save();
    
    echo "✅ Custom category created successfully! ID: {$category->id}\n";
    
    // Clean up - delete the test category
    $category->delete();
    echo "🧹 Test category cleaned up\n";
    
} catch (Exception $e) {
    echo "❌ Error creating custom category: " . $e->getMessage() . "\n";
}

echo "\n=== Test 2: Using existing category ===\n";

// Check for existing sub-categories
$existingSubCategories = Category::where('parent_id', 69)
    ->where('type', 'sub')
    ->get();

echo "Existing sub-categories for Main Course:\n";
foreach ($existingSubCategories as $sub) {
    echo "- {$sub->id}: {$sub->name} (Restaurant: {$sub->restaurant_id})\n";
}

if ($existingSubCategories->count() > 0) {
    $existingCategory = $existingSubCategories->first();
    
    $existingCategoryData = [
        'existing_category_id' => $existingCategory->id,
        'parent_id' => 69,
        'use_existing_category' => '1'
    ];
    
    echo "Form data: " . json_encode($existingCategoryData) . "\n";
    
    try {
        // Test the logic for using existing category
        $categoryToUse = Category::find($existingCategoryData['existing_category_id']);
        
        if ($categoryToUse && $categoryToUse->type === 'sub') {
            echo "✅ Existing category found and valid: {$categoryToUse->name}\n";
            
            // Check if already used by this restaurant
            $existingUsage = Category::where('restaurant_id', $restaurant->id)
                ->where('name', $categoryToUse->name)
                ->where('parent_id', $categoryToUse->parent_id)
                ->first();
                
            if ($existingUsage) {
                echo "ℹ️ Category already available for this restaurant\n";
            } else {
                echo "✅ Category can be shared with this restaurant\n";
            }
        } else {
            echo "❌ Invalid existing category\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error with existing category: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Test 3: Database structure check ===\n";

try {
    $tableInfo = DB::select("DESCRIBE categories");
    echo "Categories table structure:\n";
    foreach ($tableInfo as $column) {
        echo "- {$column->Field}: {$column->Type} " . ($column->Key === 'PRI' ? '(PRIMARY)' : '') . "\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking table structure: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
