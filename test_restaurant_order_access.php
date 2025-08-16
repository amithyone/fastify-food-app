<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Manager;
use Illuminate\Support\Facades\Auth;

echo "🔍 Testing Restaurant Order Access for Tagine Restaurant\n\n";

// Get the restaurant and manager
$restaurant = Restaurant::where('slug', 'tagine-cuisine')->first();
$manager = User::find(7); // Ossai Munachi Vincent

if (!$restaurant) {
    echo "❌ Tagine restaurant not found\n";
    exit;
}

if (!$manager) {
    echo "❌ Manager not found\n";
    exit;
}

echo "🏪 Restaurant Details:\n";
echo "  - ID: {$restaurant->id}\n";
echo "  - Name: {$restaurant->name}\n";
echo "  - Slug: {$restaurant->slug}\n\n";

echo "👤 Manager Details:\n";
echo "  - ID: {$manager->id}\n";
echo "  - Name: {$manager->name}\n";
echo "  - Is Restaurant Owner: " . ($manager->isRestaurantOwner() ? 'Yes' : 'No') . "\n\n";

// Get manager record
$managerRecord = Manager::where('user_id', $manager->id)
    ->where('restaurant_id', $restaurant->id)
    ->first();

echo "📋 Manager Record:\n";
if ($managerRecord) {
    echo "  - Found: Yes\n";
    echo "  - Role: {$managerRecord->role}\n";
    echo "  - Active: " . ($managerRecord->is_active ? 'Yes' : 'No') . "\n";
} else {
    echo "  - Found: No\n";
}
echo "\n";

// Test manager access
$canAccessRestaurant = Manager::canAccessRestaurant($manager->id, $restaurant->id, 'manager');
echo "🔐 Manager Access Test:\n";
echo "  - Can access restaurant: " . ($canAccessRestaurant ? 'Yes' : 'No') . "\n\n";

// Get recent orders for the restaurant
$orders = Order::where('restaurant_id', $restaurant->id)
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

echo "📦 Recent Orders:\n";
foreach ($orders as $order) {
    echo "  - Order #{$order->id}: {$order->order_number}\n";
    echo "    User ID: {$order->user_id}\n";
    echo "    Restaurant ID: {$order->restaurant_id}\n";
    echo "    Customer: {$order->customer_name}\n";
    
    // Test order access for this specific order
    $canAccessOrder = false;
    
    // Check if manager owns the order
    if ($order->user_id === $manager->id) {
        $canAccessOrder = true;
        echo "    Access: User owns order\n";
    }
    // Check if manager is admin
    elseif ($manager->isAdmin()) {
        $canAccessOrder = true;
        echo "    Access: User is admin\n";
    }
    // Check if manager can access restaurant
    elseif ($canAccessRestaurant) {
        $canAccessOrder = true;
        echo "    Access: Manager can access restaurant\n";
    }
    else {
        echo "    Access: DENIED\n";
    }
    
    echo "\n";
}

echo "🎯 Summary:\n";
echo "  - Manager can access restaurant: " . ($canAccessRestaurant ? 'Yes' : 'No') . "\n";
echo "  - Manager should be able to view all restaurant orders: " . ($canAccessRestaurant ? 'Yes' : 'No') . "\n";

// Test the specific error scenario
echo "\n🔍 Testing Specific Error Scenario:\n";
$testOrder = $orders->first();
if ($testOrder) {
    echo "Testing access to Order #{$testOrder->id} ({$testOrder->order_number})\n";
    
    // Simulate the restaurantOrderShow method logic
    $canAccess = Manager::canAccessRestaurant($manager->id, $restaurant->id, 'manager');
    $isAdmin = $manager->isAdmin();
    
    echo "  - Can access restaurant: " . ($canAccess ? 'Yes' : 'No') . "\n";
    echo "  - Is admin: " . ($isAdmin ? 'Yes' : 'No') . "\n";
    
    if (!$canAccess && !$isAdmin) {
        echo "  - Result: UNAUTHORIZED - Need manager privileges\n";
    } else {
        echo "  - Result: AUTHORIZED\n";
        
        // Check if order belongs to restaurant
        if ($testOrder->restaurant_id !== $restaurant->id) {
            echo "  - Order restaurant mismatch: Order belongs to restaurant {$testOrder->restaurant_id}, but accessing restaurant {$restaurant->id}\n";
        } else {
            echo "  - Order belongs to restaurant: Yes\n";
        }
    }
}
