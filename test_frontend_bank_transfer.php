<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

echo "🔧 Testing frontend bank transfer request...\n\n";

try {
    // Get the latest order
    $order = Order::latest()->first();
    if (!$order) {
        echo "❌ No orders found in database\n";
        exit(1);
    }
    
    echo "✅ Found order: {$order->order_number} (ID: {$order->id}, Amount: ₦{$order->total_amount})\n";
    
    // First, get a CSRF token by making a GET request to the homepage
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Extract CSRF token from the response
    preg_match('/<meta name="csrf-token" content="([^"]+)"/', $response, $matches);
    $csrfToken = $matches[1] ?? null;
    
    if (!$csrfToken) {
        echo "❌ Could not extract CSRF token\n";
        exit(1);
    }
    
    echo "✅ CSRF Token: $csrfToken\n";
    
    // Simulate the frontend request
    $url = 'http://localhost:8000/bank-transfer/initialize';
    $data = [
        'order_id' => $order->id,
        'amount' => $order->total_amount
    ];
    
    echo "🔧 Making request to: $url\n";
    echo "Request data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    
    // Create a cURL request to simulate frontend
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest',
        'X-CSRF-TOKEN: ' . $csrfToken
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "HTTP Status Code: $httpCode\n";
    
    if ($error) {
        echo "❌ cURL Error: $error\n";
        exit(1);
    }
    
    if ($httpCode !== 200) {
        echo "❌ HTTP Error: $httpCode\n";
        echo "Response: $response\n";
        exit(1);
    }
    
    $responseData = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "❌ JSON Parse Error: " . json_last_error_msg() . "\n";
        echo "Raw Response: $response\n";
        exit(1);
    }
    
    echo "✅ Response received successfully!\n";
    echo "Response: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    
    if (isset($responseData['success']) && $responseData['success']) {
        echo "✅ Bank transfer initialization successful!\n";
        echo "Virtual Account: " . ($responseData['data']['virtual_account_number'] ?? 'N/A') . "\n";
        echo "Bank Name: " . ($responseData['data']['bank_name'] ?? 'N/A') . "\n";
        echo "Payment Reference: " . ($responseData['data']['payment_reference'] ?? 'N/A') . "\n";
    } else {
        echo "❌ Bank transfer initialization failed!\n";
        echo "Error: " . ($responseData['message'] ?? 'Unknown error') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Test completed!\n"; 