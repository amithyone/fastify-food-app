<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Manager;

echo "🔍 Testing Status Update Endpoints\n\n";

// Test parameters
$orderId = 38; // ORD-20250816-X5J9ZG
$userId = 7; // Ossai Munachi Vincent

echo "📋 Test Parameters:\n";
echo "  - Order ID: {$orderId}\n";
echo "  - User ID: {$userId}\n\n";

// Get order and user
$order = Order::find($orderId);
$user = User::find($userId);

if (!$order) {
    echo "❌ Order not found\n";
    exit;
}

if (!$user) {
    echo "❌ User not found\n";
    exit;
}

echo "✅ Order found: {$order->order_number}\n";
echo "✅ User found: {$user->name}\n\n";

// Test authorization
$canAccess = Manager::canAccessRestaurant($user->id, $order->restaurant_id, 'manager');
echo "🔐 Authorization Test:\n";
echo "  - Can access restaurant: " . ($canAccess ? 'Yes' : 'No') . "\n\n";

if (!$canAccess) {
    echo "❌ User cannot access this restaurant\n";
    exit;
}

// Test status update data
$testData = [
    'status' => 'preparing',
    'status_note' => 'Test status update',
    'ready_time' => '15 minutes'
];

echo "📝 Test Data:\n";
foreach ($testData as $key => $value) {
    echo "  - {$key}: {$value}\n";
}
echo "\n";

// Test validation
echo "✅ Validation Test:\n";
$validator = \Illuminate\Support\Facades\Validator::make($testData, [
    'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled',
    'status_note' => 'nullable|string|max:500',
    'ready_time' => 'nullable|string|max:100'
]);

if ($validator->fails()) {
    echo "❌ Validation failed:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "  - {$error}\n";
    }
    exit;
}

echo "✅ Validation passed\n\n";

// Test order update
echo "🔄 Testing Order Update:\n";
$oldStatus = $order->status;
echo "  - Old status: {$oldStatus}\n";
echo "  - New status: {$testData['status']}\n";

try {
    // Prepare status note with time information
    $statusNote = $testData['status_note'];
    if ($testData['ready_time'] && in_array($testData['status'], ['preparing', 'ready'])) {
        $timeInfo = "Ready time: " . $testData['ready_time'];
        $statusNote = $statusNote ? $statusNote . " | " . $timeInfo : $timeInfo;
    }
    
    echo "  - Final status note: {$statusNote}\n";
    
    // Update order status
    $order->update([
        'status' => $testData['status'],
        'status_note' => $statusNote,
        'status_updated_at' => now(),
        'status_updated_by' => $user->id
    ]);
    
    echo "✅ Order updated successfully\n";
    
    // Test handleOrderStatusChange
    echo "🔄 Testing handleOrderStatusChange:\n";
    try {
        $order->restaurant->handleOrderStatusChange($order, $oldStatus, $testData['status']);
        echo "✅ handleOrderStatusChange completed\n";
    } catch (\Exception $e) {
        echo "⚠️ handleOrderStatusChange error: {$e->getMessage()}\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Order update failed: {$e->getMessage()}\n";
    exit;
}

echo "\n🎯 Status Update Test Completed Successfully!\n";
echo "The status update functionality should work correctly.\n";
