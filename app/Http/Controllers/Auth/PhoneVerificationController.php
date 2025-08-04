<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwilioSMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PhoneVerificationController extends Controller
{
    protected $smsService;

    public function __construct(TwilioSMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Show phone verification form
     */
    public function show()
    {
        return view('auth.phone-verification');
    }

    /**
     * Send verification code
     */
    public function sendCode(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20'
        ]);

        $phoneNumber = $request->phone_number;

        // Validate phone number format
        if (!$this->smsService->isValidPhoneNumber($phoneNumber)) {
            return response()->json([
                'success' => false,
                'error' => 'Please enter a valid Nigerian phone number (e.g., 08012345678)'
            ], 400);
        }

        // Send verification code
        $result = $this->smsService->sendVerificationCode($phoneNumber);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your phone number'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ], 400);
        }
    }

    /**
     * Verify phone number
     */
    public function verify(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'verification_code' => 'required|string|size:6'
        ]);

        $phoneNumber = $request->phone_number;
        $code = $request->verification_code;

        // Verify the code
        $result = $this->smsService->verifyCode($phoneNumber, $code);

        if ($result['success']) {
            // Update user's phone verification status
            $user = auth()->user();
            if ($user) {
                $user->update([
                    'phone_number' => $phoneNumber,
                    'phone_verified_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Phone number verified successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ], 400);
        }
    }

    /**
     * Resend verification code
     */
    public function resend(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20'
        ]);

        $phoneNumber = $request->phone_number;

        // Send new verification code
        $result = $this->smsService->sendVerificationCode($phoneNumber);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'New verification code sent'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ], 400);
        }
    }
} 