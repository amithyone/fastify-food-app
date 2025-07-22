<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PhoneVerification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Twilio\Rest\Client;

class PhoneAuthController extends Controller
{
    /**
     * Show the phone login form
     */
    public function showLoginForm()
    {
        return view('auth.phone-login');
    }

    /**
     * Show the phone registration form
     */
    public function showRegisterForm()
    {
        return view('auth.phone-register');
    }

    /**
     * Send verification code via WhatsApp
     */
    public function sendVerificationCode(Request $request): JsonResponse
    {
        try {
            \Log::info('Phone verification request received', [
                'phone_number' => $request->phone_number,
                'is_login' => $request->input('is_login', false)
            ]);

            $request->validate([
                'phone_number' => ['required', 'string'],
            ]);

            $phoneNumber = $this->formatPhoneNumber($request->phone_number);
            \Log::info('Formatted phone number', ['formatted' => $phoneNumber]);

            // Check if user exists (for login) or doesn't exist (for registration)
            $userExists = User::where('phone_number', $phoneNumber)->exists();
            $isLogin = $request->input('is_login', false);

            if ($isLogin && !$userExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'No account found with this phone number. Please register first.'
                ], 404);
            }

            if (!$isLogin && $userExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'An account with this phone number already exists. Please login instead.'
                ], 409);
            }

            // Generate verification code
            $verification = PhoneVerification::generateCode($phoneNumber);
            \Log::info('Verification code generated', ['code' => $verification->verification_code]);
            
            // Send WhatsApp message
            $whatsappResult = $this->sendWhatsAppMessage($phoneNumber, $verification->verification_code);

            // Prepare response based on WhatsApp result
            $responseData = [
                'success' => true,
                'phone_number' => $phoneNumber,
                'expires_in' => $verification->getRemainingMinutes(),
                'debug_code' => $verification->verification_code,
                'whatsapp_message' => "🔐 Fastify Verification Code\n\nYour verification code is: *{$verification->verification_code}*\n\nThis code will expire in 10 minutes.\n\nThank you for choosing Fastify! 🍽️"
            ];

            // Set message based on WhatsApp result
            if ($whatsappResult['sent']) {
                $responseData['message'] = 'Verification code sent to your WhatsApp! 📱';
                $responseData['notification_type'] = 'whatsapp';
                $responseData['whatsapp_status'] = $whatsappResult['status'];
            } else {
                $responseData['message'] = 'Verification code generated! Use the code below to continue.';
                $responseData['notification_type'] = 'manual';
                $responseData['whatsapp_status'] = 'not_sent';
                $responseData['whatsapp_error'] = $whatsappResult['error'] ?? 'WhatsApp not available';
            }

            // In development, always include the code for testing
            if (app()->environment('local', 'development')) {
                $responseData['development_mode'] = true;
                $responseData['message'] = 'Development Mode: Verification code shown below for testing.';
            }

            \Log::info('Phone verification successful', $responseData);
            return response()->json($responseData);

        } catch (\Exception $e) {
            \Log::error('Verification code error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = app()->environment('local', 'development') 
                ? 'Failed to send verification code: ' . $e->getMessage()
                : 'Failed to send verification code. Please try again.';
                
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }
    }

    /**
     * Verify the code and authenticate user
     */
    public function verifyCode(Request $request): JsonResponse
    {
        $request->validate([
            'phone_number' => ['required', 'string'],
            'verification_code' => ['required', 'string', 'size:6'],
            'name' => ['required_if:is_registration,true', 'string', 'max:255'],
            'is_registration' => ['boolean'],
        ]);

        $phoneNumber = $this->formatPhoneNumber($request->phone_number);
        $code = $request->verification_code;
        $isRegistration = $request->boolean('is_registration');

        // Verify the code
        if (!PhoneVerification::verifyCode($phoneNumber, $code)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code. Please try again.'
            ], 400);
        }

        try {
            if ($isRegistration) {
                // Create new user
                $user = User::create([
                    'name' => $request->name,
                    'phone_number' => $phoneNumber,
                    'phone_verified_at' => Carbon::now(),
                    'email' => $phoneNumber . '@fastify.com', // Temporary email
                    'password' => Hash::make(Str::random(16)), // Temporary password
                ]);

                Auth::login($user);

                return response()->json([
                    'success' => true,
                    'message' => 'Account created successfully!',
                    'redirect' => route('menu.index'),
                ]);

            } else {
                // Login existing user
                $user = User::where('phone_number', $phoneNumber)->first();
                
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No account found with this phone number.'
                    ], 404);
                }

                // Update phone verification status
                $user->update(['phone_verified_at' => Carbon::now()]);
                
                Auth::login($user);

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful!',
                    'redirect' => route('menu.index'),
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Resend verification code
     */
    public function resendCode(Request $request): JsonResponse
    {
        $request->validate([
            'phone_number' => ['required', 'string'],
        ]);

        $phoneNumber = $this->formatPhoneNumber($request->phone_number);

        // Check if there's a recent code (within 1 minute)
        $recentVerification = PhoneVerification::getLatest($phoneNumber);
        if ($recentVerification && !$recentVerification->isExpired() && $recentVerification->getRemainingMinutes() > 9) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before requesting a new code.'
            ], 429);
        }

        try {
            $verification = PhoneVerification::generateCode($phoneNumber);
            $this->sendWhatsAppMessage($phoneNumber, $verification->verification_code);

            return response()->json([
                'success' => true,
                'message' => 'New verification code sent!',
                'expires_in' => $verification->getRemainingMinutes(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again.'
            ], 500);
        }
    }

    /**
     * Format phone number to Nigerian format
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-digit characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If it starts with 0, replace with +234
        if (strlen($phoneNumber) === 11 && $phoneNumber[0] === '0') {
            $phoneNumber = '+234' . substr($phoneNumber, 1);
        }
        
        // If it's 10 digits, add +234
        if (strlen($phoneNumber) === 10) {
            $phoneNumber = '+234' . $phoneNumber;
        }
        
        return $phoneNumber;
    }

    /**
     * Send WhatsApp message with verification code
     */
    private function sendWhatsAppMessage(string $phoneNumber, string $code): array
    {
        $message = "🔐 Fastify Verification Code\n\n";
        $message .= "Your verification code is: *{$code}*\n\n";
        $message .= "This code will expire in 10 minutes.\n";
        $message .= "If you didn't request this code, please ignore this message.\n\n";
        $message .= "Thank you for choosing Fastify! 🍽️";

        // For development/testing, we'll log the message and show it in response
        if (app()->environment('local', 'development') && !env('ENABLE_REAL_WHATSAPP', false)) {
            \Log::info("WhatsApp message to {$phoneNumber}: {$message}");
            \Log::info("Verification code: {$code}");
            return ['sent' => true, 'status' => 'simulated_sent'];
        }

        // For production, use Twilio WhatsApp API
        try {
            $sid = config('services.twilio.account_sid');
            $token = config('services.twilio.auth_token');
            $whatsappFrom = config('services.twilio.whatsapp_from');
            
            if (!$sid || !$token) {
                \Log::warning('Twilio credentials not configured, falling back to development mode');
                \Log::info("WhatsApp message to {$phoneNumber}: {$message}");
                \Log::info("Verification code: {$code}");
                return ['sent' => false, 'error' => 'Twilio credentials not configured'];
            }
            
            $twilio = new Client($sid, $token);

            // For Twilio WhatsApp sandbox, the recipient must first join the sandbox
            // by sending a specific message to the Twilio WhatsApp number
            $twilioMessage = $twilio->messages
                ->create("whatsapp:{$phoneNumber}", // to
                    array(
                        "from" => $whatsappFrom,
                        "body" => $message
                    )
                );

            \Log::info('WhatsApp message sent successfully to ' . $phoneNumber . ' with SID: ' . $twilioMessage->sid);
            
            // Log additional details for debugging
            \Log::info('Twilio message details', [
                'to' => $phoneNumber,
                'from' => $whatsappFrom,
                'status' => $twilioMessage->status,
                'sid' => $twilioMessage->sid,
                'error_code' => $twilioMessage->errorCode ?? null,
                'error_message' => $twilioMessage->errorMessage ?? null
            ]);

            return ['sent' => true, 'status' => $twilioMessage->status];

        } catch (\Exception $e) {
            \Log::error('WhatsApp message failed: ' . $e->getMessage());
            \Log::error('Twilio error details', [
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'phone_number' => $phoneNumber
            ]);
            
            // Fallback: log the message for manual sending
            \Log::info("FALLBACK - WhatsApp message to {$phoneNumber}: {$message}");
            \Log::info("FALLBACK - Verification code: {$code}");
            
            // Don't throw the exception, just log it
            // This allows the verification process to continue
            return ['sent' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check if phone number exists (for AJAX validation)
     */
    public function checkPhoneNumber(Request $request): JsonResponse
    {
        $request->validate([
            'phone_number' => ['required', 'string'],
        ]);

        $phoneNumber = $this->formatPhoneNumber($request->phone_number);
        $userExists = User::where('phone_number', $phoneNumber)->exists();

        return response()->json([
            'exists' => $userExists,
            'phone_number' => $phoneNumber,
        ]);
    }
}
