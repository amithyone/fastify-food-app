<?php

require_once 'vendor/autoload.php';

use App\Models\Order;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Pickup Order Functionality\n";
echo "==================================\n\n";

// Test 1: Create a sample pickup order
echo "Test 1: Creating sample pickup order\n";

$order = new Order();
$order->restaurant_id = 1;
$order->user_id = null; // Guest order
$order->order_number = $order->generateOrderNumber();
$order->customer_name = 'John Doe';
$order->phone_number = '08012345678';
$order->delivery_address = 'Pickup at Restaurant';
$order->order_type = 'pickup';
$order->pickup_code = strtoupper(substr(md5(uniqid()), 0, 6));
$order->pickup_time = now()->addMinutes(30);
$order->pickup_name = 'John Doe';
$order->pickup_phone = '08012345678';
$order->subtotal = 5000;
$order->service_charge = 250;
$order->tax_amount = 375;
$order->delivery_fee = 0;
$order->total_amount = 5625;
$order->status = 'pending';
$order->payment_method = 'cash';
$order->notes = 'Payment Method: Cash | Pickup';

echo "Order created successfully!\n";
echo "- Order Number: {$order->order_number}\n";
echo "- Pickup Code: {$order->pickup_code}\n";
echo "- Pickup Time: {$order->formatted_pickup_time}\n";
echo "- Customer: {$order->pickup_name}\n";
echo "- Phone: {$order->pickup_phone}\n";
echo "- Total: {$order->formatted_total}\n";
echo "- Order Type: {$order->order_type_display}\n\n";

// Test 2: Test order type methods
echo "Test 2: Order type methods\n";
echo "- Is Pickup: " . ($order->isPickup() ? 'YES' : 'NO') . "\n";
echo "- Is Delivery: " . ($order->isDelivery() ? 'YES' : 'NO') . "\n";
echo "- Is In Restaurant: " . ($order->isInRestaurant() ? 'YES' : 'NO') . "\n";
echo "- Pickup Time Future: " . ($order->isPickupTimeFuture() ? 'YES' : 'NO') . "\n\n";

// Test 3: Test pickup code generation
echo "Test 3: Pickup code generation\n";
$pickupCode = $order->generatePickupCode();
echo "- Generated Code: {$pickupCode}\n";
echo "- Code Length: " . strlen($pickupCode) . " characters\n";
echo "- Is Uppercase: " . (ctype_upper($pickupCode) ? 'YES' : 'NO') . "\n\n";

echo "Pickup order functionality test completed!\n"; 