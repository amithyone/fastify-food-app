<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Restaurant;
use App\Models\Category;

echo "=== Test Category Creation ===\n";

try {
    $restaurant = Restaurant::where('slug', 'mr-good-tastia')->first();
    if (!$restaurant) {
        echo "Restaurant not found!\n";
        exit;
    }

    echo "Restaurant: {$restaurant->name} (ID: {$restaurant->id})\n";

    // Test creating a category
    $category = new Category();
    $category->name = 'Test Category Local';
    $category->parent_id = 69;
    $category->restaurant_id = $restaurant->id;
    $category->type = 'sub';
    $category->save();

    echo "✅ Category created successfully! ID: {$category->id}\n";

    // Clean up
    $category->delete();
    echo "🧹 Test category cleaned up\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "=== Test Complete ===\n";
