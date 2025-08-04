<?php

namespace App\Http\Controllers;

use App\Models\BankTransferPayment;
use App\Models\Order;
use App\Models\User;
use App\Models\UserReward;
use App\Services\PayVibeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BankTransferPaymentController extends Controller
{
    protected $payVibeService;

    public function __construct(PayVibeService $payVibeService)
    {
        $this->payVibeService = $payVibeService;
    }

    /**
     * Initialize bank transfer payment
     */
    public function initialize(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:100'
        ]);

        $order = Order::findOrFail($request->order_id);
        
        // Check if user can pay for this order
        // Allow guest orders (user_id = null) to be paid by anyone
        // Allow authenticated users to pay for their own orders
        // Allow restaurant managers to access orders from their managed restaurants
        $user = Auth::user();
        $canAccess = false;
        
        if (Auth::check()) {
            // User can access if:
            // 1. They own the order (user_id matches)
            // 2. They are a restaurant manager for this restaurant
            // 3. They are an admin
            if ($order->user_id === Auth::id()) {
                $canAccess = true;
            } elseif ($user->isRestaurantOwner() && $user->primaryRestaurant && $order->restaurant_id === $user->primaryRestaurant->id) {
                $canAccess = true;
            } elseif ($user->isAdmin()) {
                $canAccess = true;
            } elseif ($order->user_id === null) {
                // Guest orders can be accessed by anyone
                $canAccess = true;
            }
        } else {
            // Guest users can only access guest orders
            $canAccess = ($order->user_id === null);
        }
        
        if (!$canAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this order'
            ], 403);
        }
        
        // Log for debugging
        Log::info('Bank transfer payment authorization check', [
            'order_id' => $order->id,
            'order_user_id' => $order->user_id,
            'order_restaurant_id' => $order->restaurant_id,
            'auth_check' => Auth::check(),
            'auth_id' => Auth::id(),
            'user_name' => $user ? $user->name : 'Guest',
            'user_is_manager' => $user ? $user->isRestaurantOwner() : false,
            'user_is_admin' => $user ? $user->isAdmin() : false,
            'user_restaurant_id' => $user && $user->primaryRestaurant ? $user->primaryRestaurant->id : null,
            'customer_name' => $order->customer_name,
            'order_number' => $order->order_number,
            'can_access' => $canAccess
        ]);

        // Check if payment already exists
        $existingPayment = BankTransferPayment::where('order_id', $order->id)
            ->whereIn('status', ['pending', 'partial'])
            ->first();

        if ($existingPayment) {
            return response()->json([
                'success' => false,
                'message' => 'A payment is already in progress for this order'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Get user ID safely
            $userId = null;
            if (Auth::check()) {
                $user = Auth::user();
                if ($user) {
                    $userId = $user->id;
                    Log::info('User authenticated for bank transfer payment', [
                        'user_id' => $userId,
                        'user_name' => $user->name,
                        'user_email' => $user->email
                    ]);
                }
            }

            // Create bank transfer payment record
            $payment = BankTransferPayment::create([
                'order_id' => $order->id,
                'user_id' => $userId,
                'payment_reference' => BankTransferPayment::generatePaymentReference(),
                'amount' => $request->amount,
                'amount_paid' => 0,
                'amount_remaining' => $request->amount,
                'status' => 'pending',
                'reward_points_rate' => 1, // 1 point per ₦100
                'reward_points_threshold' => 100, // ₦100 per point
                'service_charge_rate' => 2.00, // 2% service charge
                'service_charge_amount' => ($request->amount * 2) / 100,
                'expires_at' => now()->addHours(24),
                'payment_instructions' => null // Will be set after PayVibe response
            ]);

            // Generate PayVibe virtual account
            $payVibeResponse = $this->payVibeService->generateVirtualAccount([
                'reference' => $payment->payment_reference
            ]);

            if ($payVibeResponse['success']) {
                $payment->update([
                    'virtual_account_number' => $payVibeResponse['account_number'],
                    'bank_name' => $payVibeResponse['bank_name'],
                    'account_name' => $payVibeResponse['account_name']
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Bank transfer payment initialized successfully',
                    'data' => [
                        'payment_id' => $payment->id,
                        'payment_reference' => $payment->payment_reference,
                        'amount' => $payment->amount,
                        'virtual_account_number' => $payment->virtual_account_number,
                        'bank_name' => $payment->bank_name,
                        'account_name' => $payment->account_name,
                        'payment_instructions' => $payment->payment_instructions,
                        'expires_at' => $payment->expires_at->toISOString(),
                        'reward_points_rate' => $payment->reward_points_rate,
                        'reward_points_threshold' => $payment->reward_points_threshold,
                        'service_charge_rate' => $payment->service_charge_rate,
                        'service_charge_amount' => $payment->service_charge_amount
                    ]
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate virtual account: ' . $payVibeResponse['message']
                ], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bank transfer payment initialization failed', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize bank transfer payment'
            ], 500);
        }
    }

    /**
     * Handle PayVibe webhook for payment confirmation
     */
    public function webhook(Request $request)
    {
        Log::info('PayVibe webhook received', $request->all());

        // Verify webhook signature (implement based on PayVibe documentation)
        // $signature = $request->header('X-PayVibe-Signature');
        // if (!$this->verifyWebhookSignature($request->getContent(), $signature)) {
        //     return response()->json(['error' => 'Invalid signature'], 400);
        // }

        // Extract data from PayVibe payload structure
        $reference = $request->input('reference');
        $netAmount = $request->input('net_amount'); // Amount after bank charges
        $bankCharge = $request->input('bank_charge');
        $creditedAt = $request->input('credited_at');
        $platformFee = $request->input('platform_fee');
        $settledAmount = $request->input('settled_amount');
        $platformProfit = $request->input('platform_profit');
        $transactionAmount = $request->input('transaction_amount');

        if (!$reference || !$netAmount) {
            Log::error('PayVibe webhook missing required parameters', $request->all());
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $payment = BankTransferPayment::where('payment_reference', $reference)->first();

        if (!$payment) {
            Log::warning('Payment not found for reference', ['reference' => $reference]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if ($payment->isCompleted()) {
            Log::info('Payment already completed', ['reference' => $reference]);
            return response()->json(['message' => 'Payment already completed'], 200);
        }

        DB::beginTransaction();
        try {
            // Use net_amount as the actual amount received (after bank charges)
            $amountReceived = $netAmount;
            
            // Update payment with received amount
            $payment->updatePayment($amountReceived);
            
            // Store PayVibe-specific data
            $payment->update([
                'payvibe_net_amount' => $netAmount,
                'payvibe_bank_charge' => $bankCharge,
                'payvibe_platform_fee' => $platformFee,
                'payvibe_settled_amount' => $settledAmount,
                'payvibe_platform_profit' => $platformProfit,
                'payvibe_transaction_amount' => $transactionAmount,
                'payvibe_credited_at' => $creditedAt ? \Carbon\Carbon::parse($creditedAt) : now()
            ]);

            // Log detailed payment information
            Log::info('PayVibe payment details', [
                'reference' => $reference,
                'net_amount' => $netAmount,
                'bank_charge' => $bankCharge,
                'platform_fee' => $platformFee,
                'settled_amount' => $settledAmount,
                'platform_profit' => $platformProfit,
                'transaction_amount' => $transactionAmount,
                'credited_at' => $creditedAt,
                'amount_received' => $amountReceived,
                'payment_status' => $payment->status
            ]);

            // If payment is completed, update order and add reward points
            if ($payment->isCompleted()) {
                $order = $payment->order;
                if ($order) {
                    $order->update([
                        'payment_status' => 'paid',
                        'paid_at' => $creditedAt ? \Carbon\Carbon::parse($creditedAt) : now()
                    ]);
                }

                // Add reward points to user
                if ($payment->user_id && $payment->reward_points_earned > 0) {
                    UserReward::create([
                        'user_id' => $payment->user_id,
                        'restaurant_id' => $order->restaurant_id,
                        'points' => $payment->reward_points_earned,
                        'type' => 'bank_transfer',
                        'description' => "Earned {$payment->reward_points_earned} points for bank transfer payment",
                        'order_id' => $order->id
                    ]);
                }

                Log::info('Payment completed successfully', [
                    'reference' => $reference,
                    'order_id' => $order->id ?? null,
                    'reward_points' => $payment->reward_points_earned
                ]);
            } else {
                Log::info('Partial payment received', [
                    'reference' => $reference,
                    'amount_paid' => $payment->amount_paid,
                    'amount_remaining' => $payment->amount_remaining
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Payment processed successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing failed', [
                'error' => $e->getMessage(),
                'reference' => $reference,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Payment processing failed'], 500);
        }
    }

    /**
     * Get payment status
     */
    public function status($paymentId)
    {
        $payment = BankTransferPayment::findOrFail($paymentId);
        
        // Check if user can view this payment
        if (Auth::check() && $payment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this payment'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'payment_id' => $payment->id,
                'payment_reference' => $payment->payment_reference,
                'amount' => $payment->amount,
                'amount_paid' => $payment->amount_paid,
                'amount_remaining' => $payment->amount_remaining,
                'status' => $payment->status,
                'reward_points_earned' => $payment->reward_points_earned,
                'service_charge_amount' => $payment->service_charge_amount,
                'expires_at' => $payment->expires_at->toISOString(),
                'paid_at' => $payment->paid_at?->toISOString(),
                'time_remaining' => $payment->time_remaining
            ]
        ]);
    }

    /**
     * Generate new virtual account for partial payment
     */
    public function generateNewAccount(Request $request, $paymentId)
    {
        $payment = BankTransferPayment::findOrFail($paymentId);
        
        // Check if user can access this payment
        if (Auth::check() && $payment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this payment'
            ], 403);
        }

        if (!$payment->isPartial()) {
            return response()->json([
                'success' => false,
                'message' => 'Payment is not in partial status'
            ], 400);
        }

        // Calculate remaining amount with service charge
        $remainingAmount = $payment->amount_remaining;
        $serviceCharge = $payment->calculateServiceCharge($remainingAmount);
        $totalAmount = $remainingAmount + $serviceCharge;

        // Generate new payment reference
        $newReference = BankTransferPayment::generatePaymentReference();

        // Generate new virtual account
        $payVibeResponse = $this->payVibeService->generateVirtualAccount([
            'reference' => $newReference
        ]);

        if (!$payVibeResponse['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate new virtual account'
            ], 500);
        }

        // Create new payment record for remaining amount
        $newPayment = BankTransferPayment::create([
            'order_id' => $payment->order_id,
            'user_id' => $payment->user_id,
            'payment_reference' => $newReference,
            'amount' => $totalAmount,
            'amount_paid' => 0,
            'amount_remaining' => $totalAmount,
            'virtual_account_number' => $payVibeResponse['account_number'],
            'bank_name' => $payVibeResponse['bank_name'],
            'account_name' => $payVibeResponse['account_name'],
            'status' => 'pending',
            'reward_points_rate' => $payment->reward_points_rate,
            'reward_points_threshold' => $payment->reward_points_threshold,
            'service_charge_rate' => $payment->service_charge_rate,
            'service_charge_amount' => $serviceCharge,
            'expires_at' => now()->addHours(24),
            'payment_instructions' => "Please pay the remaining amount of ₦{$totalAmount} to complete your order. A {$payment->service_charge_rate}% service charge has been applied to the remaining balance."
        ]);

        return response()->json([
            'success' => true,
            'message' => 'New virtual account generated for remaining payment',
            'data' => [
                'payment_id' => $newPayment->id,
                'payment_reference' => $newPayment->payment_reference,
                'amount_remaining' => $remainingAmount,
                'service_charge_amount' => $serviceCharge,
                'total_amount' => $totalAmount,
                'virtual_account_number' => $newPayment->virtual_account_number,
                'bank_name' => $newPayment->bank_name,
                'account_name' => $newPayment->account_name,
                'expires_at' => $newPayment->expires_at->toISOString()
            ]
        ]);
    }

    /**
     * Get user's bank transfer payments
     */
    public function userPayments()
    {
        $payments = BankTransferPayment::where('user_id', Auth::id())
            ->with(['order'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /**
     * Verify webhook signature (implement based on PayVibe documentation)
     */
    private function verifyWebhookSignature($payload, $signature)
    {
        // Implement signature verification based on PayVibe documentation
        // This is a placeholder - implement according to PayVibe's webhook security requirements
        return true;
    }
}
