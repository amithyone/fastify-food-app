<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "🔍 Testing Order Authorization Logic\n\n";

// Get the order and user
$order = Order::find(20);
$user = User::find(4);

if (!$order) {
    echo "❌ Order 20 not found\n";
    exit;
}

if (!$user) {
    echo "❌ User 4 not found\n";
    exit;
}

echo "📊 Order Details:\n";
echo "  - Order ID: {$order->id}\n";
echo "  - Order User ID: {$order->user_id}\n";
echo "  - Restaurant ID: {$order->restaurant_id}\n";
echo "  - Customer Name: {$order->customer_name}\n\n";

echo "👤 User Details:\n";
echo "  - User ID: {$user->id}\n";
echo "  - User Name: {$user->name}\n";
echo "  - User Role: {$user->role}\n";
echo "  - Is Admin: " . ($user->isAdmin() ? 'Yes' : 'No') . "\n";
echo "  - Is Restaurant Owner: " . ($user->isRestaurantOwner() ? 'Yes' : 'No') . "\n\n";

// Test authorization logic
echo "🔐 Authorization Tests:\n";

// Test 1: User owns order
$userOwnsOrder = $order->user_id === $user->id;
echo "  1. User owns order: " . ($userOwnsOrder ? '✅ Yes' : '❌ No') . "\n";

// Test 2: User is admin
$userIsAdmin = $user->isAdmin();
echo "  2. User is admin: " . ($userIsAdmin ? '✅ Yes' : '❌ No') . "\n";

// Test 3: User is restaurant manager for this restaurant
$userIsManager = $user->isRestaurantOwner();
$primaryRestaurant = $user->primaryRestaurant;
$managesThisRestaurant = $userIsManager && $primaryRestaurant && $order->restaurant_id === $primaryRestaurant->id;
echo "  3. User manages this restaurant: " . ($managesThisRestaurant ? '✅ Yes' : '❌ No') . "\n";

// Test 4: Guest order
$isGuestOrder = $order->user_id === null;
echo "  4. Is guest order: " . ($isGuestOrder ? '✅ Yes' : '❌ No') . "\n";

// Final authorization result
$canAccess = $userOwnsOrder || $userIsAdmin || $managesThisRestaurant || $isGuestOrder;
echo "\n🎯 Final Result: " . ($canAccess ? '✅ CAN ACCESS' : '❌ CANNOT ACCESS') . "\n";

if ($canAccess) {
    echo "✅ Authorization should work!\n";
} else {
    echo "❌ Authorization should fail!\n";
}

echo "\n📋 Debug Info:\n";
echo "  - User ID: {$user->id}\n";
echo "  - Order User ID: {$order->user_id}\n";
echo "  - IDs Match: " . ($user->id === $order->user_id ? 'Yes' : 'No') . "\n";
echo "  - Primary Restaurant: " . ($primaryRestaurant ? $primaryRestaurant->name : 'None') . "\n";
echo "  - Order Restaurant ID: {$order->restaurant_id}\n"; 