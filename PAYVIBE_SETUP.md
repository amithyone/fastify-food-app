# PayVibe Integration Setup Guide

This guide explains how to set up and use the PayVibe payment integration in the AbujaEat Laravel application.

## Overview

PayVibe is a secure payment gateway that allows restaurants to accept payments for promotions and other services. The integration includes:

- Payment initialization
- Webhook handling
- Payment verification
- Transaction tracking
- External webhook notifications

## Environment Variables

Add the following variables to your `.env` file:

```env
# PayVibe Configuration
PAYVIBE_PUBLIC_KEY=pk_live_jzndandouhd5rlh1rlrvabbtsnr64qu8
PAYVIBE_SECRET_KEY=sk_live_eqnfqzsy0x5qoagvb4v8ong9qqtollc3
PAYVIBE_BASE_URL=https://payvibeapi.six3tech.com/api
PAYVIBE_TEST_MODE=true
PAYVIBE_PRODUCT_IDENTIFIER=fast

# External Webhook Configuration (Optional)
EXTERNAL_WEBHOOK_URL=https://your-external-service.com/webhook
EXTERNAL_WEBHOOK_API_KEY=your_external_api_key
EXTERNAL_WEBHOOK_API_CODE=abujaeat
```

## Database Tables

The integration creates the following table:

### `pay_vibe_transactions`

- `id` - Primary key
- `payment_id` - Foreign key to promotion_payments
- `reference` - Unique payment reference
- `amount` - Amount in kobo
- `status` - Payment status (pending, successful, failed)
- `authorization_url` - PayVibe payment URL
- `access_code` - PayVibe access code
- `gateway_reference` - Gateway transaction reference
- `amount_received` - Amount received in kobo
- `metadata` - Additional transaction data (JSON)
- `webhook_data` - Webhook payload data (JSON)
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Web Routes

- `POST /{slug}/payvibe/initialize` - Initialize PayVibe payment
- `POST /{slug}/payvibe/virtual-account` - Generate virtual account
- `GET /payvibe/callback` - Handle PayVibe callback
- `GET /payvibe/verify/{reference}` - Verify payment status
- `GET /{slug}/promotions/payment/{paymentId}/payvibe` - PayVibe payment page
- `GET /{slug}/promotions/payment/{paymentId}/virtual-account` - Virtual account payment page

### API Routes

- `POST /api/webhook/payvibe` - Handle PayVibe webhooks

## Controllers

### PayVibeController

Main controller for handling PayVibe payments:

- `initializePayment()` - Initialize payment with PayVibe
- `generateVirtualAccount()` - Generate virtual account for payment
- `webhook()` - Handle PayVibe webhooks
- `verifyPayment()` - Verify payment status
- `callback()` - Handle payment callbacks

### PromotionController

Extended with PayVibe support:

- `payvibePayment()` - Show PayVibe payment page

## Services

### PayVibeService

Handles PayVibe API interactions:

- `initializePayment()` - Initialize payment
- `generateVirtualAccount()` - Generate virtual account
- `verifyVirtualAccountPayment()` - Verify virtual account payment
- `verifyPayment()` - Verify payment status
- `generateReference()` - Generate unique references
- `generateWebhookHash()` - Generate webhook hash
- `verifyWebhookHash()` - Verify webhook hash

### WebhookService

Handles external webhook notifications:

- `sendToExternalService()` - Send webhook to external service
- `sendSuccessfulTransaction()` - Send success webhook
- `sendFailedTransaction()` - Send failure webhook
- `sendPendingTransaction()` - Send pending webhook

## Models

### PayVibeTransaction

Model for tracking PayVibe transactions:

- Relationships with PromotionPayment, Restaurant, and User
- Status scopes (pending, successful, failed)
- Helper methods for status checks and formatting
- Methods for marking transactions as successful/failed

## Views

### promotions/payvibe-payment.blade.php

Modern payment interface with:

- Payment status display
- Payment information
- Secure payment options
- PayVibe payment button
- Alternative payment methods
- Real-time status checking

### promotions/virtual-account-payment.blade.php

Virtual account payment interface with:

