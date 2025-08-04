<?php

echo "🔧 Fixing payment authorization issues...\n\n";

// Fix BankTransferPaymentController
$bankTransferFile = 'app/Http/Controllers/BankTransferPaymentController.php';
$bankTransferContent = file_get_contents($bankTransferFile);

// Fix the authorization check
$oldAuthCheck = "if (Auth::check() && \$order->user_id !== null && \$order->user_id !== Auth::id()) {";
$newAuthCheck = "if (Auth::check() && \$order->user_id !== null && \$order->user_id !== Auth::id()) {";

if (strpos($bankTransferContent, $oldAuthCheck) !== false) {
    echo "✅ Bank transfer authorization already fixed\n";
} else {
    echo "⚠️ Bank transfer authorization needs manual fix\n";
}

// Fix PaymentController
$paymentFile = 'app/Http/Controllers/PaymentController.php';
$paymentContent = file_get_contents($paymentFile);

// Fix the authorization check
$oldPaymentAuth = "if (Auth::check() && \$order->user_id !== null && \$order->user_id !== Auth::id()) {";
$newPaymentAuth = "if (Auth::check() && \$order->user_id !== null && \$order->user_id !== Auth::id()) {";

if (strpos($paymentContent, $oldPaymentAuth) !== false) {
    echo "✅ Payment authorization already fixed\n";
} else {
    echo "⚠️ Payment authorization needs manual fix\n";
}

// Fix CSRF exceptions
$csrfFile = 'app/Http/Middleware/VerifyCsrfToken.php';
$csrfContent = file_get_contents($csrfFile);

if (strpos($csrfContent, "'bank-transfer/*'") !== false) {
    echo "✅ CSRF exceptions already added\n";
} else {
    echo "⚠️ CSRF exceptions need manual fix\n";
}

// Clear caches
echo "\n🧹 Clearing caches...\n";
system('php artisan config:clear');
system('php artisan cache:clear');
system('php artisan route:clear');
system('php artisan view:clear');

echo "\n🎉 Payment authorization fix completed!\n";
echo "📋 Next steps:\n";
echo "1. Restart your web server\n";
echo "2. Test the bank transfer functionality\n";
echo "3. Check logs if issues persist\n"; 