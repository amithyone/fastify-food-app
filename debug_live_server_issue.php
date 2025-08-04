<?php

echo "🔧 Debugging live server issue...\n\n";

// 1. Check if we can bootstrap Laravel
echo "1️⃣ Testing Laravel bootstrap...\n";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    echo "✅ Laravel bootstrapped successfully\n";
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Check if the route exists
echo "\n2️⃣ Testing route registration...\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $bankTransferRoute = null;
    
    foreach ($routes as $route) {
        if ($route->uri() === 'bank-transfer/initialize' && in_array('POST', $route->methods())) {
            $bankTransferRoute = $route;
            break;
        }
    }
    
    if ($bankTransferRoute) {
        echo "✅ Bank transfer route found\n";
        echo "   URI: " . $bankTransferRoute->uri() . "\n";
        echo "   Methods: " . implode(', ', $bankTransferRoute->methods()) . "\n";
        echo "   Controller: " . get_class($bankTransferRoute->getController()) . "\n";
        echo "   Action: " . $bankTransferRoute->getActionMethod() . "\n";
    } else {
        echo "❌ Bank transfer route not found\n";
        echo "Available routes:\n";
        foreach ($routes as $route) {
            if (strpos($route->uri(), 'bank') !== false) {
                echo "   " . $route->uri() . " (" . implode(',', $route->methods()) . ")\n";
            }
        }
    }
} catch (Exception $e) {
    echo "❌ Route check failed: " . $e->getMessage() . "\n";
}

// 3. Test if the controller can be instantiated
echo "\n3️⃣ Testing controller instantiation...\n";
try {
    $controller = new \App\Http\Controllers\BankTransferPaymentController(
        app(\App\Services\PayVibeService::class)
    );
    echo "✅ BankTransferPaymentController instantiated successfully\n";
} catch (Exception $e) {
    echo "❌ Controller instantiation failed: " . $e->getMessage() . "\n";
}

// 4. Test if PayVibeService can be instantiated
echo "\n4️⃣ Testing PayVibeService...\n";
try {
    $payVibeService = app(\App\Services\PayVibeService::class);
    echo "✅ PayVibeService instantiated successfully\n";
} catch (Exception $e) {
    echo "❌ PayVibeService instantiation failed: " . $e->getMessage() . "\n";
}

// 5. Test database connection
echo "\n5️⃣ Testing database connection...\n";
try {
    $result = \Illuminate\Support\Facades\DB::select('SELECT 1 as test');
    echo "✅ Database connection successful\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

// 6. Test if Order model exists
echo "\n6️⃣ Testing Order model...\n";
try {
    $order = \App\Models\Order::first();
    echo "✅ Order model accessible\n";
} catch (Exception $e) {
    echo "❌ Order model failed: " . $e->getMessage() . "\n";
}

// 7. Test if BankTransferPayment model exists
echo "\n7️⃣ Testing BankTransferPayment model...\n";
try {
    $payment = \App\Models\BankTransferPayment::first();
    echo "✅ BankTransferPayment model accessible\n";
} catch (Exception $e) {
    echo "❌ BankTransferPayment model failed: " . $e->getMessage() . "\n";
}

// 8. Test environment variables
echo "\n8️⃣ Testing environment variables...\n";
$requiredEnvVars = [
    'PAYVIBE_BASE_URL',
    'PAYVIBE_SECRET_KEY',
    'PAYVIBE_PUBLIC_KEY',
    'PAYVIBE_PRODUCT_IDENTIFIER'
];

foreach ($requiredEnvVars as $var) {
    if (env($var)) {
        echo "✅ $var is set\n";
    } else {
        echo "❌ $var is not set\n";
    }
}

// 9. Test if we can make a simple request
echo "\n9️⃣ Testing simple request...\n";
try {
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'order_id' => 1,
        'amount' => 1000
        ]);
    
    echo "✅ Request object created successfully\n";
} catch (Exception $e) {
    echo "❌ Request creation failed: " . $e->getMessage() . "\n";
}

echo "\n🎉 Debug completed!\n";
echo "\n📋 Next steps:\n";
echo "1. If any ❌ errors above, fix them first\n";
echo "2. Run: php fix_live_server_migrations.php\n";
echo "3. Run: php artisan optimize:clear\n";
echo "4. Restart your web server\n"; 