- Virtual account generation
- Bank account details display
- Payment instructions
- Copy to clipboard functionality
- Real-time status checking
- Alternative payment methods

## Usage Flow

### Standard Payment Flow
1. **Payment Creation**: Restaurant owner creates a promotion payment
2. **Payment Initialization**: User clicks "Pay with PayVibe"
3. **PayVibe Redirect**: User is redirected to PayVibe payment page
4. **Payment Processing**: User completes payment on PayVibe
5. **Webhook Handling**: PayVibe sends webhook with payment status
6. **Status Update**: Payment status is updated in database
7. **Promotion Activation**: If successful, promotion is activated
8. **External Notification**: Webhook is sent to external service (optional)

### Virtual Account Flow
1. **Payment Creation**: Restaurant owner creates a promotion payment
2. **Virtual Account Generation**: User clicks "Virtual Account"
3. **Account Generation**: System generates virtual account via PayVibe API
4. **Account Display**: Virtual account details are shown to user
5. **Bank Transfer**: User makes bank transfer to virtual account
6. **Payment Verification**: System verifies payment via PayVibe API
7. **Status Update**: Payment status is updated in database
8. **Promotion Activation**: If successful, promotion is activated

## Webhook Security

The integration includes security measures:

- Hash verification using HMAC SHA256
- Access key validation
- Request validation
- Error logging and monitoring

## Testing

### Test Environment

Set `PAYVIBE_TEST_MODE=true` in your `.env` file for testing.

### Test Payment Flow

1. Create a promotion payment
2. Navigate to PayVibe payment page
3. Use test credentials to complete payment
4. Verify webhook handling
5. Check payment status updates

## Error Handling

The integration includes comprehensive error handling:

- API request failures
- Webhook verification failures
- Database transaction errors
- Payment verification errors
- Logging for debugging

## Monitoring

Monitor the following logs:

- `PayVibe payment initialization`
- `PayVibe webhook processing`
- `External webhook notifications`
- `Payment verification`

## Security Considerations

1. **API Keys**: Keep PayVibe API keys secure
2. **Webhook Verification**: Always verify webhook signatures
3. **Database Transactions**: Use transactions for data consistency
4. **Error Logging**: Log errors without exposing sensitive data
5. **HTTPS**: Ensure all communications use HTTPS

## Troubleshooting

### Common Issues

1. **Payment Initialization Fails**
   - Check PayVibe API credentials
   - Verify API endpoint configuration
   - Check network connectivity

2. **Webhook Not Received**
   - Verify webhook URL configuration
   - Check server accessibility
   - Validate webhook signature

3. **Payment Status Not Updated**
   - Check webhook processing
   - Verify database transactions
   - Review error logs

### Debug Commands

```bash
# Check PayVibe configuration
php artisan tinker --execute="echo 'PayVibe Config: ' . config('services.payvibe.base_url');"

# Test webhook endpoint
curl -X POST http://your-domain.com/api/webhook/payvibe \
  -H "Content-Type: application/json" \
  -d '{"data":{"reference":"TEST123","amount":1000,"status":"successful"},"hash":"test_hash"}'

# Check transaction records
php artisan tinker --execute="echo 'PayVibe Transactions: ' . App\Models\PayVibeTransaction::count();"
```

## Integration with Existing System

The PayVibe integration works alongside the existing payment system:

- **Bank Transfer**: Original payment method
- **PayVibe**: New secure payment gateway
- **Hybrid Approach**: Users can choose payment method
- **Unified Interface**: Consistent user experience

## Future Enhancements

Potential improvements:

1. **Multiple Payment Gateways**: Add more payment providers
2. **Recurring Payments**: Support subscription payments
3. **Payment Analytics**: Track payment performance
4. **Mobile SDK**: Native mobile payment integration
5. **Advanced Webhooks**: More detailed webhook payloads

## Support

For technical support:

1. Check the application logs
2. Verify PayVibe account configuration
3. Test webhook endpoints
4. Review database transactions
5. Contact PayVibe support if needed

## Reference Implementation

This integration is based on the reference implementation in `payveibe-refrence/` which provides:

- XtraPay payment processing
- Webhook handling patterns
- Security best practices
- Error handling examples
- Database transaction management 