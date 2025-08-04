# 📱 WhatsApp Business API Setup Guide

## Overview

This guide provides step-by-step instructions for setting up WhatsApp Business API with Meta verification for live production use.

## 🚀 Prerequisites

- Meta Business Account
- Business phone number
- Business documents (registration, license, etc.)
- Website domain (recommended)
- SSL certificate for webhook

## 📋 Step-by-Step Setup

### Step 1: Meta Business Account Setup

1. **Create Meta Business Account**
   ```
   Go to: business.facebook.com
   Click: "Create Account" or "Get Started"
   ```

2. **Complete Business Profile**
   - Business name and description
   - Business address and contact details
   - Business category selection
   - Upload business documents

3. **Verify Business Email**
   - Check email for verification link
   - Complete email verification process

### Step 2: WhatsApp Business Account Setup

1. **Access WhatsApp in Business Manager**
   ```
   Meta Business Manager → All Tools → WhatsApp
   Click: "Get Started" or "Add WhatsApp"
   ```

2. **Choose Business API**
   - Select "Business API" option
   - Not "WhatsApp Business App"

3. **Complete Business Profile**
   - Business name and description
   - Business category
   - Business website URL
   - Business hours
   - Upload business logo

### Step 3: Phone Number Verification

1. **Add Business Phone Number**
   - Enter your business phone number
   - Select country code
   - Choose number type (landline/mobile)

2. **Verify Phone Number**
   - Meta sends verification code via SMS
   - Enter code to verify number
   - This becomes your WhatsApp Business number

### Step 4: Business Verification (Required for Live API)

1. **Submit Required Documents**
   - Business registration certificate
   - Government-issued ID
   - Business license (if applicable)
   - Proof of address
   - Website ownership (if applicable)

2. **Business Information Verification**
   - Verify business address
   - Confirm business category
   - Validate contact information

### Step 5: API Credentials Setup

1. **Get API Credentials**
   ```
   WhatsApp → API Setup → Get Started
   ```

2. **Generate Access Token**
   - Click "Generate Token"
   - Copy and save the access token securely
   - Add to your `.env` file

3. **Get Phone Number ID**
   - Note your Phone Number ID
   - Add to your `.env` file

4. **Get Business Account ID**
   - Note your WhatsApp Business Account ID
   - Add to your `.env` file

### Step 6: Webhook Configuration

1. **Set Up Webhook URL**
   ```
   Webhook URL: https://yourdomain.com/whatsapp/webhook
   Verify Token: [Generate a secure token]
   ```

2. **Configure Webhook Fields**
   - Select: `messages`, `message_status`, `message_template_status`
   - Save webhook configuration

3. **Test Webhook**
   - Send test message
   - Verify webhook receives data

### Step 7: Message Templates Setup

1. **Create Message Templates**
   ```
   WhatsApp → Message Templates → Create Template
   ```

2. **Template Categories**
   - **Marketing**: Promotional messages
   - **Utility**: Transactional messages
   - **Authentication**: OTP, verification codes

3. **Template Examples**
   ```
   Template Name: order_confirmation
   Category: Utility
   Language: English
   Content: "Your order #{order_number} has been confirmed. Total: ₦{total_amount}. Thank you!"
   ```

### Step 8: Environment Configuration

Add these variables to your `.env` file:

```env
# WhatsApp Business API Configuration
WHATSAPP_ACCESS_TOKEN=your_access_token_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id_here
WHATSAPP_BUSINESS_ACCOUNT_ID=your_business_account_id_here
WHATSAPP_WEBHOOK_VERIFY_TOKEN=your_webhook_verify_token_here
WHATSAPP_API_VERSION=v18.0
```

### Step 9: Testing and Validation

1. **Test Message Sending**
   ```php
   $whatsappService = new WhatsAppVerificationService();
   $result = $whatsappService->sendMessage('+234XXXXXXXXX', 'Test message');
   ```

2. **Test Webhook Reception**
   - Send message to your WhatsApp number
   - Check webhook logs
   - Verify message processing

