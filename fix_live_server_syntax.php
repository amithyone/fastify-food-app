<?php

echo "🔧 Checking for PHP syntax errors on live server...\n\n";

// List of critical files to check
$criticalFiles = [
    'app/Http/Controllers/BankTransferPaymentController.php',
    'app/Http/Controllers/OrderController.php',
    'app/Services/PayVibeService.php',
    'routes/web.php',
    'app/Models/Order.php',
    'app/Models/BankTransferPayment.php',
    'app/Models/Restaurant.php'
];

$errorsFound = false;

foreach ($criticalFiles as $file) {
    if (file_exists($file)) {
        $output = [];
        $returnCode = 0;
        exec("php -l $file 2>&1", $output, $returnCode);
        
        if ($returnCode === 0) {
            echo "✅ $file - No syntax errors\n";
        } else {
            echo "❌ $file - Syntax errors found:\n";
            foreach ($output as $line) {
                echo "   $line\n";
            }
            $errorsFound = true;
        }
    } else {
        echo "⚠️ $file - File not found\n";
    }
}

if (!$errorsFound) {
    echo "\n✅ No syntax errors found in critical files\n";
} else {
    echo "\n❌ Syntax errors found. Please fix them before proceeding.\n";
}

// Check if all required classes can be loaded
echo "\n🔧 Testing class loading...\n";

try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Test loading critical classes
    $classes = [
        'App\Http\Controllers\BankTransferPaymentController',
        'App\Http\Controllers\OrderController',
        'App\Services\PayVibeService',
        'App\Models\Order',
        'App\Models\BankTransferPayment',
        'App\Models\Restaurant'
    ];
    
    foreach ($classes as $class) {
        try {
            $reflection = new ReflectionClass($class);
            echo "✅ $class - Loaded successfully\n";
        } catch (Exception $e) {
            echo "❌ $class - Failed to load: " . $e->getMessage() . "\n";
            $errorsFound = true;
        }
    }
    
} catch (Exception $e) {
    echo "❌ Failed to bootstrap application: " . $e->getMessage() . "\n";
    $errorsFound = true;
}

// Check route registration
if (!$errorsFound) {
    echo "\n🔧 Testing route registration...\n";
    
    try {
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        $bankTransferRoute = null;
        
        foreach ($routes as $route) {
            if ($route->uri() === 'bank-transfer/initialize' && $route->methods()[0] === 'POST') {
                $bankTransferRoute = $route;
                break;
            }
        }
        
        if ($bankTransferRoute) {
            echo "✅ Bank transfer route registered successfully\n";
        } else {
            echo "❌ Bank transfer route not found\n";
            $errorsFound = true;
        }
        
    } catch (Exception $e) {
        echo "❌ Failed to check routes: " . $e->getMessage() . "\n";
        $errorsFound = true;
    }
}

if (!$errorsFound) {
    echo "\n🎉 All checks passed! The application should work correctly.\n";
} else {
    echo "\n⚠️ Issues found. Please fix them before testing the application.\n";
} 