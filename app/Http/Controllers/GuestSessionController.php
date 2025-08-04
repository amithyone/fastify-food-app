<?php

namespace App\Http\Controllers;

use App\Models\GuestSession;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GuestSessionController extends Controller
{
    /**
     * Create a new guest session
     */
    public function create(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'table_qr_id' => 'required|exists:table_q_r_s,id',
            'table_number' => 'required|string',
            'cart_data' => 'nullable|array',
            'customer_info' => 'nullable|array'
        ]);

        try {
            $session = GuestSession::create([
                'restaurant_id' => $request->restaurant_id,
                'table_qr_id' => $request->table_qr_id,
                'session_id' => GuestSession::generateSessionId(),
                'table_number' => $request->table_number,
                'cart_data' => $request->cart_data,
                'customer_info' => $request->customer_info,
                'expires_at' => now()->addHours(24),
                'is_active' => true
            ]);

            Log::info('Guest session created', [
                'session_id' => $session->session_id,
                'restaurant_id' => $session->restaurant_id,
                'table_number' => $session->table_number
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Guest session created successfully',
                'data' => [
                    'session_id' => $session->session_id,
                    'expires_at' => $session->expires_at->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create guest session', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create guest session'
            ], 500);
        }
    }

    /**
     * Get guest session details and orders
     */
    public function show(Request $request, $sessionId)
    {
        $session = GuestSession::getActiveSession($sessionId);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Guest session not found or expired'
            ], 404);
        }

        // Get orders for this session
        $orders = Order::where('session_id', $sessionId)
            ->with(['orderItems.menuItem', 'restaurant'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'session' => [
                    'session_id' => $session->session_id,
                    'table_number' => $session->table_number,
                    'restaurant' => $session->restaurant->name,
                    'expires_at' => $session->expires_at->toISOString(),
                    'is_expired' => $session->isExpired()
                ],
                'orders' => $orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'total_amount' => $order->total_amount,
                        'status' => $order->status,
                        'created_at' => $order->created_at->toISOString(),
                        'items' => $order->orderItems->map(function ($item) {
                            return [
                                'name' => $item->menuItem->name,
                                'quantity' => $item->quantity,
                                'price' => $item->price
                            ];
                        })
                    ];
                })
            ]
        ]);
    }

    /**
     * Get orders for a guest session
     */
    public function orders(Request $request, $sessionId)
    {
        $session = GuestSession::getActiveSession($sessionId);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Guest session not found or expired'
            ], 404);
        }

        $orders = Order::where('session_id', $sessionId)
            ->with(['orderItems.menuItem', 'restaurant'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'session_id' => $sessionId,
                'orders' => $orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'total_amount' => $order->total_amount,
                        'status' => $order->status,
                        'payment_method' => $order->payment_method,
                        'created_at' => $order->created_at->toISOString(),
                        'items' => $order->orderItems->map(function ($item) {
                            return [
                                'name' => $item->menuItem->name,
                                'quantity' => $item->quantity,
                                'price' => $item->price
                            ];
                        })
                    ];
                })
            ]
        ]);
    }

    /**
     * Extend guest session
     */
    public function extend(Request $request, $sessionId)
    {
        $session = GuestSession::getActiveSession($sessionId);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Guest session not found or expired'
            ], 404);
        }

        $session->extendSession(24); // Extend by 24 hours

        return response()->json([
            'success' => true,
            'message' => 'Guest session extended successfully',
            'data' => [
                'session_id' => $session->session_id,
                'expires_at' => $session->expires_at->toISOString()
            ]
        ]);
    }

    /**
     * End guest session
     */
    public function end(Request $request, $sessionId)
    {
        $session = GuestSession::where('session_id', $sessionId)->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Guest session not found'
            ], 404);
        }

        $session->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Guest session ended successfully'
        ]);
    }
}
