<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayVibeService
{
    protected $apiKey;
    protected $secretKey;
    protected $baseUrl;
    protected $isTestMode;

    public function __construct()
    {
        $this->apiKey = config('services.payvibe.public_key');
        $this->secretKey = config('services.payvibe.secret_key');
        $this->baseUrl = config('services.payvibe.base_url', 'https://payvibeapi.six3tech.com/api');
        $this->isTestMode = config('services.payvibe.test_mode', true);
    }

    /**
     * Initialize a payment transaction
     */
    public function initializePayment($orderData)
    {
        try {
            $payload = [
                'amount' => $orderData['amount'] * 100, // Convert to kobo
                'currency' => 'NGN',
                'reference' => $orderData['reference'],
                'email' => $orderData['email'],
                'callback_url' => route('payment.callback'),
                'metadata' => [
                    'order_id' => $orderData['order_id'],
                    'customer_name' => $orderData['customer_name'],
                    'restaurant_id' => $orderData['restaurant_id'],
                    'payment_type' => 'order_payment'
                ]
            ];

            Log::info('PayVibe payment initialization', [
                'order_id' => $orderData['order_id'],
                'amount' => $orderData['amount'],
                'reference' => $orderData['reference']
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/transaction/initialize', $payload);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('PayVibe payment initialized successfully', [
                    'reference' => $orderData['reference'],
                    'authorization_url' => $data['authorization_url'] ?? null
                ]);
                
                return [
                    'success' => true,
                    'authorization_url' => $data['authorization_url'],
                    'reference' => $data['reference'] ?? $orderData['reference'],
                    'access_code' => $data['access_code'] ?? null
                ];
            } else {
                Log::error('PayVibe payment initialization failed', [
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Payment initialization failed',
                    'error' => $response->json()
                ];
            }
        } catch (\Exception $e) {
            Log::error('PayVibe payment initialization exception', [
                'error' => $e->getMessage(),
                'order_id' => $orderData['order_id'] ?? null
            ]);
            
            return [
                'success' => false,
                'message' => 'Payment service temporarily unavailable',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify a payment transaction
     */
    public function verifyPayment($reference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->get($this->baseUrl . '/transaction/verify/' . $reference);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('PayVibe payment verification', [
                    'reference' => $reference,
                    'status' => $data['status'] ?? 'unknown',
                    'amount' => $data['amount'] ?? null
                ]);
                
                return [
                    'success' => true,
                    'status' => $data['status'] ?? 'unknown',
                    'amount' => $data['amount'] ?? 0,
                    'gateway_ref' => $data['gateway_ref'] ?? null,
                    'data' => $data
                ];
            } else {
                Log::error('PayVibe payment verification failed', [
                    'reference' => $reference,
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Payment verification failed',
                    'error' => $response->json()
                ];
            }
        } catch (\Exception $e) {
            Log::error('PayVibe payment verification exception', [
                'error' => $e->getMessage(),
                'reference' => $reference
            ]);
            
            return [
                'success' => false,
                'message' => 'Payment verification service temporarily unavailable',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate a unique payment reference
     */
    public function generateReference()
    {
        return 'PAYVIBE_' . time() . '_' . strtoupper(uniqid());
    }

    /**
     * Get payment status text
     */
    public function getStatusText($status)
    {
        $statuses = [
            'success' => 'Payment Successful',
            'failed' => 'Payment Failed',
            'pending' => 'Payment Pending',
            'abandoned' => 'Payment Abandoned'
        ];

        return $statuses[$status] ?? 'Unknown Status';
    }

    /**
     * Generate webhook hash for verification
     */
    public function generateWebhookHash($data)
    {
        return hash_hmac('sha256', json_encode($data), $this->secretKey);
    }

    /**
     * Verify webhook hash
     */
    public function verifyWebhookHash($data, $hash)
    {
        $expectedHash = $this->generateWebhookHash($data);
        return hash_equals($expectedHash, $hash);
    }

    /**
     * Generate virtual account for payment
     */
    public function generateVirtualAccount($paymentData)
    {
        try {
            $payload = [
                'reference' => $paymentData['reference'],
                'product_identifier' => config('services.payvibe.product_identifier', 'fast')
            ];

            Log::info('PayVibe virtual account generation', [
                'reference' => $paymentData['reference'],
                'product_identifier' => config('services.payvibe.product_identifier', 'fast')
            ]);

            $response = Http::withToken($this->secretKey)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->baseUrl . '/v1/payments/virtual-accounts/initiate', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['status']) && $data['status'] === true && isset($data['data'])) {
                    $accountData = $data['data'];
                    Log::info('PayVibe virtual account generated successfully', [
                        'reference' => $paymentData['reference'],
                        'account_number' => $accountData['virtual_account_number'] ?? null,
                        'bank_name' => $accountData['bank_name'] ?? null
                    ]);
                    
                    return [
                        'success' => true,
                        'account_number' => $accountData['virtual_account_number'] ?? null,
                        'bank_name' => $accountData['bank_name'] ?? null,
                        'account_name' => $accountData['account_name'] ?? null,
                        'reference' => $accountData['reference'] ?? $paymentData['reference'],
                        'status' => $accountData['status'] ?? 'pending',
                        'data' => $data
                    ];
                } else {
                    Log::error('PayVibe virtual account generation failed', [
                        'response' => $data,
                        'reference' => $paymentData['reference']
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => $data['message'] ?? 'Virtual account generation failed',
                        'error' => $data
                    ];
                }
            } else {
                Log::error('PayVibe virtual account generation failed', [
                    'response' => $response->json(),
                    'status' => $response->status(),
                    'reference' => $paymentData['reference']
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Virtual account generation failed',
                    'error' => $response->json()
                ];
            }
        } catch (\Exception $e) {
            Log::error('PayVibe virtual account generation exception', [
                'error' => $e->getMessage(),
                'reference' => $paymentData['reference'] ?? null
            ]);
            
            return [
                'success' => false,
                'message' => 'Virtual account service temporarily unavailable',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify virtual account payment
     */
    public function verifyVirtualAccountPayment($reference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->get($this->baseUrl . '/v1/payments/virtual-accounts/verify/' . $reference);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('PayVibe virtual account payment verification', [
                    'reference' => $reference,
                    'status' => $data['status'] ?? 'unknown',
                    'amount' => $data['amount'] ?? null
                ]);
                
                return [
                    'success' => true,
                    'status' => $data['status'] ?? 'unknown',
                    'amount' => $data['amount'] ?? 0,
                    'gateway_ref' => $data['gateway_ref'] ?? null,
                    'paid_at' => $data['paid_at'] ?? null,
                    'data' => $data
                ];
            } else {
                Log::error('PayVibe virtual account verification failed', [
                    'reference' => $reference,
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Virtual account verification failed',
                    'error' => $response->json()
                ];
            }
        } catch (\Exception $e) {
            Log::error('PayVibe virtual account verification exception', [
                'error' => $e->getMessage(),
                'reference' => $reference
            ]);
            
            return [
                'success' => false,
                'message' => 'Virtual account verification service temporarily unavailable',
                'error' => $e->getMessage()
            ];
        }
    }
} 