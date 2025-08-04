<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\GuestSession;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\TableQR;

echo "🔧 Testing guest session functionality...\n\n";

try {
    // Get a restaurant and table QR for testing
    $restaurant = Restaurant::first();
    if (!$restaurant) {
        echo "❌ No restaurants found in database\n";
        exit(1);
    }
    
    $tableQR = TableQR::first();
    if (!$tableQR) {
        echo "❌ No table QR codes found in database\n";
        exit(1);
    }
    
    echo "✅ Found restaurant: {$restaurant->name}\n";
    echo "✅ Found table QR: {$tableQR->code}\n";
    
    // Create a guest session
    $session = GuestSession::create([
        'restaurant_id' => $restaurant->id,
        'table_qr_id' => $tableQR->id,
        'session_id' => GuestSession::generateSessionId(),
        'table_number' => '5',
        'cart_data' => ['items' => []],
        'customer_info' => ['name' => 'Guest User'],
        'expires_at' => now()->addHours(24),
        'is_active' => true
    ]);
    
    echo "✅ Created guest session: {$session->session_id}\n";
    
    // Create an order for this session
    $order = Order::create([
        'restaurant_id' => $restaurant->id,
        'user_id' => null, // Guest user
        'session_id' => $session->session_id,
        'order_number' => (new Order())->generateOrderNumber(),
        'customer_name' => 'Guest Customer',
        'phone_number' => 'N/A',
        'delivery_address' => 'Table: 5',
        'order_type' => 'in_restaurant',
        'total_amount' => 2500,
        'status' => 'pending',
        'payment_method' => 'transfer'
    ]);
    
    echo "✅ Created order for guest session: {$order->order_number}\n";
    
    // Test retrieving session
    $retrievedSession = GuestSession::getActiveSession($session->session_id);
    if ($retrievedSession) {
        echo "✅ Successfully retrieved guest session\n";
        echo "   Session ID: {$retrievedSession->session_id}\n";
        echo "   Table: {$retrievedSession->table_number}\n";
        echo "   Expires: {$retrievedSession->expires_at}\n";
        echo "   Is Expired: " . ($retrievedSession->isExpired() ? 'Yes' : 'No') . "\n";
    } else {
        echo "❌ Failed to retrieve guest session\n";
    }
    
    // Test retrieving orders for session
    $orders = Order::where('session_id', $session->session_id)->get();
    echo "✅ Found {$orders->count()} orders for guest session\n";
    
    foreach ($orders as $order) {
        echo "   Order: {$order->order_number} - ₦{$order->total_amount} - {$order->status}\n";
    }
    
    // Test API endpoint
    $url = "http://localhost:8000/guest-session/{$session->session_id}";
    echo "\n🔧 Testing API endpoint: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Status Code: $httpCode\n";
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        echo "✅ API endpoint working correctly!\n";
        echo "Session ID: " . $data['data']['session']['session_id'] . "\n";
        echo "Orders count: " . count($data['data']['orders']) . "\n";
    } else {
        echo "❌ API endpoint failed\n";
        echo "Response: $response\n";
    }
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Guest session test completed!\n"; 