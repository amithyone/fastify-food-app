<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\UserReward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Restaurant;

class OrderController extends Controller
{
    public function cart()
    {
        return view('cart.index');
    }

    public function checkout()
    {
        $cart = Session::get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $restaurantId => $items) {
            $restaurant = Restaurant::find($restaurantId);
            if ($restaurant) {
                $restaurantItems = [];
                $restaurantTotal = 0;

                foreach ($items as $itemId => $quantity) {
                    $menuItem = MenuItem::find($itemId);
                    if ($menuItem && $menuItem->is_available) {
                        $itemTotal = $menuItem->price * $quantity;
                        $restaurantItems[] = [
                            'id' => $menuItem->id,
                            'name' => $menuItem->name,
                            'price' => $menuItem->price,
                            'quantity' => $quantity,
                            'total' => $itemTotal,
                            'image' => $menuItem->image
                        ];
                        $restaurantTotal += $itemTotal;
                    }
                }

                if (!empty($restaurantItems)) {
                    $cartItems[] = [
                        'restaurant' => $restaurant,
                        'items' => $restaurantItems,
                        'total' => $restaurantTotal
                    ];
                    $total += $restaurantTotal;
                }
            }
        }

        // Ensure cartItems is always an array
        if (!is_array($cartItems)) {
            $cartItems = [];
        }

        return view('checkout.index', compact('cartItems', 'total'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'integer|min:1'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart'
        ]);
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'quantity' => 'required|integer|min:0'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated'
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_info' => 'required|array',
            'customer_info.in_restaurant' => 'required|boolean',
            'payment_method' => 'required|in:cash,card,transfer,wallet',
            'items' => 'required|array|min:1',
            'subtotal' => 'required|numeric|min:0',
            'delivery_fee' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        // Additional validation for delivery orders
        if (!$request->customer_info['in_restaurant']) {
            $request->validate([
                'customer_info.name' => 'required|string|max:255',
                'customer_info.phone' => 'required|string|max:20',
                'customer_info.address' => 'required|string',
                'customer_info.city' => 'required|string|max:100',
                'customer_info.state' => 'required|string|max:100',
            ]);
        } else {
            // Additional validation for restaurant orders
            $request->validate([
                'customer_info.table_number' => 'required|string|max:50',
            ]);
        }

