<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Category;

echo "=== Global Main Categories Check and Fix ===\n\n";

// Check current global main categories
echo "Current global main categories:\n";
$globalCategories = Category::where('type', 'main')->whereNull('restaurant_id')->orderBy('sort_order')->get();
foreach ($globalCategories as $category) {
    echo "- {$category->name} (ID: {$category->id}, Sort: {$category->sort_order})\n";
}

echo "\n";

// Define the required global main categories
$requiredCategories = [
    ['name' => 'Breakfast', 'slug' => 'breakfast', 'type' => 'main', 'sort_order' => 1, 'is_active' => true],
    ['name' => 'Lunch', 'slug' => 'lunch', 'type' => 'main', 'sort_order' => 2, 'is_active' => true],
    ['name' => 'Dinner', 'slug' => 'dinner', 'type' => 'main', 'sort_order' => 3, 'is_active' => true],
    ['name' => 'Drinks', 'slug' => 'drinks', 'type' => 'main', 'sort_order' => 4, 'is_active' => true],
    ['name' => 'Intercontinental', 'slug' => 'intercontinental', 'type' => 'main', 'sort_order' => 5, 'is_active' => true],
    ['name' => 'Starters', 'slug' => 'starters', 'type' => 'main', 'sort_order' => 6, 'is_active' => true],
    ['name' => 'Desserts', 'slug' => 'desserts', 'type' => 'main', 'sort_order' => 7, 'is_active' => true],
    ['name' => 'Pastries', 'slug' => 'pastries', 'type' => 'main', 'sort_order' => 8, 'is_active' => true]
];

echo "Ensuring all required global main categories exist...\n";

foreach ($requiredCategories as $categoryData) {
    $category = Category::updateOrCreate(
        ['slug' => $categoryData['slug']],
        $categoryData
    );
    
    if ($category->wasRecentlyCreated) {
        echo "✓ Created: {$category->name}\n";
    } else {
        echo "✓ Exists: {$category->name}\n";
    }
}

echo "\nFinal check:\n";
$finalCategories = Category::where('type', 'main')->whereNull('restaurant_id')->orderBy('sort_order')->get();
foreach ($finalCategories as $category) {
    echo "- {$category->name} (ID: {$category->id}, Sort: {$category->sort_order})\n";
}

echo "\n=== Global Main Categories Check Complete ===\n";
echo "Total global main categories: " . $finalCategories->count() . "\n";
