<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\TwilioSMSService;

echo "🔧 Testing Twilio Verify Integration...\n\n";

try {
    $smsService = new TwilioSMSService();
    
    // Test 1: Check Configuration
    echo "📋 Test 1: Checking Configuration...\n";
    $accountSid = config('services.twilio.account_sid');
    $authToken = config('services.twilio.auth_token');
    $fromNumber = config('services.twilio.from_number');
    $verifyServiceSid = config('services.twilio.verify_service_sid');
    
    echo "Account SID: " . ($accountSid ? '✅ Set' : '❌ Missing') . "\n";
    echo "Auth Token: " . ($authToken ? '✅ Set' : '❌ Missing') . "\n";
    echo "From Number: " . ($fromNumber ? '✅ Set' : '❌ Missing') . "\n";
    echo "Verify Service SID: " . ($verifyServiceSid ? '✅ Set' : '❌ Missing') . "\n\n";
    
    if (!$accountSid || !$authToken || !$fromNumber || !$verifyServiceSid) {
        echo "❌ Configuration incomplete. Please check your .env file.\n";
        echo "Required variables:\n";
        echo "- TWILIO_ACCOUNT_SID\n";
        echo "- TWILIO_AUTH_TOKEN\n";
        echo "- TWILIO_FROM_NUMBER\n";
        echo "- TWILIO_VERIFY_SERVICE_SID\n\n";
        exit(1);
    }
    
    // Test 2: Phone Number Validation
    echo "📱 Test 2: Phone Number Validation...\n";
    $testNumbers = [
        '08012345678',
        '+2348012345678',
        '8012345678',
        '1234567890',
        'invalid'
    ];
    
    foreach ($testNumbers as $number) {
        $isValid = $smsService->isValidPhoneNumber($number);
        echo "{$number}: " . ($isValid ? '✅ Valid' : '❌ Invalid') . "\n";
    }
    echo "\n";
    
    // Test 3: Send Verification Code (if phone number provided)
    if (isset($argv[1])) {
        $phoneNumber = $argv[1];
        echo "📤 Test 3: Sending Verification Code to {$phoneNumber}...\n";
        
        $result = $smsService->sendVerificationCode($phoneNumber);
        
        if ($result['success']) {
            echo "✅ Verification code sent successfully!\n";
            echo "Verification SID: {$result['verification_sid']}\n";
            echo "Status: {$result['status']}\n\n";
            
            // Test 4: Verify Code (if code provided)
            if (isset($argv[2])) {
                $code = $argv[2];
                echo "🔍 Test 4: Verifying Code {$code}...\n";
                
                $verifyResult = $smsService->verifyCode($phoneNumber, $code);
                
                if ($verifyResult['success']) {
                    echo "✅ Code verified successfully!\n";
                    echo "Verification SID: {$verifyResult['verification_sid']}\n\n";
                } else {
                    echo "❌ Code verification failed: {$verifyResult['error']}\n\n";
                }
            } else {
                echo "💡 To test verification, run: php test_twilio_verify.php {$phoneNumber} [CODE]\n\n";
            }
        } else {
            echo "❌ Failed to send verification code: {$result['error']}\n\n";
        }
    } else {
        echo "💡 To test SMS sending, run: php test_twilio_verify.php [PHONE_NUMBER]\n";
        echo "Example: php test_twilio_verify.php 08012345678\n\n";
    }
    
    // Test 5: Order Confirmation SMS
    echo "📦 Test 5: Testing Order Confirmation SMS...\n";
    $orderData = [
        'order_number' => 'ORD-20250804-TEST',
        'total_amount' => 5000,
        'status' => 'confirmed'
    ];
    
    if (isset($argv[1])) {
        $result = $smsService->sendOrderConfirmation($argv[1], $orderData);
        
        if ($result['success']) {
            echo "✅ Order confirmation SMS sent successfully!\n";
            echo "Message SID: {$result['message_sid']}\n\n";
        } else {
            echo "❌ Failed to send order confirmation: {$result['error']}\n\n";
        }
    } else {
        echo "💡 Skipped (no phone number provided)\n\n";
    }
    
    echo "🎉 Twilio Verify integration test completed!\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 