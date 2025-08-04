<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\WhatsAppVerificationService;

echo "🔧 Testing WhatsApp Business API Setup...\n\n";

try {
    $whatsappService = new WhatsAppVerificationService();
    
    // Test 1: Check API Health
    echo "1️⃣ Testing API Health...\n";
    $healthResult = $whatsappService->checkAPIHealth();
    
    if ($healthResult['success']) {
        echo "✅ API Health: {$healthResult['status']}\n";
        echo "Phone Number ID: " . ($healthResult['data']['id'] ?? 'N/A') . "\n";
        echo "Phone Number: " . ($healthResult['data']['verified_name'] ?? 'N/A') . "\n";
    } else {
        echo "❌ API Health Check Failed: {$healthResult['error']}\n";
        echo "Please check your credentials and API access.\n";
    }
    
    // Test 2: Get Message Templates
    echo "\n2️⃣ Testing Message Templates...\n";
    $templatesResult = $whatsappService->getMessageTemplates();
    
    if ($templatesResult['success']) {
        $templates = $templatesResult['templates'];
        echo "✅ Found " . count($templates) . " message templates\n";
        
        foreach ($templates as $template) {
            echo "   - {$template['name']} ({$template['status']})\n";
        }
    } else {
        echo "❌ Failed to get templates: {$templatesResult['error']}\n";
    }
    
    // Test 3: Test Message Sending (if phone number provided)
    $testPhone = getenv('TEST_WHATSAPP_PHONE');
    if ($testPhone) {
        echo "\n3️⃣ Testing Message Sending...\n";
        $messageResult = $whatsappService->sendMessage($testPhone, "🧪 Test message from Laravel app at " . now()->format('Y-m-d H:i:s'));
        
        if ($messageResult['success']) {
            echo "✅ Message sent successfully!\n";
            echo "Message ID: {$messageResult['message_id']}\n";
        } else {
            echo "❌ Message sending failed: {$messageResult['error']}\n";
        }
    } else {
        echo "\n3️⃣ Skipping message test (set TEST_WHATSAPP_PHONE env var to test)\n";
    }
    
    // Test 4: Test OTP Sending
    if ($testPhone) {
        echo "\n4️⃣ Testing OTP Sending...\n";
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpResult = $whatsappService->sendOTP($testPhone, $otp);
        
        if ($otpResult['success']) {
            echo "✅ OTP sent successfully!\n";
            echo "OTP: {$otp}\n";
            echo "Message ID: {$otpResult['message_id']}\n";
        } else {
            echo "❌ OTP sending failed: {$otpResult['error']}\n";
        }
    }
    
    // Test 5: Test Order Confirmation
    if ($testPhone) {
        echo "\n5️⃣ Testing Order Confirmation...\n";
        $orderData = [
            'order_number' => 'TEST-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'total_amount' => '2500.00',
            'status' => 'confirmed'
        ];
        
        $orderResult = $whatsappService->sendOrderConfirmation($testPhone, $orderData);
        
        if ($orderResult['success']) {
            echo "✅ Order confirmation sent successfully!\n";
            echo "Order #: {$orderData['order_number']}\n";
            echo "Message ID: {$orderResult['message_id']}\n";
        } else {
            echo "❌ Order confirmation failed: {$orderResult['error']}\n";
        }
    }
    
    // Test 6: Environment Variables Check
    echo "\n6️⃣ Checking Environment Variables...\n";
    $requiredVars = [
        'WHATSAPP_ACCESS_TOKEN' => 'Access Token',
        'WHATSAPP_PHONE_NUMBER_ID' => 'Phone Number ID',
        'WHATSAPP_BUSINESS_ACCOUNT_ID' => 'Business Account ID',
        'WHATSAPP_WEBHOOK_VERIFY_TOKEN' => 'Webhook Verify Token'
    ];
    
    $allVarsSet = true;
    foreach ($requiredVars as $var => $description) {
        $value = getenv($var);
        if ($value) {
            echo "✅ {$description}: " . substr($value, 0, 10) . "...\n";
        } else {
            echo "❌ {$description}: Not set\n";
            $allVarsSet = false;
        }
    }
    
    if ($allVarsSet) {
        echo "✅ All required environment variables are set!\n";
    } else {
        echo "❌ Some environment variables are missing. Please check your .env file.\n";
    }
    
    // Test 7: Webhook URL Check
    echo "\n7️⃣ Webhook Configuration...\n";
    $webhookUrl = "https://" . getenv('APP_URL', 'localhost') . "/whatsapp/webhook";
    echo "Webhook URL: {$webhookUrl}\n";
    echo "Verify Token: " . (getenv('WHATSAPP_WEBHOOK_VERIFY_TOKEN') ? 'Set' : 'Not Set') . "\n";
    
    echo "\n📋 Configuration Instructions:\n";
    echo "1. Add this webhook URL to your Meta Business Manager\n";
    echo "2. Set the verify token in Meta settings\n";
    echo "3. Subscribe to: messages, message_status, message_template_status\n";
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🎉 WhatsApp setup test completed!\n";
echo "\n📱 Next Steps:\n";
echo "1. Complete Meta Business verification\n";
echo "2. Set up webhook in Meta Business Manager\n";
echo "3. Create and approve message templates\n";
echo "4. Test with real phone numbers\n";
echo "5. Submit for production approval\n"; 