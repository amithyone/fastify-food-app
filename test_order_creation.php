<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Auth;

echo "🔧 Testing order creation...\n\n";

try {
    // Get the first user (restaurant owner)
    $user = User::first();
    if (!$user) {
        echo "❌ No users found in database\n";
        exit(1);
    }
    
    echo "✅ Found user: {$user->name} (ID: {$user->id})\n";
    
    // Authenticate as this user
    Auth::login($user);
    echo "✅ Authenticated as: " . Auth::user()->name . " (ID: " . Auth::id() . ")\n";
    
    // Get the first menu item
    $menuItem = MenuItem::first();
    if (!$menuItem) {
        echo "❌ No menu items found in database\n";
        exit(1);
    }
    
    echo "✅ Found menu item: {$menuItem->name} (ID: {$menuItem->id})\n";
    
    // Simulate order creation request
    $requestData = [
        'items' => [
            [
                'id' => $menuItem->id,
                'name' => $menuItem->name,
                'price' => $menuItem->price,
                'quantity' => 1
            ]
        ],
        'customer_info' => [
            'order_type' => 'restaurant',
            'in_restaurant' => true,
            'table_number' => '2',
            'restaurant_notes' => null
        ],
        'payment_method' => 'transfer',
        'subtotal' => $menuItem->price,
        'delivery_fee' => 0,
        'total' => $menuItem->price
    ];
    
    echo "✅ Order data prepared\n";
    
    // Test the controller method
    $controller = new \App\Http\Controllers\OrderController(app(\App\Services\PaymentCalculationService::class));
    
    // Create a mock request
    $request = new \Illuminate\Http\Request();
    $request->merge($requestData);
    
    echo "🔧 Testing order creation...\n";
    
    try {
        $response = $controller->store($request);
        echo "✅ Order creation successful!\n";
        echo "Response: " . json_encode($response->getData(), JSON_PRETTY_PRINT) . "\n";
    } catch (Exception $e) {
        echo "❌ Order creation failed: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Test completed!\n"; 