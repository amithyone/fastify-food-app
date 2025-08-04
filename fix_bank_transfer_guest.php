<?php

echo "🔧 Fixing bank transfer authentication issue...\n\n";

// Update the routes file to remove auth middleware
$routesFile = 'routes/web.php';
$content = file_get_contents($routesFile);

// Replace the auth middleware line
$oldPattern = "Route::middleware(['auth'])->prefix('bank-transfer')->name('bank-transfer.')->group(function () {";
$newPattern = "Route::prefix('bank-transfer')->name('bank-transfer.')->group(function () {";

if (strpos($content, $oldPattern) !== false) {
    $content = str_replace($oldPattern, $newPattern, $content);
    file_put_contents($routesFile, $content);
    echo "✅ Removed auth middleware from bank transfer routes\n";
} else {
    echo "⚠️ Auth middleware already removed or pattern not found\n";
}

// Update the BankTransferPaymentController to handle guest users
$controllerFile = 'app/Http/Controllers/BankTransferPaymentController.php';
$controllerContent = file_get_contents($controllerFile);

// Find the user authorization check
$oldAuthCheck = "// Check if user can pay for this order
        if (Auth::check() && \$order->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this order'
            ], 403);
        }";

$newAuthCheck = "// Check if user can pay for this order (allow guest users)
        if (Auth::check() && \$order->user_id !== null && \$order->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this order'
            ], 403);
        }";

if (strpos($controllerContent, $oldAuthCheck) !== false) {
    $controllerContent = str_replace($oldAuthCheck, $newAuthCheck, $controllerContent);
    file_put_contents($controllerFile, $controllerContent);
    echo "✅ Updated controller to handle guest users\n";
} else {
    echo "⚠️ Controller already updated or pattern not found\n";
}

// Update the user_id assignment
$oldUserId = "'user_id' => Auth::id(),";
$newUserId = "'user_id' => Auth::check() ? Auth::id() : null,";

if (strpos($controllerContent, $oldUserId) !== false) {
    $controllerContent = str_replace($oldUserId, $newUserId, $controllerContent);
    file_put_contents($controllerFile, $controllerContent);
    echo "✅ Updated user_id assignment to handle guest users\n";
} else {
    echo "⚠️ User ID assignment already updated or pattern not found\n";
}

echo "\n🎉 Bank transfer authentication fix completed!\n";
echo "📋 Next steps:\n";
echo "1. Clear caches: php artisan optimize:clear\n";
echo "2. Test the bank transfer functionality\n"; 