        try {
            DB::beginTransaction();

            // Get restaurant_id from the first cart item (all items in cart should be from same restaurant)
            $restaurantId = null;
            if (!empty($request->items)) {
                // Find the menu item to get its restaurant_id
                $firstMenuItem = MenuItem::find($request->items[0]['id']);
                if ($firstMenuItem) {
                    $restaurantId = $firstMenuItem->restaurant_id;
                }
            }

            if (!$restaurantId) {
                throw new \Exception('No restaurant found for order items');
            }

            // Calculate total from items
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Add delivery fee
            $total += $request->delivery_fee;

            // Prepare customer info
            $customerInfo = $request->customer_info;
            $customerName = $customerInfo['in_restaurant'] ? 'Restaurant Customer' : $customerInfo['name'];
            $phoneNumber = $customerInfo['in_restaurant'] ? 'N/A' : $customerInfo['phone'];
            $deliveryAddress = $customerInfo['in_restaurant'] ? 
                'Table: ' . $customerInfo['table_number'] : 
                $customerInfo['address'] . ', ' . $customerInfo['city'] . ', ' . $customerInfo['state'];
            
            // Prepare notes
            $notes = 'Payment Method: ' . ucfirst($request->payment_method) . 
                    ($customerInfo['in_restaurant'] ? ' | In Restaurant' : ' | Delivery');
            
            if ($customerInfo['in_restaurant'] && !empty($customerInfo['restaurant_notes'])) {
                $notes .= ' | Notes: ' . $customerInfo['restaurant_notes'];
            } elseif (!$customerInfo['in_restaurant'] && !empty($customerInfo['instructions'])) {
                $notes .= ' | Instructions: ' . $customerInfo['instructions'];
            }

            // Create order
            $order = Order::create([
                'restaurant_id' => $restaurantId,
                'customer_name' => $customerName,
                'phone_number' => $phoneNumber,
                'delivery_address' => $deliveryAddress,
                'allergies' => $customerInfo['instructions'] ?? null, // Re-purposed for delivery instructions
                'delivery_time' => $customerInfo['in_restaurant'] ? 'In Restaurant' : 'ASAP',
                'total_amount' => $total,
                'status' => 'pending',
                'notes' => $notes,
            ]);

            // Create order items
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                ]);
            }

                    // Handle reward points for bank transfer payments
                    if ($request->payment_method === 'transfer' && Auth::check()) {
                        $user = Auth::user();
                        $wallet = $user->getWalletOrCreate();
                        
                        // Calculate reward points (1 point per ₦100 spent)
                        $pointsEarned = (int) ($total / 100);
                        
                        if ($pointsEarned > 0) {
                            // Create reward record
                            \App\Models\UserReward::create([
                                'user_id' => $user->id,
                                'order_id' => $order->id,
                                'points_earned' => $pointsEarned,
                                'order_amount' => $total,
                                'payment_method' => 'transfer',
                                'status' => 'credited',
                                'credited_at' => now(),
                                'expires_at' => now()->addMonths(6)
                            ]);

                            // Credit points to wallet immediately
                            $wallet->credit(0, $pointsEarned, "Reward points from order #{$order->id}", $order->id);
                        }
                    }

                    // Handle wallet payments
                    if ($request->payment_method === 'wallet' && Auth::check()) {
                        $user = Auth::user();
                        $wallet = $user->getWalletOrCreate();
                        
                        // Check if user has sufficient balance
                        if ($wallet->balance < $total) {
                            throw new \Exception('Insufficient wallet balance');
                        }
                        
                        // Debit the amount from wallet
                        $wallet->debit($total, "Payment for order #{$order->id}", $order->id);
                        
                        // Update order payment status
                        $order->update([
                            'payment_status' => 'paid'
                        ]);
                    }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_number' => $order->order_number ?? 'ORD-' . $order->id,
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Order creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order. Please try again. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $order = Order::with('orderItems.menuItem')->findOrFail($id);
        
        // Check if user owns this order or is admin
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }
        
        return view('orders.show', compact('order'));
    }

    public function index()
    {
        if (Auth::check()) {
            // For authenticated users, show their orders
            $orders = Order::with('orderItems.menuItem')
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            // For guests, show all orders (admin view)
        $orders = Order::with('orderItems.menuItem')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        }
        
        return view('orders.index', compact('orders'));
    }

    public function userOrders()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $orders = Order::with('orderItems.menuItem')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('orders.user-orders', compact('orders'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully!'
        ]);
    }

    public function getOrderStatus($id)
    {
        $order = Order::findOrFail($id);
        
        // Check if user owns this order
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }
        
        return response()->json([
            'success' => true,
            'order' => $order,
            'status_info' => $this->getStatusInfo($order->status)
        ]);
    }

    private function getStatusInfo($status)
    {
        $statuses = [
            'pending' => [
                'title' => 'Order Pending',
                'description' => 'Your order has been received and is being reviewed.',
                'icon' => 'fas fa-clock',
                'color' => 'text-yellow-500',
                'bg_color' => 'bg-yellow-100',
                'step' => 1
            ],
            'confirmed' => [
                'title' => 'Order Confirmed',
                'description' => 'Your order has been confirmed and is being prepared.',
                'icon' => 'fas fa-check-circle',
                'color' => 'text-blue-500',
                'bg_color' => 'bg-blue-100',
                'step' => 2
            ],
            'preparing' => [
                'title' => 'Preparing Your Order',
                'description' => 'Our chefs are preparing your delicious meal.',
                'icon' => 'fas fa-utensils',
                'color' => 'text-orange-500',
                'bg_color' => 'bg-orange-100',
                'step' => 3
            ],
            'ready' => [
                'title' => 'Order Ready',
                'description' => 'Your order is ready and will be delivered soon.',
                'icon' => 'fas fa-check-double',
                'color' => 'text-green-500',
                'bg_color' => 'bg-green-100',
                'step' => 4
            ],
            'delivered' => [
                'title' => 'Order Delivered',
                'description' => 'Your order has been successfully delivered.',
                'icon' => 'fas fa-truck',
                'color' => 'text-green-600',
                'bg_color' => 'bg-green-100',
                'step' => 5
            ],
            'cancelled' => [
                'title' => 'Order Cancelled',
                'description' => 'Your order has been cancelled.',
                'icon' => 'fas fa-times-circle',
                'color' => 'text-red-500',
                'bg_color' => 'bg-red-100',
                'step' => 0
            ]
        ];

        return $statuses[$status] ?? $statuses['pending'];
    }
}
