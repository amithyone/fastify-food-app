<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

echo "🔧 Testing bank transfer route...\n";

try {
    // Test if the route exists
    $routes = Route::getRoutes();
    $bankTransferRoute = null;
    
    foreach ($routes as $route) {
        if ($route->uri() === 'bank-transfer/initialize' && $route->methods()[0] === 'POST') {
            $bankTransferRoute = $route;
            break;
        }
    }
    
    if ($bankTransferRoute) {
        echo "✅ Bank transfer route found\n";
        echo "Route: " . $bankTransferRoute->uri() . "\n";
        echo "Methods: " . implode(', ', $bankTransferRoute->methods()) . "\n";
        echo "Controller: " . get_class($bankTransferRoute->getController()) . "\n";
        echo "Action: " . $bankTransferRoute->getActionMethod() . "\n";
    } else {
        echo "❌ Bank transfer route not found\n";
    }
    
    // Test if the controller exists
    $controllerPath = 'app/Http/Controllers/BankTransferPaymentController.php';
    if (file_exists($controllerPath)) {
        echo "✅ BankTransferPaymentController exists\n";
        
        // Check if the initialize method exists
        $controllerContent = file_get_contents($controllerPath);
        if (strpos($controllerContent, 'public function initialize') !== false) {
            echo "✅ initialize method exists\n";
        } else {
            echo "❌ initialize method not found\n";
        }
    } else {
        echo "❌ BankTransferPaymentController not found\n";
    }
    
    // Test if PayVibeService exists
    $servicePath = 'app/Services/PayVibeService.php';
    if (file_exists($servicePath)) {
        echo "✅ PayVibeService exists\n";
        
        $serviceContent = file_get_contents($servicePath);
        if (strpos($serviceContent, 'generateVirtualAccount') !== false) {
            echo "✅ generateVirtualAccount method exists\n";
        } else {
            echo "❌ generateVirtualAccount method not found\n";
        }
    } else {
        echo "❌ PayVibeService not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Route test completed!\n"; 