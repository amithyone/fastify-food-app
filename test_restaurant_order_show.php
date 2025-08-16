<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Manager;

echo "🔍 Testing Restaurant Order Show Method\n\n";

// Simulate the restaurantOrderShow method
$slug = 'tagine-cuisine';
$orderId = 38; // ORD-20250816-X5J9ZG
$userId = 7; // Ossai Munachi Vincent

echo "📋 Test Parameters:\n";
echo "  - Slug: {$slug}\n";
echo "  - Order ID: {$orderId}\n";
echo "  - User ID: {$userId}\n\n";

// Step 1: Find restaurant
$restaurant = Restaurant::where('slug', $slug)->first();
if (!$restaurant) {
    echo "❌ Restaurant not found\n";
    exit;
}
echo "✅ Restaurant found: {$restaurant->name} (ID: {$restaurant->id})\n";

// Step 2: Find order
$order = Order::with(['orderItems.menuItem'])->find($orderId);
if (!$order) {
    echo "❌ Order not found\n";
    exit;
}
echo "✅ Order found: {$order->order_number} (ID: {$order->id})\n";

// Step 3: Find user
$user = User::find($userId);
if (!$user) {
    echo "❌ User not found\n";
    exit;
}
echo "✅ User found: {$user->name} (ID: {$user->id})\n\n";

// Step 4: Check authorization
echo "🔐 Authorization Check:\n";
$canAccess = Manager::canAccessRestaurant($user->id, $restaurant->id, 'manager');
$isAdmin = $user->isAdmin();

echo "  - Can access restaurant: " . ($canAccess ? 'Yes' : 'No') . "\n";
echo "  - Is admin: " . ($isAdmin ? 'Yes' : 'No') . "\n";

if (!$canAccess && !$isAdmin) {
    echo "❌ UNAUTHORIZED - Need manager privileges\n";
    exit;
}
echo "✅ AUTHORIZED\n\n";

// Step 5: Check if order belongs to restaurant
echo "🔍 Order-Restaurant Check:\n";
echo "  - Order restaurant_id: {$order->restaurant_id}\n";
echo "  - Restaurant id: {$restaurant->id}\n";
echo "  - Match: " . ($order->restaurant_id === $restaurant->id ? 'Yes' : 'No') . "\n";

if ($order->restaurant_id !== $restaurant->id) {
    echo "❌ Order does not belong to this restaurant\n";
    exit;
}
echo "✅ Order belongs to restaurant\n\n";

echo "🎯 RESULT: ACCESS GRANTED\n";
echo "The manager should be able to view this order.\n";

// Additional debugging
echo "\n📊 Additional Info:\n";
echo "  - Order customer: {$order->customer_name}\n";
echo "  - Order status: {$order->status}\n";
echo "  - Order total: {$order->total_amount}\n";
echo "  - Order items count: " . $order->orderItems->count() . "\n";
