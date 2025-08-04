<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WhatsAppVerificationService
{
    protected $accessToken;
    protected $phoneNumberId;
    protected $businessAccountId;
    protected $webhookVerifyToken;
    protected $apiVersion = 'v18.0';

    public function __construct()
    {
        $this->accessToken = config('services.whatsapp.access_token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->businessAccountId = config('services.whatsapp.business_account_id');
        $this->webhookVerifyToken = config('services.whatsapp.webhook_verify_token');
    }

    /**
     * Verify webhook endpoint
     */
    public function verifyWebhook(string $mode, string $token, string $challenge): array
    {
        if ($mode === 'subscribe' && $token === $this->webhookVerifyToken) {
            Log::info('WhatsApp webhook verified successfully', [
                'mode' => $mode,
                'challenge' => $challenge
            ]);

            return [
                'success' => true,
                'challenge' => $challenge
            ];
        }

        Log::error('WhatsApp webhook verification failed', [
            'mode' => $mode,
            'token' => $token,
            'expected_token' => $this->webhookVerifyToken
        ]);

        return [
            'success' => false,
            'error' => 'Invalid verification token'
        ];
    }

    /**
     * Send WhatsApp message
     */
    public function sendMessage(string $to, string $message, string $templateName = null): array
    {
        try {
            $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";
            
            $data = [
                'messaging_product' => 'whatsapp',
                'to' => $to,
            ];

            if ($templateName) {
                // Send template message
                $data['type'] = 'template';
                $data['template'] = [
                    'name' => $templateName,
                    'language' => [
                        'code' => 'en'
                    ]
                ];
            } else {
                // Send text message
                $data['type'] = 'text';
                $data['text'] = [
                    'body' => $message
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json'
            ])->post($url, $data);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('WhatsApp message sent successfully', [
                    'to' => $to,
                    'message_id' => $result['messages'][0]['id'] ?? null
                ]);

                return [
                    'success' => true,
                    'message_id' => $result['messages'][0]['id'] ?? null,
                    'data' => $result
                ];
            } else {
                Log::error('WhatsApp message sending failed', [
                    'to' => $to,
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);

                return [
                    'success' => false,
                    'error' => $response->json()['error']['message'] ?? 'Unknown error',
                    'status' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp service error', [
                'error' => $e->getMessage(),
                'to' => $to
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send OTP verification message
     */
    public function sendOTP(string $to, string $otp): array
    {
        $message = "Your verification code is: {$otp}. Valid for 10 minutes. Do not share this code with anyone.";
        
        return $this->sendMessage($to, $message);
    }

    /**
     * Send order confirmation message
     */
    public function sendOrderConfirmation(string $to, array $orderData): array
    {
        $message = "Order Confirmed!\n\n";
        $message .= "Order #: {$orderData['order_number']}\n";
        $message .= "Total: ₦{$orderData['total_amount']}\n";
        $message .= "Status: {$orderData['status']}\n";
        $message .= "Thank you for your order!";

        return $this->sendMessage($to, $message);
    }

    /**
     * Send order status update
     */
    public function sendOrderStatusUpdate(string $to, array $orderData): array
    {
        $message = "Order Status Update!\n\n";
        $message .= "Order #: {$orderData['order_number']}\n";
        $message .= "New Status: {$orderData['status']}\n";
        $message .= "Updated: " . now()->format('M d, Y H:i');

        return $this->sendMessage($to, $message);
    }

    /**
     * Get message templates
     */
    public function getMessageTemplates(): array
    {
        try {
            $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->businessAccountId}/message_templates";
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}"
            ])->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'templates' => $response->json()['data'] ?? []
                ];
            } else {
                Log::error('Failed to get WhatsApp templates', [
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);

                return [
                    'success' => false,
                    'error' => $response->json()['error']['message'] ?? 'Unknown error'
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp template error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create message template
     */
    public function createMessageTemplate(string $name, string $category, string $language, string $content): array
    {
        try {
            $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->businessAccountId}/message_templates";
            
            $data = [
                'name' => $name,
                'category' => $category,
                'language' => $language,
                'components' => [
                    [
                        'type' => 'BODY',
                        'text' => $content
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json'
            ])->post($url, $data);

            if ($response->successful()) {
                Log::info('WhatsApp template created successfully', [
                    'name' => $name,
                    'template_id' => $response->json()['id'] ?? null
                ]);

                return [
                    'success' => true,
                    'template_id' => $response->json()['id'] ?? null,
                    'data' => $response->json()
                ];
            } else {
                Log::error('Failed to create WhatsApp template', [
                    'name' => $name,
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);

                return [
                    'success' => false,
                    'error' => $response->json()['error']['message'] ?? 'Unknown error'
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp template creation error', [
                'error' => $e->getMessage(),
                'name' => $name
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check API health
     */
    public function checkAPIHealth(): array
    {
        try {
            $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}";
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}"
            ])->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => 'healthy',
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'status' => 'unhealthy',
                    'error' => $response->json()['error']['message'] ?? 'Unknown error'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }
} 