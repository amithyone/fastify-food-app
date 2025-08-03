<?php

namespace App\Http\Controllers;

use App\Models\PromotionPayment;
use App\Models\PayVibeTransaction;
use App\Services\PayVibeService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PayVibeController extends Controller
{
    protected $payVibeService;

    public function __construct(PayVibeService $payVibeService)
    {
        $this->payVibeService = $payVibeService;
    }

    /**
     * Initialize PayVibe payment for promotion
     */
    public function initializePayment(Request $request, $slug)
    {
        $restaurant = \App\Models\Restaurant::where('slug', $slug)->firstOrFail();
        $payment = PromotionPayment::with(['promotionPlan', 'featuredRestaurant'])
            ->where('id', $request->payment_id)
            ->where('restaurant_id', $restaurant->id)
            ->firstOrFail();
        
        // Check if user can manage this restaurant
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        try {
            // Prepare order data for PayVibe
            $orderData = [
                'amount' => $payment->amount / 100, // Convert from kobo to naira
                'reference' => $payment->payment_reference,
                'email' => Auth::user()->email,
                'order_id' => $payment->id,
                'customer_name' => Auth::user()->name,
                'restaurant_id' => $restaurant->id,
                'payment_type' => 'promotion_payment'
            ];

            // Initialize payment with PayVibe
            $result = $this->payVibeService->initializePayment($orderData);

            if ($result['success']) {
                // Create PayVibe transaction record
                PayVibeTransaction::create([
                    'payment_id' => $payment->id,
                    'reference' => $payment->payment_reference,
                    'amount' => $payment->amount,
                    'status' => 'pending',
                    'authorization_url' => $result['authorization_url'],
                    'access_code' => $result['access_code'] ?? null,
                    'metadata' => [
                        'restaurant_id' => $restaurant->id,
                        'user_id' => Auth::id(),
                        'payment_type' => 'promotion_payment'
                    ]
                ]);

                Log::info('PayVibe payment initialized', [
                    'payment_id' => $payment->id,
                    'reference' => $payment->payment_reference,
                    'amount' => $payment->amount
                ]);

                return response()->json([
                    'success' => true,
                    'authorization_url' => $result['authorization_url'],
                    'reference' => $payment->payment_reference
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Payment initialization failed'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('PayVibe payment initialization error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment service temporarily unavailable'
            ], 500);
        }
    }

    /**
     * Generate virtual account for promotion payment
     */
    public function generateVirtualAccount(Request $request, $slug)
    {
        $restaurant = \App\Models\Restaurant::where('slug', $slug)->firstOrFail();
        $payment = PromotionPayment::with(['promotionPlan', 'featuredRestaurant'])
            ->where('id', $request->payment_id)
            ->where('restaurant_id', $restaurant->id)
            ->firstOrFail();
        
        // Check if user can manage this restaurant
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        try {
            // Prepare payment data for virtual account generation
            $paymentData = [
                'reference' => $payment->payment_reference
            ];

            // Generate virtual account with PayVibe
            $result = $this->payVibeService->generateVirtualAccount($paymentData);

            if ($result['success']) {
                // Create PayVibe transaction record
                PayVibeTransaction::create([
                    'payment_id' => $payment->id,
                    'reference' => $payment->payment_reference,
                    'amount' => $payment->amount,
                    'status' => 'pending',
                    'authorization_url' => null,
                    'access_code' => null,
                    'metadata' => [
                        'restaurant_id' => $restaurant->id,
                        'user_id' => Auth::id(),
                        'payment_type' => 'promotion_payment',
                        'virtual_account' => [
                            'account_number' => $result['account_number'],
                            'bank_name' => $result['bank_name'],
                            'account_name' => $result['account_name'],
                            'expires_at' => $result['expires_at']
                        ]
                    ]
                ]);

                Log::info('PayVibe virtual account generated', [
                    'payment_id' => $payment->id,
                    'reference' => $payment->payment_reference,
                    'account_number' => $result['account_number'],
                    'bank_name' => $result['bank_name']
                ]);

                return response()->json([
                    'success' => true,
                    'account_number' => $result['account_number'],
                    'bank_name' => $result['bank_name'],
                    'account_name' => $result['account_name'],
                    'reference' => $payment->payment_reference,
                    'amount' => $payment->formatted_amount,
                    'expires_at' => $result['expires_at']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Virtual account generation failed'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('PayVibe virtual account generation error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Virtual account service temporarily unavailable'
            ], 500);
        }
    }

    /**
     * Handle PayVibe webhook
     */
    public function webhook(Request $request)
    {
        // Retrieve JSON payload
        $payload = $request->json()->all();

        // Ensure required fields exist
        if (!isset($payload['data']) || !isset($payload['hash'])) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        // Retrieve access key securely
        $accessKey = env('PAYVIBE_ACCESS_KEY', 'your_default_access_key');

        // Compute expected hash
        $computedHash = hash_hmac('sha256', json_encode($payload['data']), $accessKey);
        
        // Verify hash
        if (!hash_equals($computedHash, $payload['hash'])) {
            Log::error('PayVibe webhook hash verification failed', [
                'received_hash' => $payload['hash'],
                'computed_hash' => $computedHash
            ]);
            return response()->json(['error' => 'Invalid authentication'], 400);
        }

        // Extract transaction details
        $data = $payload['data'];
        $reference = $data['reference'] ?? null;
        $amountReceived = $data['amount'] ?? 0;
        $status = strtolower($data['status'] ?? 'pending');

        // Define valid statuses
        $validStatuses = ['pending', 'successful', 'failed', 'reversed'];

        if (!in_array($status, $validStatuses)) {
            Log::error('PayVibe webhook invalid status', [
                'reference' => $reference,
                'status' => $status
            ]);
            return response()->json(['error' => 'Invalid status'], 400);
        }

        // Find payment transaction
        $payment = PromotionPayment::where('payment_reference', $reference)->lockForUpdate()->first();

        if (!$payment) {
            Log::error('PayVibe webhook payment not found', [
                'reference' => $reference
            ]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Prevent multiple processing of successful transactions
        if ($payment->status === 'paid' && $status === 'successful') {
            return response()->json(['message' => 'Payment already processed'], 200);
        }

        // Start database transaction
        DB::beginTransaction();

        try {
            if ($status === 'successful') {
                // Mark payment as paid
                $payment->markAsPaid('payvibe', [
                    'gateway_reference' => $data['gateway_ref'] ?? null,
                    'amount_received' => $amountReceived,
                    'webhook_data' => $data
                ]);

                // Update PayVibe transaction record
                $payVibeTransaction = PayVibeTransaction::where('reference', $reference)->first();
                if ($payVibeTransaction) {
                    $payVibeTransaction->update([
                        'status' => 'successful',
                        'gateway_reference' => $data['gateway_ref'] ?? null,
                        'amount_received' => $amountReceived,
                        'webhook_data' => $data
                    ]);
                }

                // Send webhook notification
                WebhookService::sendSuccessfulTransaction($payVibeTransaction, $payment->restaurant->user);

                Log::info('PayVibe payment successful', [
                    'payment_id' => $payment->id,
                    'reference' => $reference,
                    'amount' => $amountReceived
                ]);

            } elseif ($status === 'failed' || $status === 'reversed') {
                // Mark payment as failed
                $payment->update(['status' => 'failed']);

                // Update PayVibe transaction record
                $payVibeTransaction = PayVibeTransaction::where('reference', $reference)->first();
                if ($payVibeTransaction) {
                    $payVibeTransaction->update([
                        'status' => 'failed',
                        'webhook_data' => $data
                    ]);
                }

                // Send webhook notification
                WebhookService::sendFailedTransaction($payVibeTransaction, $payment->restaurant->user, "Payment {$status}");

                Log::info('PayVibe payment failed', [
                    'payment_id' => $payment->id,
                    'reference' => $reference,
                    'status' => $status
                ]);
            }

            // Commit transaction
            DB::commit();

            return response()->json(['message' => 'Payment processed successfully'], 200);

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            Log::error('PayVibe webhook processing error', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Processing error'], 500);
        }
    }

    /**
     * Verify payment status
     */
    public function verifyPayment($reference)
    {
        try {
            $result = $this->payVibeService->verifyPayment($reference);

            if ($result['success']) {
                // Update local payment status if needed
                $payment = PromotionPayment::where('payment_reference', $reference)->first();
                if ($payment && $result['status'] === 'successful' && $payment->status !== 'paid') {
                    $payment->markAsPaid('payvibe', [
                        'gateway_reference' => $result['gateway_ref'] ?? null,
                        'verified_at' => now()
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'status' => $result['status'],
                    'amount' => $result['amount']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('PayVibe payment verification error', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed'
            ], 500);
        }
    }

    /**
     * Payment callback (redirect from PayVibe)
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');
        $status = $request->query('status');

        if (!$reference) {
            return redirect()->route('dashboard')->with('error', 'Invalid payment reference');
        }

        $payment = PromotionPayment::where('payment_reference', $reference)->first();

        if (!$payment) {
            return redirect()->route('dashboard')->with('error', 'Payment not found');
        }

        // Verify payment status with PayVibe
        $result = $this->payVibeService->verifyPayment($reference);

        if ($result['success'] && $result['status'] === 'successful') {
            return redirect()->route('restaurant.promotions.payment.show', [
                'slug' => $payment->restaurant->slug,
                'paymentId' => $payment->id
            ])->with('success', 'Payment completed successfully!');
        } else {
            return redirect()->route('restaurant.promotions.payment.show', [
                'slug' => $payment->restaurant->slug,
                'paymentId' => $payment->id
            ])->with('error', 'Payment verification failed. Please contact support.');
        }
    }
} 