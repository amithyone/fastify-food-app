<?php

require_once 'vendor/autoload.php';

use App\Services\PaymentCalculationService;
use App\Models\PaymentSetting;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Debugging Payment Calculation System\n";
echo "===================================\n\n";

$paymentService = new PaymentCalculationService();

// Test with ₦5,000 order
$subtotal = 5000;
$orderData = [
    'subtotal' => $subtotal,
    'payment_method' => 'card'
];

echo "Testing with ₦{$subtotal} order and card payment\n";
echo "Order Data: " . json_encode($orderData) . "\n\n";

// Check each setting individually
$settings = [
    'service_charge' => 'Service Charge',
    'tax_rate' => 'Tax Rate',
    'delivery_fee' => 'Delivery Fee'
];

foreach ($settings as $key => $name) {
    $setting = PaymentSetting::getByKey($key);
    if ($setting) {
        echo "{$name}:\n";
        echo "- Value: {$setting->value}\n";
        echo "- Type: {$setting->type}\n";
        echo "- Status: {$setting->status}\n";
        echo "- Conditions: " . json_encode($setting->conditions) . "\n";
        echo "- Conditions Met: " . ($setting->conditionsMet($orderData) ? 'YES' : 'NO') . "\n";
        echo "- Calculated Charge: ₦" . number_format($setting->calculateCharge($subtotal), 2) . "\n\n";
    } else {
        echo "{$name}: NOT FOUND\n\n";
    }
}

// Test the full calculation
echo "Full Calculation Result:\n";
$charges = $paymentService->calculateOrderCharges($subtotal, $orderData);
echo json_encode($charges, JSON_PRETTY_PRINT) . "\n\n";

echo "Debug completed!\n"; 