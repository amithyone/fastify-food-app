<?php

namespace App\Http\Controllers;

use App\Services\PayVibeService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $payVibeService;

    public function __construct(PayVibeService $payVibeService)
    {
        $this->payVibeService = $payVibeService;
    }

    /**
     * Initialize payment for an order
     */
    public function initialize(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:card'
        ]);

        try {
            $order = Order::findOrFail($request->order_id);
            
            // Check if user can access this order
            // Support both old system (user_id) and new system (created_by)
            $user = Auth::user();
            $canAccess = false;
            
            if (Auth::check()) {
                // User can access if:
                // 1. They own the order (user_id matches)
                // 2. They created the order (created_by matches) - if column exists
                // 3. They are a restaurant manager for this restaurant
                // 4. They are an admin
                // 5. It's a guest order (user_id = null)
                
                // Check if user owns the order (old system)
                if ($order->user_id === Auth::id()) {
                    $canAccess = true;
                }
                // Check if user created the order (new system) - only if column exists
                elseif (Schema::hasColumn('orders', 'created_by') && $order->created_by === Auth::id()) {
                    $canAccess = true;
                }
                // Check if it's a guest order (anyone can access)
                elseif ($order->user_id === null) {
                    $canAccess = true;
                }
                // Check if user is restaurant manager
                elseif ($user->isRestaurantOwner() && $user->primaryRestaurant && $order->restaurant_id === $user->primaryRestaurant->id) {
                    $canAccess = true;
                }
                // Check if user is admin
                elseif ($user->isAdmin()) {
                    $canAccess = true;
                }
            } else {
                // Guest users can only access guest orders
                $canAccess = ($order->user_id === null);
            }
            
            if (!$canAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this order. Order user_id: ' . ($order->user_id ?? 'null') . ', Your ID: ' . Auth::id()
                ], 403);
            }
            
            // Log for debugging
            Log::info('Payment authorization check', [
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

            // Check if order is already paid
            if ($order->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is already paid'
                ], 400);
            }

            // Prepare payment data
            $paymentData = [
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'reference' => $this->payVibeService->generateReference(),
                'email' => $request->email ?? 'customer@example.com',
                'customer_name' => $order->customer_name,
                'restaurant_id' => $order->restaurant_id
            ];

            // Initialize payment
            $result = $this->payVibeService->initializePayment($paymentData);

            if ($result['success']) {
                // Update order with payment reference
                $order->update([
                    'payment_reference' => $result['reference'],
                    'payment_method' => 'card'
                ]);

                return response()->json([
                    'success' => true,
                    'authorization_url' => $result['authorization_url'],
                    'reference' => $result['reference'],
                    'message' => 'Payment initialized successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Payment initialization failed'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Payment initialization error', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment service temporarily unavailable'
            ], 500);
        }
    }

    /**
     * Handle payment callback from PayVibe
     */
    public function callback(Request $request)
    {
        Log::info('PayVibe callback received', $request->all());

        try {
            $reference = $request->reference;
            
            if (!$reference) {
                Log::error('PayVibe callback missing reference');
                return response()->json(['error' => 'Missing reference'], 400);
            }

            // Find order by payment reference
            $order = Order::where('payment_reference', $reference)->first();
            
            if (!$order) {
                Log::error('PayVibe callback: Order not found', ['reference' => $reference]);
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Verify payment with PayVibe
            $verification = $this->payVibeService->verifyPayment($reference);

            if ($verification['success'] && $verification['status'] === 'success') {
                // Payment successful
                $order->update([
                    'status' => 'paid',
                    'payment_status' => 'completed',
                    'paid_at' => now(),
                    'gateway_reference' => $verification['gateway_ref'] ?? null
                ]);

                Log::info('Payment completed successfully', [
                    'order_id' => $order->id,
                    'reference' => $reference,
                    'amount' => $verification['amount']
                ]);

                // Redirect to success page
                return redirect()->route('payment.success', ['order_id' => $order->id]);
            } else {
                // Payment failed
                $order->update([
                    'status' => 'payment_failed',
                    'payment_status' => 'failed'
                ]);

                Log::error('Payment failed', [
                    'order_id' => $order->id,
                    'reference' => $reference,
                    'verification' => $verification
                ]);

                return redirect()->route('payment.failed', ['order_id' => $order->id]);
            }

        } catch (\Exception $e) {
            Log::error('PayVibe callback error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json(['error' => 'Callback processing failed'], 500);
        }
    }

    /**
     * Payment success page
     */
    public function success(Request $request)
    {
        $orderId = $request->order_id;
        $order = Order::findOrFail($orderId);

        return view('payment.success', compact('order'));
    }

    /**
     * Payment failed page
     */
    public function failed(Request $request)
    {
        $orderId = $request->order_id;
        $order = Order::findOrFail($orderId);

        return view('payment.failed', compact('order'));
    }

    /**
     * Verify payment status
     */
    public function verify(Request $request)
    {
        $request->validate([
            'reference' => 'required|string'
        ]);

        try {
            $verification = $this->payVibeService->verifyPayment($request->reference);

            return response()->json([
                'success' => true,
                'status' => $verification['status'] ?? 'unknown',
                'message' => $this->payVibeService->getStatusText($verification['status'] ?? 'unknown'),
                'data' => $verification
            ]);

        } catch (\Exception $e) {
            Log::error('Payment verification error', [
                'error' => $e->getMessage(),
                'reference' => $request->reference
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed'
            ], 500);
        }
    }
} 