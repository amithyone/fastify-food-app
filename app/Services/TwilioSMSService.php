<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TwilioSMSService
{
    protected $accountSid;
    protected $authToken;
    protected $fromNumber;

    public function __construct()
    {
        $this->accountSid = config('services.twilio.account_sid');
        $this->authToken = config('services.twilio.auth_token');
        $this->fromNumber = config('services.twilio.from_number');
    }

    /**
     * Send SMS verification code
     */
    public function sendVerificationCode(string $phoneNumber): array
    {
        try {
            // Generate 6-digit verification code
            $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Format phone number (add +234 if not present)
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            
            // Store verification code in cache for 10 minutes
            $cacheKey = "sms_verification_{$formattedPhone}";
            Cache::put($cacheKey, $verificationCode, 600); // 10 minutes
            
            // Prepare message
            $message = "Your Fastify verification code is: {$verificationCode}. Valid for 10 minutes. Do not share this code with anyone.";
            
            // Send SMS via Twilio
            $response = Http::withBasicAuth($this->accountSid, $this->authToken)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Messages.json", [
                    'From' => $this->fromNumber,
                    'To' => $formattedPhone,
                    'Body' => $message
                ]);
            
            if ($response->successful()) {
                $result = $response->json();
                Log::info('SMS verification code sent successfully', [
                    'to' => $formattedPhone,
                    'message_sid' => $result['sid'] ?? null
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Verification code sent successfully',
                    'message_sid' => $result['sid'] ?? null
                ];
            } else {
                Log::error('SMS verification failed', [
                    'to' => $formattedPhone,
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);
                
                return [
                    'success' => false,
                    'error' => $response->json()['message'] ?? 'Failed to send verification code'
                ];
            }
        } catch (\Exception $e) {
            Log::error('SMS service error', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber
            ]);
            
            return [
                'success' => false,
                'error' => 'SMS service temporarily unavailable'
            ];
        }
    }

    /**
     * Verify SMS code
     */
    public function verifyCode(string $phoneNumber, string $code): array
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            $cacheKey = "sms_verification_{$formattedPhone}";
            
            $storedCode = Cache::get($cacheKey);
            
            if (!$storedCode) {
                return [
                    'success' => false,
                    'error' => 'Verification code expired or not found'
                ];
            }
            
            if ($storedCode === $code) {
                // Remove the code from cache after successful verification
                Cache::forget($cacheKey);
                
                Log::info('SMS verification successful', [
                    'phone' => $formattedPhone
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Phone number verified successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Invalid verification code'
                ];
            }
        } catch (\Exception $e) {
            Log::error('SMS verification error', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber
            ]);
            
            return [
                'success' => false,
                'error' => 'Verification failed'
            ];
        }
    }

    /**
     * Send order confirmation SMS
     */
    public function sendOrderConfirmation(string $phoneNumber, array $orderData): array
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            
            $message = "Order Confirmed!\n\n";
            $message .= "Order #: {$orderData['order_number']}\n";
            $message .= "Total: ₦{$orderData['total_amount']}\n";
            $message .= "Status: {$orderData['status']}\n";
            $message .= "Thank you for your order!";
            
            $response = Http::withBasicAuth($this->accountSid, $this->authToken)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Messages.json", [
                    'From' => $this->fromNumber,
                    'To' => $formattedPhone,
                    'Body' => $message
                ]);
            
            if ($response->successful()) {
                $result = $response->json();
                Log::info('Order confirmation SMS sent successfully', [
                    'to' => $formattedPhone,
                    'order_number' => $orderData['order_number'],
                    'message_sid' => $result['sid'] ?? null
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Order confirmation sent',
                    'message_sid' => $result['sid'] ?? null
                ];
            } else {
                Log::error('Order confirmation SMS failed', [
                    'to' => $formattedPhone,
                    'response' => $response->json()
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Failed to send order confirmation'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Order confirmation SMS error', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber
            ]);
            
            return [
                'success' => false,
                'error' => 'SMS service temporarily unavailable'
            ];
        }
    }

    /**
     * Send order status update SMS
     */
    public function sendOrderStatusUpdate(string $phoneNumber, array $orderData): array
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            
            $message = "Order Status Update!\n\n";
            $message .= "Order #: {$orderData['order_number']}\n";
            $message .= "New Status: {$orderData['status']}\n";
            $message .= "Updated: " . now()->format('M d, Y H:i');
            
            $response = Http::withBasicAuth($this->accountSid, $this->authToken)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Messages.json", [
                    'From' => $this->fromNumber,
                    'To' => $formattedPhone,
                    'Body' => $message
                ]);
            
            if ($response->successful()) {
                $result = $response->json();
                Log::info('Order status update SMS sent successfully', [
                    'to' => $formattedPhone,
                    'order_number' => $orderData['order_number'],
                    'status' => $orderData['status']
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Status update sent',
                    'message_sid' => $result['sid'] ?? null
                ];
            } else {
                Log::error('Order status update SMS failed', [
                    'to' => $formattedPhone,
                    'response' => $response->json()
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Failed to send status update'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Order status update SMS error', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber
            ]);
            
            return [
                'success' => false,
                'error' => 'SMS service temporarily unavailable'
            ];
        }
    }

    /**
     * Format phone number to international format
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-digit characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If it starts with 0, replace with +234
        if (strlen($phoneNumber) === 11 && substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '+234' . substr($phoneNumber, 1);
        }
        // If it's 10 digits, add +234
        elseif (strlen($phoneNumber) === 10) {
            $phoneNumber = '+234' . $phoneNumber;
        }
        // If it doesn't start with +, add +
        elseif (strlen($phoneNumber) === 11 && substr($phoneNumber, 0, 1) !== '+') {
            $phoneNumber = '+' . $phoneNumber;
        }
        
        return $phoneNumber;
    }

    /**
     * Check if phone number is valid
     */
    public function isValidPhoneNumber(string $phoneNumber): bool
    {
        $formatted = $this->formatPhoneNumber($phoneNumber);
        return preg_match('/^\+234[0-9]{10}$/', $formatted);
    }
} 