3. **Test Template Messages**
   ```php
   $result = $whatsappService->sendMessage('+234XXXXXXXXX', '', 'order_confirmation');
   ```

### Step 10: Production Approval

1. **Submit for Production Review**
   - Provide use case details
   - Explain message volume expectations
   - Submit business verification documents

2. **Wait for Approval**
   - Usually takes 2-5 business days
   - Check email for notifications
   - Monitor status in Business Manager

## 🔧 Technical Implementation

### 1. Service Provider Registration

Add to `config/app.php`:

```php
'providers' => [
    // ...
    App\Providers\WhatsAppServiceProvider::class,
],
```

### 2. Create Service Provider

```php
<?php

namespace App\Providers;

use App\Services\WhatsAppVerificationService;
use Illuminate\Support\ServiceProvider;

class WhatsAppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(WhatsAppVerificationService::class, function ($app) {
            return new WhatsAppVerificationService();
        });
    }
}
```

### 3. Usage Examples

```php
// Send simple message
$whatsappService = app(WhatsAppVerificationService::class);
$result = $whatsappService->sendMessage('+234XXXXXXXXX', 'Hello from our restaurant!');

// Send order confirmation
$orderData = [
    'order_number' => 'ORD-20250804-12345',
    'total_amount' => '2500.00',
    'status' => 'confirmed'
];
$result = $whatsappService->sendOrderConfirmation('+234XXXXXXXXX', $orderData);

// Send OTP
$result = $whatsappService->sendOTP('+234XXXXXXXXX', '123456');
```

## 🚨 Common Issues and Solutions

### 1. Webhook Verification Fails
```
Error: "Invalid verification token"
Solution: Ensure webhook verify token matches in .env and Meta settings
```

### 2. Message Sending Fails
```
Error: "Invalid access token"
Solution: Regenerate access token in Meta Business Manager
```

### 3. Template Message Rejected
```
Error: "Template not approved"
Solution: Wait for template approval or modify template content
```

### 4. Phone Number Not Verified
```
Error: "Phone number not verified"
Solution: Complete phone number verification process
```

## 📊 Monitoring and Logging

### 1. Enable Detailed Logging

```php
// In your WhatsApp service
Log::info('WhatsApp message sent', [
    'to' => $phoneNumber,
    'message_id' => $messageId,
    'status' => $status
]);
```

### 2. Monitor Webhook Events

```php
// In webhook controller
Log::info('WhatsApp webhook received', [
    'event_type' => $eventType,
    'data' => $request->all()
]);
```

### 3. Track Message Status

```php
// Handle message status updates
protected function handleMessageStatus(array $status): void
{
    $messageId = $status['id'];
    $statusType = $status['status'];
    
    Log::info('Message status update', [
        'message_id' => $messageId,
        'status' => $statusType
    ]);
}
```

## 🔒 Security Best Practices

### 1. Secure Token Storage
- Store tokens in environment variables
- Never commit tokens to version control
- Rotate tokens regularly

### 2. Webhook Security
- Use HTTPS for webhook URLs
- Implement webhook signature verification
- Validate incoming webhook data

### 3. Rate Limiting
- Implement rate limiting for message sending
- Respect WhatsApp API limits
- Monitor API usage

## 📈 Production Checklist

- [ ] Meta Business Account verified
- [ ] WhatsApp Business Account approved
- [ ] Phone number verified
- [ ] API credentials configured
- [ ] Webhook URL accessible
- [ ] Message templates approved
- [ ] Environment variables set
- [ ] Error handling implemented
- [ ] Logging configured
- [ ] Rate limiting implemented
- [ ] Security measures in place
- [ ] Production approval received

## 🎯 Next Steps

1. **Complete Meta verification process**
2. **Set up webhook endpoints**
3. **Create and approve message templates**
4. **Test with sandbox environment**
5. **Submit for production approval**
6. **Monitor and optimize**

This setup ensures your WhatsApp Business API integration is production-ready and compliant with Meta's requirements! 📱✅ 