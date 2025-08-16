<?php

// Bootstrap Laravel
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\MenuController;

echo "=== Test API Endpoint ===\n";

try {
    // Create a mock request for custom category
    $request = new Request();
    $request->merge([
        'name' => 'Test API Category',
        'parent_id' => 69,
        'use_existing_category' => '0',
        'force_create' => '0'
    ]);

    echo "Request data: " . json_encode($request->all()) . "\n";

    // Create controller instance with dependency injection
    $locationService = app(\App\Services\LocationService::class);
    $controller = new MenuController($locationService);
    
    // Test the storeCategory method
    $response = $controller->storeCategory($request, 'mr-good-tastia');
    
    echo "Response type: " . get_class($response) . "\n";
    
    if (method_exists($response, 'getData')) {
        $data = $response->getData();
        echo "Response data: " . json_encode($data) . "\n";
    } else {
        echo "Response: " . $response . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "=== Test Complete ===\n";
