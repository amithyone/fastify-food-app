<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

echo "🔧 Testing bank transfer with order...\n\n";

try {
    // Get the first user and authenticate
    $user = User::first();
    Auth::login($user);
    echo "✅ Authenticated as: " . Auth::user()->name . " (ID: " . Auth::id() . ")\n";
    
    // Get the first order
    $order = Order::first();
    if (!$order) {
        echo "❌ No orders found in database\n";
        exit(1);
    }
    
    echo "✅ Found order: {$order->order_number} (ID: {$order->id}, Amount: ₦{$order->total_amount})\n";
    
    // Test the bank transfer controller
    $controller = new \App\Http\Controllers\BankTransferPaymentController(app(\App\Services\PayVibeService::class));
    
    // Create a mock request
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'order_id' => $order->id,
        'amount' => $order->total_amount
    ]);
    
    echo "🔧 Testing bank transfer initialization...\n";
    
    try {
        $response = $controller->initialize($request);
        echo "✅ Bank transfer initialization successful!\n";
        echo "Response: " . json_encode($response->getData(), JSON_PRETTY_PRINT) . "\n";
    } catch (Exception $e) {
        echo "❌ Bank transfer initialization failed: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Test completed!\n"; 