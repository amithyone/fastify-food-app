<?php

require_once 'vendor/autoload.php';

use App\Services\PayVibeService;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Updated PayVibeService\n";
echo "==============================\n\n";

$service = new PayVibeService();

$paymentData = [
    'reference' => 'TEST-SERVICE-' . time()
];

echo "Test Data:\n";
echo "- Reference: {$paymentData['reference']}\n\n";

try {
    $result = $service->generateVirtualAccount($paymentData);
    
    echo "Result:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
    
    if ($result['success']) {
        echo "✅ SUCCESS!\n";
        echo "- Account Number: " . ($result['account_number'] ?? 'N/A') . "\n";
        echo "- Bank Name: " . ($result['bank_name'] ?? 'N/A') . "\n";
        echo "- Account Name: " . ($result['account_name'] ?? 'N/A') . "\n";
        echo "- Reference: " . ($result['reference'] ?? 'N/A') . "\n";
        echo "- Status: " . ($result['status'] ?? 'N/A') . "\n";
    } else {
        echo "❌ FAILED: " . ($result['message'] ?? 'Unknown error') . "\n";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n"; 