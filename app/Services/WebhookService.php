<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PayVibeTransaction;
use App\Models\User;

class WebhookService
{
    /**
     * Send transaction notification to external service
     */
    public static function sendToExternalService(PayVibeTransaction $transaction, User $user, $status = 'successful')
    {
        try {
            // External webhook configuration
            $webhookUrl = env('EXTERNAL_WEBHOOK_URL', '');
            $apiKey = env('EXTERNAL_WEBHOOK_API_KEY', '');
            $apiCode = env('EXTERNAL_WEBHOOK_API_CODE', 'abujaeat');

            if (empty($webhookUrl) || empty($apiKey)) {
                Log::warning('External webhook not configured', [
                    'transaction_id' => $transaction->id,
                    'webhook_url' => $webhookUrl,
                    'has_api_key' => !empty($apiKey)
                ]);
                return false;
            }

            // Prepare the webhook payload
            $payload = [
                'site_api_code' => $apiCode,
                'reference' => $transaction->reference,
                'amount' => $transaction->amount / 100, // Convert from kobo to naira
                'currency' => 'NGN',
                'status' => $status === 'successful' ? 'success' : $status,
                'payment_method' => 'payvibe',
                'customer_email' => $user->email,
                'customer_name' => $user->name,
                'description' => 'Promotion payment via PayVibe',
                'external_id' => (string) $transaction->id,
                'metadata' => [
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'payment_id' => $transaction->payment_id,
                    'gateway_reference' => $transaction->gateway_reference,
                    'amount_received' => $transaction->amount_received ? $transaction->amount_received / 100 : null,
                    'payment_reference' => $transaction->reference,
                    'site_name' => 'abujaeat.com',
                    'site_url' => config('app.url')
                ],
                'timestamp' => $transaction->created_at ? $transaction->created_at->toISOString() : now()->toISOString()
            ];

            // Send webhook
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-API-Key' => $apiKey,
                'User-Agent' => 'AbujaEat-Webhook/1.0'
            ])->timeout(30)->post($webhookUrl, $payload);

            // Log the response
            Log::info('External webhook sent', [
                'transaction_id' => $transaction->id,
                'status_code' => $response->status(),
                'response' => $response->json(),
                'payload' => $payload
            ]);

            // Check if webhook was successful
            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['success']) && $responseData['success']) {
                    Log::info('External webhook successful', [
                        'transaction_id' => $transaction->id,
                        'message' => $responseData['message'] ?? 'Transaction processed'
                    ]);
                    return true;
                } else {
                    Log::error('External webhook failed', [
                        'transaction_id' => $transaction->id,
                        'error' => $responseData['error'] ?? 'Unknown error'
                    ]);
                    return false;
                }
            } else {
                Log::error('External webhook HTTP error', [
                    'transaction_id' => $transaction->id,
                    'status_code' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('External webhook exception', [
                'transaction_id' => $transaction->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send webhook for failed transactions
     */
    public static function sendFailedTransaction(PayVibeTransaction $transaction, User $user, $reason = 'Transaction failed')
    {
        return self::sendToExternalService($transaction, $user, 'failed');
    }

    /**
     * Send webhook for pending transactions (when transaction is created)
     */
    public static function sendPendingTransaction(PayVibeTransaction $transaction, User $user)
    {
        return self::sendToExternalService($transaction, $user, 'pending');
    }

    /**
     * Send webhook for successful transactions
     */
    public static function sendSuccessfulTransaction(PayVibeTransaction $transaction, User $user)
    {
        return self::sendToExternalService($transaction, $user, 'success');
    }

    /**
     * Retry failed webhooks
     */
    public static function retryFailedWebhooks()
    {
        // This method can be used to retry failed webhook attempts
        // You can implement a queue system for this
        Log::info('Retrying failed webhooks');
    }
} 