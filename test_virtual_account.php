<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing PayVibe Virtual Account Generation\n";
echo "==========================================\n\n";

// Test configuration
$secretKey = config('services.payvibe.secret_key');
$baseUrl = config('services.payvibe.base_url');
$productIdentifier = config('services.payvibe.product_identifier');

echo "Configuration:\n";
echo "- Secret Key: " . substr($secretKey, 0, 20) . "...\n";
echo "- Base URL: $baseUrl\n";
echo "- Product Identifier: $productIdentifier\n\n";

// Test with correct payload structure
$ref = 'TEST-VA-' . time();

echo "Test Data:\n";
echo "- Reference: $ref\n";
echo "- Product Identifier: $productIdentifier\n\n";

// Correct payload structure
$payload = [
    'reference' => $ref,
    'product_identifier' => $productIdentifier
];

echo "Payload:\n";
echo json_encode($payload, JSON_PRETTY_PRINT) . "\n\n";

echo "Testing with correct payload structure...\n\n";

try {
    $response = Http::withToken($secretKey)
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post($baseUrl . '/v1/payments/virtual-accounts/initiate', $payload);

    echo "Response Status: " . $response->status() . "\n";
    echo "Content-Type: " . ($response->header('Content-Type') ?? 'N/A') . "\n\n";

    if ($response->successful()) {
        $data = $response->json();
        echo "✅ SUCCESS!\n";
        echo "Response Data:\n";
        echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
        
        if ($data && is_array($data)) {
            echo "Account Details:\n";
            echo "- Account Number: " . ($data['account_number'] ?? $data['accountNumber'] ?? 'N/A') . "\n";
            echo "- Bank Name: " . ($data['bank_name'] ?? $data['bank'] ?? 'N/A') . "\n";
            echo "- Account Name: " . ($data['account_name'] ?? $data['accountName'] ?? 'N/A') . "\n";
            echo "- Reference: " . ($data['reference'] ?? 'N/A') . "\n";
            echo "- Amount: " . ($data['amount'] ?? 'N/A') . "\n";
            echo "- Expires At: " . ($data['expires_at'] ?? $data['expiresAt'] ?? 'N/A') . "\n";
            echo "- Gateway Reference: " . ($data['gateway_ref'] ?? $data['gatewayRef'] ?? 'N/A') . "\n";
            
            // Check for nested data structure
            if (isset($data['data'])) {
                echo "\nNested Data Structure:\n";
                echo json_encode($data['data'], JSON_PRETTY_PRINT) . "\n";
            }
        }
    } else {
        echo "❌ FAILED: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n"; 