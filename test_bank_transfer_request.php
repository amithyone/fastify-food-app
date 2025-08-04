<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\BankTransferPaymentController;
use App\Services\PayVibeService;
use Illuminate\Http\Request;

echo "🔧 Testing bank transfer request...\n\n";

try {
    // Create a test request
    $request = new Request();
    $request->merge([
        'order_id' => 1,
        'amount' => 1000
    ]);
    
    echo "✅ Request created successfully\n";
    
    // Test if controller can handle the request
    $controller = new BankTransferPaymentController(app(PayVibeService::class));
    
    echo "✅ Controller instantiated successfully\n";
    
    // Test the initialize method
    echo "🔧 Testing initialize method...\n";
    
    try {
        $response = $controller->initialize($request);
        echo "✅ Initialize method executed successfully\n";
        echo "Response type: " . get_class($response) . "\n";
        
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            echo "Response data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Initialize method failed: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🎉 Test completed!\n"; 