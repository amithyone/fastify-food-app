<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\MenuController;
use App\Models\Restaurant;
use App\Models\MenuItem;
use App\Models\Manager;
use App\Services\LocationService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Create a test request
$request = new Request();
$request->merge([
    '_token' => csrf_token(),
    '_method' => 'PUT',
    'name' => 'Test Menu Item Updated',
    'price' => '1500',
    'category_id' => '1',
    'description' => 'Updated description',
    'is_available' => 'on',
    'is_available_for_delivery' => '1',
    'is_available_for_pickup' => '1',
    'is_available_for_restaurant' => '1',
    'image_source' => 'existing',
    'selected_image_id' => '1',
    'selected_image_path' => 'test/path.jpg'
]);

// Get a restaurant and menu item
$restaurant = Restaurant::first();
$menuItem = MenuItem::where('restaurant_id', $restaurant->id)->first();

if (!$restaurant || !$menuItem) {
    echo "No restaurant or menu item found for testing\n";
    exit;
}

echo "Testing menu item update:\n";
echo "Restaurant: {$restaurant->name} (ID: {$restaurant->id})\n";
echo "Menu Item: {$menuItem->name} (ID: {$menuItem->id})\n";
echo "Request data: " . json_encode($request->all(), JSON_PRETTY_PRINT) . "\n\n";

// Create controller instance with dependency injection
$locationService = app(LocationService::class);
$controller = new MenuController($locationService);

// Test the update method
try {
    $result = $controller->restaurantUpdate($request, $restaurant->slug, $menuItem->id);
    echo "Update result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
