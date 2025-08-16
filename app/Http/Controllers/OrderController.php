<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\UserReward;
use App\Models\TableQR;
use App\Models\User;
use App\Services\PaymentCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use App\Models\Restaurant;

class OrderController extends Controller
{
    protected $paymentCalculationService;

    public function __construct(PaymentCalculationService $paymentCalculationService)
    {
        $this->paymentCalculationService = $paymentCalculationService;
    }

    public function cart()
    {
        return view('cart.index');
    }

    public function checkout()
    {
        $cart = Session::get('cart', []);
        $cartItems = [];
        $total = 0;

        // Log cart data for debugging
        \Log::info('Checkout cart data:', [
            'session_cart' => $cart,
            'session_id' => session()->getId(),
            'auth_check' => Auth::check(),
            'user_id' => Auth::id()
        ]);

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
                            'image' => $menuItem->image,
                            'is_available_for_delivery' => $menuItem->is_available_for_delivery
                        ];
                        $restaurantTotal += $itemTotal;
                    }
                }

                if (!empty($restaurantItems)) {
                    // Get restaurant delivery settings
                    $deliverySetting = $restaurant->deliverySetting;
                    
                    $cartItems[] = [
                        'restaurant' => $restaurant,
                        'items' => $restaurantItems,
                        'total' => $restaurantTotal,
                        'delivery_setting' => $deliverySetting
                    ];
                    $total += $restaurantTotal;
                }
            }
        }

        // Ensure cartItems is always an array
        if (!is_array($cartItems)) {
            $cartItems = [];
        }

        // Check if cart is empty and redirect if necessary
        if (empty($cartItems)) {
            \Log::warning('Checkout accessed with empty cart', [
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'cart_data' => $cart
            ]);
            
            // For guest users, don't redirect immediately - let them see the checkout page
            // The JavaScript will handle the empty cart validation
            if (Auth::check()) {
                return redirect()->route('home')->with('error', 'Your cart is empty. Please add some items before proceeding to checkout.');
            }
        }

        // Get QR code information from session
        $qrTableNumber = session('qr_table_number');
        $qrRestaurantId = session('qr_restaurant_id');
        $qrTableQrId = session('qr_table_qr_id');

        return view('checkout.index', compact('cartItems', 'total', 'qrTableNumber', 'qrRestaurantId', 'qrTableQrId'));
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
        // Debug: Log the incoming request data
        \Log::info('=== ORDER CREATION START ===');
        \Log::info('Order creation request received:', [
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'request_headers' => $request->headers->all(),
            'request_body' => $request->all(),
            'items' => $request->items,
            'items_count' => is_array($request->items) ? count($request->items) : 'not_array',
            'customer_info' => $request->customer_info,
            'payment_method' => $request->payment_method,
            'subtotal' => $request->subtotal,
            'delivery_fee' => $request->delivery_fee,
            'total' => $request->total,
            'auth_check' => Auth::check(),
            'auth_user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'session_cart' => Session::get('cart', [])
        ]);
        
        \Log::info('Starting validation...');
        
        // Check if items are empty and try to get them from session
        $items = $request->items;
        if (empty($items) || !is_array($items) || count($items) === 0) {
            \Log::warning('Items array is empty, attempting to get from session cart');
            
            $sessionCart = Session::get('cart', []);
            $items = [];
            
            foreach ($sessionCart as $restaurantId => $cartItems) {
                foreach ($cartItems as $itemId => $quantity) {
                    $menuItem = MenuItem::find($itemId);
                    if ($menuItem && $menuItem->is_available) {
                        $items[] = [
                            'id' => $menuItem->id,
                            'name' => $menuItem->name,
                            'price' => $menuItem->price,
                            'quantity' => $quantity
                        ];
                    }
                }
            }
            
            \Log::info('Items from session cart:', [
                'session_cart' => $sessionCart,
                'processed_items' => $items,
                'items_count' => count($items)
            ]);
            
            // If we still don't have items, proceed with validation
            if (empty($items)) {
                try {
                    $request->validate([
                        'customer_info' => 'required|array',
                        'customer_info.order_type' => 'required|in:delivery,pickup,restaurant,in_restaurant',
                        'payment_method' => 'required|in:cash,card,transfer,wallet',
                        'items' => 'required|array|min:1',
                        'subtotal' => 'required|numeric|min:0',
                        'delivery_fee' => 'required|numeric|min:0',
                        'total' => 'required|numeric|min:0',
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    \Log::error('Validation failed:', [
                        'errors' => $e->errors(),
                        'request_data' => $request->all()
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Please ensure you have items in your cart before placing an order.',
                        'errors' => $e->errors()
                    ], 422);
                }
            } else {
                \Log::info('Successfully retrieved items from session cart');
            }
        } else {
            try {
                $request->validate([
                    'customer_info' => 'required|array',
                    'customer_info.order_type' => 'required|in:delivery,pickup,restaurant,in_restaurant',
                    'payment_method' => 'required|in:cash,card,transfer,wallet',
                    'items' => 'required|array|min:1',
                    'subtotal' => 'required|numeric|min:0',
                    'delivery_fee' => 'required|numeric|min:0',
                    'total' => 'required|numeric|min:0',
                ]);
                \Log::info('Basic validation passed successfully');
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Validation failed:', [
                    'errors' => $e->errors(),
                    'request_data' => $request->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Please ensure you have items in your cart before placing an order.',
                    'errors' => $e->errors()
                ], 422);
            }
        }

        // Additional validation based on order type
        $orderType = $request->customer_info['order_type'] ?? 'delivery';
        \Log::info('Order type validation:', ['order_type' => $orderType, 'customer_info' => $request->customer_info]);
        
        try {
            if ($orderType === 'delivery') {
                \Log::info('Validating delivery order...');
                $request->validate([
                    'customer_info.name' => 'required|string|max:255',
                    'customer_info.phone' => 'required|string|max:20',
                    'customer_info.address' => 'required|string',
                    'customer_info.city' => 'required|string|max:100',
                    'customer_info.state' => 'required|string|max:100',
                ]);
                \Log::info('Delivery validation passed');
            } elseif ($orderType === 'pickup') {
                \Log::info('Validating pickup order...');
                $request->validate([
                    'customer_info.pickup_name' => 'required|string|max:255',
                    'customer_info.pickup_phone' => 'required|string|max:20',
                    'customer_info.pickup_time' => 'required|string',
                ]);
                \Log::info('Pickup validation passed');
            } elseif ($orderType === 'restaurant' || $orderType === 'in_restaurant') {
                \Log::info('Validating restaurant order...');
                $request->validate([
                    'customer_info.table_number' => 'required|string|max:50',
                ]);
                \Log::info('Restaurant validation passed');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Order type validation failed:', [
                'order_type' => $orderType,
                'errors' => $e->errors(),
                'customer_info' => $request->customer_info
            ]);
            throw $e;
        }

        try {
            \Log::info('Starting database transaction...');
            DB::beginTransaction();
            
            \Log::info('Database transaction started successfully');

            // Get restaurant_id from the first cart item (all items in cart should be from same restaurant)
            $restaurantId = null;
            \Log::info('Looking up restaurant ID from items:', ['items_count' => count($items)]);
            
            if (!empty($items)) {
                // Find the menu item to get its restaurant_id
                $firstItem = $items[0];
                \Log::info('First item data:', $firstItem);
                
                $firstMenuItem = MenuItem::find($firstItem['id']);
                if ($firstMenuItem) {
                    $restaurantId = $firstMenuItem->restaurant_id;
                    \Log::info('Found restaurant ID:', ['restaurant_id' => $restaurantId, 'menu_item' => $firstMenuItem->toArray()]);
                } else {
                    \Log::error('Menu item not found:', ['item_id' => $firstItem['id']]);
                }
            } else {
                \Log::error('No items in request');
            }

            if (!$restaurantId) {
                \Log::error('No restaurant found for order items');
                throw new \Exception('No restaurant found for order items');
            }

            // Calculate subtotal from items
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            // Calculate charges using payment calculation service
            $orderData = [
                'subtotal' => $subtotal,
                'payment_method' => $request->payment_method
            ];

            $charges = $this->paymentCalculationService->calculateOrderCharges($subtotal, $orderData);
            
            // Log for debugging
            \Log::info('Order charges calculation:', [
                'subtotal' => $subtotal,
                'charges' => $charges,
                'frontend_total' => $request->total
            ]);

            // Prepare customer info
            $customerInfo = $request->customer_info;
            $orderType = $customerInfo['order_type'] ?? 'delivery';
            
            \Log::info('Preparing customer info:', ['order_type' => $orderType, 'customer_info' => $customerInfo]);
            
            // Determine customer name and phone based on order type
            if ($orderType === 'pickup') {
                $customerName = $customerInfo['pickup_name'] ?? $customerInfo['name'] ?? 'Pickup Customer';
                $phoneNumber = $customerInfo['pickup_phone'] ?? $customerInfo['phone'] ?? 'N/A';
                $deliveryAddress = 'Pickup at Restaurant';
            } elseif ($orderType === 'restaurant' || $orderType === 'in_restaurant') {
                $customerName = 'Restaurant Customer';
                $phoneNumber = 'N/A';
                $deliveryAddress = 'Table: ' . ($customerInfo['table_number'] ?? 'N/A');
            } else {
                $customerName = $customerInfo['name'] ?? 'Delivery Customer';
                $phoneNumber = $customerInfo['phone'] ?? 'N/A';
                $deliveryAddress = $customerInfo['address'] . ', ' . $customerInfo['city'] . ', ' . $customerInfo['state'];
            }
            
            \Log::info('Customer info prepared:', [
                'customer_name' => $customerName,
                'phone_number' => $phoneNumber,
                'delivery_address' => $deliveryAddress
            ]);
            
            // Prepare notes
            $notes = 'Payment Method: ' . ucfirst($request->payment_method) . ' | ' . ucfirst($orderType);
            
            if ($orderType === 'pickup' && !empty($customerInfo['pickup_notes'])) {
                $notes .= ' | Pickup Notes: ' . $customerInfo['pickup_notes'];
            } elseif ($orderType === 'restaurant' && !empty($customerInfo['restaurant_notes'])) {
                $notes .= ' | Notes: ' . $customerInfo['restaurant_notes'];
            } elseif ($orderType === 'delivery' && !empty($customerInfo['instructions'])) {
                $notes .= ' | Instructions: ' . $customerInfo['instructions'];
            }

            // Calculate pickup time
            $pickupTime = null;
            if ($orderType === 'pickup') {
                $pickupTimeOption = $customerInfo['pickup_time'] ?? 'asap';
                switch ($pickupTimeOption) {
                    case 'asap':
                        $pickupTime = now()->addMinutes(20);
                        break;
                    case '30min':
                        $pickupTime = now()->addMinutes(30);
                        break;
                    case '1hour':
                        $pickupTime = now()->addHour();
                        break;
                    case 'custom':
                        $pickupTime = $customerInfo['custom_pickup_datetime'] ? 
                            \Carbon\Carbon::parse($customerInfo['custom_pickup_datetime']) : 
                            now()->addMinutes(20);
                        break;
                    default:
                        $pickupTime = now()->addMinutes(20);
                }
            }

            // Get user ID safely
            $userId = null;
            if (Auth::check()) {
                $user = Auth::user();
                if ($user) {
                    $userId = $user->id;
                    \Log::info('User authenticated for order creation', [
                        'user_id' => $userId,
                        'user_name' => $user->name,
                        'user_email' => $user->email
                    ]);
                }
            }
            
            \Log::info('Creating order with data:', [
                'restaurant_id' => $restaurantId,
                'user_id' => $userId,
                'auth_check' => Auth::check(),
                'auth_id' => Auth::id(),
                'customer_name' => $customerName,
                'phone_number' => $phoneNumber,
                'delivery_address' => $deliveryAddress,
                'order_type' => $orderType === 'restaurant' ? 'in_restaurant' : $orderType,
                'total_amount' => $charges['total'],
                'status' => $request->payment_method === 'transfer' ? 'pending_payment' : 'pending'
            ]);

            // Get session ID for guest users
            $sessionId = null;
            if (!Auth::check() && $request->has('session_id')) {
                $sessionId = $request->session_id;
            }

            // Create order with calculated charges
            $orderData = [
                'restaurant_id' => $restaurantId,
                'user_id' => $userId, // Will be null for guest users
                'session_id' => $sessionId, // For guest session tracking
                'order_number' => (new Order())->generateOrderNumber(),
                'customer_name' => $customerName,
                'phone_number' => $phoneNumber,
                'delivery_address' => $deliveryAddress,
                'allergies' => $customerInfo['instructions'] ?? null, // Re-purposed for delivery instructions
                'delivery_time' => $orderType === 'restaurant' ? 'In Restaurant' : 'ASAP',
                'order_type' => $orderType === 'restaurant' ? 'in_restaurant' : $orderType,
                'pickup_code' => $orderType === 'pickup' ? strtoupper(substr(md5(uniqid()), 0, 6)) : null,
                'pickup_time' => $pickupTime,
                'pickup_name' => $orderType === 'pickup' ? ($customerInfo['pickup_name'] ?? $customerName) : null,
                'pickup_phone' => $orderType === 'pickup' ? ($customerInfo['pickup_phone'] ?? $phoneNumber) : null,
                'subtotal' => $charges['subtotal'],
                'service_charge' => $charges['service_charge'],
                'tax_amount' => $charges['tax_amount'],
                'delivery_fee' => $charges['delivery_fee'],
                'discount_amount' => $charges['discount_amount'],
                'charge_breakdown' => $charges['breakdown'],
                'total_amount' => $charges['total'],
                'status' => $request->payment_method === 'transfer' ? 'pending_payment' : 'pending',
                'notes' => $notes,
                'payment_method' => $request->payment_method
            ];

            // Add created_by if the column exists
            if (Schema::hasColumn('orders', 'created_by')) {
                $orderData['created_by'] = Auth::id();
            }

            \Log::info('Attempting to create order with data:', $orderData);
            
            try {
                $order = Order::create($orderData);
                \Log::info('Order created successfully:', ['order_id' => $order->id, 'order_number' => $order->order_number]);
                
                // Note: Real-time notifications are handled via polling in the frontend
                \Log::info('Order created - real-time notifications will be handled by frontend polling');
            } catch (\Exception $e) {
                \Log::error('Failed to create order:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'order_data' => $orderData
                ]);
                throw $e;
            }

            // Generate tracking code for guest orders (non-authenticated users)
            if (!Auth::check()) {
                $order->update([
                    'tracking_code' => $order->generateTrackingCode(),
                    'tracking_code_expires_at' => now()->addHours(24),
                ]);
            }

            // Create order items
            foreach ($items as $item) {
                // Debug: Log each item
                \Log::info('Processing order item:', $item);
                
                if (!isset($item['id'])) {
                    throw new \Exception('Item missing ID: ' . json_encode($item));
                }
                
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                ]);
                
                \Log::info('Order item created:', [
                    'order_item_id' => $orderItem->id,
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity']
                ]);
            }

            \Log::info('All order items created successfully');

            // Handle reward points for bank transfer payments
            if ($request->payment_method === 'transfer' && Auth::check()) {
                \Log::info('Processing reward points for bank transfer payment');
                
                $user = Auth::user();
                $wallet = $user->getWalletOrCreate();
                
                // Calculate reward points (1 point per ₦100 spent)
                $pointsEarned = (int) ($charges['total'] / 100);
                
                if ($pointsEarned > 0) {
                    \Log::info('Calculated reward points:', ['points_earned' => $pointsEarned]);
                    
                    // Find existing reward record or create new one
                    $userReward = \App\Models\UserReward::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'restaurant_id' => $restaurantId
                        ],
                        [
                            'points' => 0,
                            'total_spent' => 0,
                            'orders_count' => 0,
                            'tier' => 'bronze'
                        ]
                    );
                    
                    // Update the reward record with new order data
                    $userReward->update([
                        'order_id' => $order->id,
                        'points_earned' => $pointsEarned,
                        'order_amount' => $charges['total'],
                        'payment_method' => 'transfer',
                        'status' => 'credited',
                        'credited_at' => now(),
                        'expires_at' => now()->addMonths(6),
                        'points' => $userReward->points + $pointsEarned,
                        'total_spent' => $userReward->total_spent + $charges['total'],
                        'orders_count' => $userReward->orders_count + 1,
                        'last_order_at' => now()
                    ]);

                    // Credit points to wallet immediately
                    $wallet->credit($pointsEarned, "Reward points from order #{$order->id}", $order->id);
                    
                    \Log::info('Reward points processed successfully');
                }
            }

            // Handle wallet payments
            if ($request->payment_method === 'wallet' && Auth::check()) {
                \Log::info('Processing wallet payment');
                
                $user = Auth::user();
                $wallet = $user->getWalletOrCreate();
                
                // Check if user has sufficient balance
                if ($wallet->balance < $charges['total']) {
                    throw new \Exception('Insufficient wallet balance');
                }
                
                // Debit the amount from wallet
                $wallet->debit($charges['total'], "Payment for order #{$order->id}", $order->id);
                
                // Update order payment status
                $order->update([
                    'payment_status' => 'paid'
                ]);
                
                \Log::info('Wallet payment processed successfully');
            }

            DB::commit();
            
            \Log::info('Order creation transaction committed successfully');
            \Log::info('=== ORDER CREATION SUCCESS ===');

            // Determine response based on user authentication
            $response = [
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_number' => $order->order_number ?? 'ORD-' . $order->id,
                'order_id' => $order->id,
                'order' => $order,
                'total' => $charges['total']
            ];

            // For guest users, add email collection redirect
            if (!Auth::check()) {
                $response['redirect_url'] = route('guest.collect-email', $order->id);
                $response['message'] = 'Order placed successfully! Please provide your email to track your order.';
            }

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('=== ORDER CREATION FAILED ===');
            \Log::error('Order creation failed:', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'auth_check' => Auth::check(),
                'auth_user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order. Please try again. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($orderNumber)
    {
        // Find order by order number or tracking code
        $order = Order::with('orderItems.menuItem')
            ->where(function($query) use ($orderNumber) {
                $query->where('order_number', $orderNumber)
                      ->orWhere('tracking_code', $orderNumber);
            })
            ->first();

        if (!$order) {
            abort(404, 'Order not found.');
        }

        $user = Auth::user();
        
        // Debug logging
        \Log::info('Order show access attempt', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'tracking_code' => $order->tracking_code,
            'order_user_id' => $order->user_id,
            'order_restaurant_id' => $order->restaurant_id,
            'auth_check' => Auth::check(),
            'auth_id' => Auth::id(),
            'user_name' => $user ? $user->name : 'Guest',
            'user_is_manager' => $user ? $user->isRestaurantOwner() : false,
            'user_is_admin' => $user ? $user->isAdmin() : false,
            'route_name' => request()->route()->getName()
        ]);
        
        // Check if user can access this order
        $canAccess = false;
        
        if (Auth::check()) {
            // Authenticated user can access their own orders
            if ($order->user_id === Auth::id()) {
                $canAccess = true;
            }
            // Admin can access all orders
            elseif ($user && $user->isAdmin()) {
                $canAccess = true;
            }
            // Restaurant manager/owner can access their restaurant's orders
            elseif ($user && \App\Models\Manager::canAccessRestaurant(Auth::id(), $order->restaurant_id, 'manager')) {
                $canAccess = true;
            }
        }
        
        \Log::info('Order show authorization check', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'order_user_id' => $order->user_id,
            'auth_check' => Auth::check(),
            'auth_id' => Auth::id(),
            'user_name' => $user ? $user->name : 'Guest',
            'can_access' => $canAccess
        ]);
        
        if (!$canAccess) {
            \Log::warning('Unauthorized order show access', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => $user ? $user->id : null,
                'order_created_by' => $order->created_by,
                'route_name' => request()->route()->getName()
            ]);
            abort(403, 'Unauthorized access to this order.');
        }
        
        return view('orders.show', compact('order'));
    }

    public function guestOrderShow($orderNumber)
    {
        // Find order by order number or tracking code
        $order = Order::with('orderItems.menuItem')
            ->where(function($query) use ($orderNumber) {
                $query->where('order_number', $orderNumber)
                      ->orWhere('tracking_code', $orderNumber);
            })
            ->first();

        if (!$order) {
            abort(404, 'Order not found.');
        }

        // For guest orders, check if it's a guest order (no user_id or has tracking code)
        if ($order->user_id !== null && !$order->tracking_code) {
            abort(403, 'This order requires authentication to view.');
        }

        // Log guest order access
        \Log::info('Guest order show access', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'tracking_code' => $order->tracking_code,
            'order_type' => $order->order_type,
            'restaurant_id' => $order->restaurant_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return view('orders.guest-show', compact('order'));
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
            // For guests, redirect to login
            return redirect()->route('login')->with('error', 'Please log in to view your orders.');
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

    public function adminIndex()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // If user is admin, show all orders
        if ($user && $user->isAdmin()) {
            $orders = Order::with(['orderItems.menuItem', 'restaurant'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } 
        // If user is restaurant owner, show only their restaurant's orders
        elseif ($user && $user->isRestaurantOwner() && $user->primaryRestaurant) {
            $orders = Order::with(['orderItems.menuItem', 'restaurant'])
                ->where('restaurant_id', $user->primaryRestaurant->id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } 
        // Otherwise, unauthorized
        else {
            abort(403, 'Unauthorized access to admin orders.');
        }
        
        return view('orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $user = Auth::user();
        
        // Check if user can update this order
        if ($user && $user->isAdmin()) {
            // Admin can update any order
        } elseif ($user && $user->isRestaurantOwner() && $user->primaryRestaurant && $order->restaurant_id === $user->primaryRestaurant->id) {
            // Restaurant owner can update their own restaurant's orders
        } else {
            abort(403, 'Unauthorized access to update this order.');
        }
        
        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully!'
        ]);
    }

    public function getOrderStatus($id)
    {
        $order = Order::findOrFail($id);
        $user = Auth::user();
        
        // Check if user can view this order
        if ($user && $user->isAdmin()) {
            // Admin can view any order
        } elseif ($user && $user->isRestaurantOwner() && $user->primaryRestaurant && $order->restaurant_id === $user->primaryRestaurant->id) {
            // Restaurant owner can view their own restaurant's orders
        } elseif (Auth::check() && $order->user_id === Auth::id()) {
            // User can view their own orders
        } elseif ($order->user_id === null) {
            // Guest orders can be viewed by anyone (no private info)
        } else {
            abort(403, 'Unauthorized access to this order.');
        }
        
        return response()->json([
            'success' => true,
            'order' => $order,
            'status_info' => $this->getStatusInfo($order->status)
        ]);
    }

    public function status($orderNumber)
    {
        // Find order by order number or tracking code
        $order = Order::where(function($query) use ($orderNumber) {
            $query->where('order_number', $orderNumber)
                  ->orWhere('tracking_code', $orderNumber);
        })->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }

        $user = Auth::user();
        
        // Check if user can view this order
        if ($user && $user->isAdmin()) {
            // Admin can view any order
        } elseif ($user && \App\Models\Manager::canAccessRestaurant(Auth::id(), $order->restaurant_id, 'manager')) {
            // Restaurant manager/owner can view their restaurant's orders
        } elseif (Auth::check() && $order->user_id === Auth::id()) {
            // User can view their own orders
        } elseif ($order->user_id === null) {
            // Guest orders can be viewed by anyone (no private info)
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this order.'
            ], 403);
        }
        
        return response()->json([
            'success' => true,
            'order' => $order,
            'status_info' => $this->getStatusInfo($order->status)
        ]);
    }

    public function userOrderShow($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $order = Order::with('orderItems.menuItem')->findOrFail($id);
        $user = Auth::user();
        
        // Debug logging
        \Log::info('User order show access attempt', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'order_id' => $order->id,
            'order_user_id' => $order->user_id,
            'order_restaurant_id' => $order->restaurant_id,
            'order_customer_name' => $order->customer_name,
            'auth_id' => Auth::id(),
            'user_owns_order' => $order->user_id === Auth::id(),
            'user_is_manager' => $user->isRestaurantOwner(),
            'user_is_admin' => $user->isAdmin()
        ]);
        
        // TEMPORARY WORKAROUND: Allow all authenticated users to access any order
        // TODO: Remove this workaround once authorization is properly fixed
        $canAccess = true; // Allow everyone for now
        
        \Log::info('User order show authorization check (WORKAROUND ENABLED)', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'order_user_id' => $order->user_id,
            'auth_id' => Auth::id(),
            'can_access' => $canAccess,
            'note' => 'WORKAROUND: All users allowed'
        ]);
        
        return view('orders.user-show', compact('order'));
    }

    public function restaurantOrders($slug)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $user = Auth::user();
        
        \Log::info('Restaurant orders access attempt', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
        ]);
        
        // Check if user can access this restaurant
        $canAccess = \App\Models\Manager::canAccessRestaurant($user->id, $restaurant->id, 'manager');
        $isAdmin = $user->isAdmin();
        
        \Log::info('Restaurant orders authorization check', [
            'can_access' => $canAccess,
            'is_admin' => $isAdmin,
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
        ]);
        
        if (!$canAccess && !$isAdmin) {
            abort(403, 'Unauthorized access to restaurant orders. You need manager privileges.');
        }
        
        $orders = Order::with(['orderItems.menuItem'])
            ->where('restaurant_id', $restaurant->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('restaurant.orders.index', compact('restaurant', 'orders'));
    }

    public function restaurantOrderShow($slug, $order)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $order = Order::with(['orderItems.menuItem'])->findOrFail($order);
        $user = Auth::user();
        
        \Log::info('Restaurant order show access attempt', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            'order_id' => $order->id,
            'order_restaurant_id' => $order->restaurant_id,
            'url_slug' => $slug,
            'request_url' => request()->url(),
        ]);
        
        // Check if user can access this restaurant
        $canAccess = \App\Models\Manager::canAccessRestaurant($user->id, $restaurant->id, 'manager');
        $isAdmin = $user->isAdmin();
        
        \Log::info('Restaurant order show authorization check', [
            'can_access' => $canAccess,
            'is_admin' => $isAdmin,
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
            'order_restaurant_id' => $order->restaurant_id,
        ]);
        
        if (!$canAccess && !$isAdmin) {
            \Log::warning('Unauthorized restaurant order access', [
                'user_id' => $user->id,
                'restaurant_id' => $restaurant->id,
                'order_id' => $order->id,
            ]);
            abort(403, 'Unauthorized access to restaurant orders. You need manager privileges.');
        }
        
        // Enhanced check for order-restaurant relationship
        if ($order->restaurant_id != $restaurant->id) {
            \Log::warning('Order restaurant mismatch', [
                'user_id' => $user->id,
                'restaurant_id' => $restaurant->id,
                'order_id' => $order->id,
                'order_restaurant_id' => $order->restaurant_id,
                'slug' => $slug,
            ]);
            abort(403, 'Order does not belong to this restaurant.');
        }
        
        \Log::info('Restaurant order access granted', [
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
            'order_id' => $order->id,
        ]);
        
        return view('restaurant.orders.show', compact('restaurant', 'order'));
    }

    /**
     * Alternative method for restaurant managers to view orders
     * This method is more permissive and handles edge cases
     */
    public function restaurantOrderShowAlternative($orderId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $order = Order::with(['orderItems.menuItem', 'restaurant'])->findOrFail($orderId);
        $user = Auth::user();
        
        \Log::info('Alternative restaurant order show access attempt', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'order_id' => $order->id,
            'order_restaurant_id' => $order->restaurant_id,
            'restaurant_name' => $order->restaurant->name ?? 'Unknown',
        ]);
        
        // Check if user can access this restaurant
        $canAccess = \App\Models\Manager::canAccessRestaurant($user->id, $order->restaurant_id, 'manager');
        $isAdmin = $user->isAdmin();
        
        \Log::info('Alternative restaurant order authorization check', [
            'can_access' => $canAccess,
            'is_admin' => $isAdmin,
            'user_id' => $user->id,
            'order_restaurant_id' => $order->restaurant_id,
        ]);
        
        if (!$canAccess && !$isAdmin) {
            \Log::warning('Unauthorized alternative restaurant order access', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'order_restaurant_id' => $order->restaurant_id,
            ]);
            abort(403, 'Unauthorized access to this order. You need manager privileges.');
        }
        
        \Log::info('Alternative restaurant order access granted', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'order_restaurant_id' => $order->restaurant_id,
        ]);
        
        // Get the restaurant from the order
        $restaurant = $order->restaurant;
        
        return view('restaurant.orders.show', compact('order', 'restaurant'));
    }

    /**
     * Alternative method for restaurant managers to update order status
     */
    public function restaurantOrderStatusAlternative(Request $request, $orderId)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled',
            'status_note' => 'nullable|string|max:500'
        ]);

        $order = Order::findOrFail($orderId);
        $user = Auth::user();
        
        \Log::info('Alternative restaurant order status update attempt', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'order_id' => $order->id,
            'order_restaurant_id' => $order->restaurant_id,
            'new_status' => $request->status,
            'status_note' => $request->status_note,
        ]);
        
        // Check if user can access this restaurant
        $canAccess = \App\Models\Manager::canAccessRestaurant($user->id, $order->restaurant_id, 'manager');
        $isAdmin = $user->isAdmin();
        
        if (!$canAccess && !$isAdmin) {
            \Log::warning('Unauthorized alternative restaurant order status update', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'order_restaurant_id' => $order->restaurant_id,
            ]);
            return response()->json(['success' => false, 'message' => 'Unauthorized access to this order.'], 403);
        }

        $oldStatus = $order->status;
        
        // Update order status
        $order->update([
            'status' => $request->status,
            'status_note' => $request->status_note,
            'status_updated_at' => now(),
            'status_updated_by' => $user->id
        ]);

        // Handle earnings calculation based on order status change
        $order->restaurant->handleOrderStatusChange($order, $oldStatus, $request->status);

        \Log::info('Alternative restaurant order status updated successfully', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'order' => $order->fresh()
        ]);
    }

    public function restaurantOrderStatus(Request $request, $slug, $order)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled',
            'status_note' => 'nullable|string|max:500'
        ]);

        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $order = Order::findOrFail($order);
        $user = Auth::user();
        
        \Log::info('Restaurant order status access attempt', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            'order_id' => $order->id,
            'new_status' => $request->status,
            'status_note' => $request->status_note,
        ]);
        
        // Check if user can access this restaurant
        $canAccess = \App\Models\Manager::canAccessRestaurant($user->id, $restaurant->id, 'manager');
        $isAdmin = $user->isAdmin();
        
        \Log::info('Restaurant order status authorization check', [
            'can_access' => $canAccess,
            'is_admin' => $isAdmin,
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
        ]);
        
        if (!$canAccess && !$isAdmin) {
            abort(403, 'Unauthorized access to restaurant orders. You need manager privileges.');
        }
        
        if ($order->restaurant_id !== $restaurant->id) {
            abort(403, 'Order does not belong to this restaurant.');
        }

        $oldStatus = $order->status;
        $isPayOnDelivery = $order->payment_method === 'cash';
        
        // Update order status
        $order->update([
            'status' => $request->status,
            'status_note' => $request->status_note,
            'status_updated_at' => now(),
            'status_updated_by' => $user->id
        ]);

        // Handle earnings calculation based on order status change
        $restaurant->handleOrderStatusChange($order, $oldStatus, $request->status);

        // Log the status change
        \Log::info('Order status updated', [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'updated_by' => $user->id,
            'status_note' => $request->status_note,
            'is_pay_on_delivery' => $isPayOnDelivery,
            'payment_method' => $order->payment_method
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully!',
            'new_status' => $request->status,
            'status_note' => $request->status_note
        ]);
    }



    public function searchByTrackingCode(Request $request)
    {
        $request->validate([
            'tracking_code' => 'required|string|size:4'
        ]);

        $order = Order::byTrackingCode($request->tracking_code)->first();

        if (!$order) {
            return back()->withErrors(['tracking_code' => 'Invalid or expired tracking code.']);
        }

        return view('orders.track', compact('order'));
    }

    public function restaurantTrackForm($slug)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $user = Auth::user();
        
        \Log::info('Restaurant track form access attempt', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
        ]);
        
        // Check if user can access this restaurant
        $canAccess = \App\Models\Manager::canAccessRestaurant($user->id, $restaurant->id, 'manager');
        $isAdmin = $user->isAdmin();
        
        \Log::info('Restaurant track form authorization check', [
            'can_access' => $canAccess,
            'is_admin' => $isAdmin,
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
        ]);
        
        if (!$canAccess && !$isAdmin) {
            abort(403, 'Unauthorized access to restaurant tracking. You need manager privileges.');
        }
        
        return view('restaurant.track-form', compact('restaurant'));
    }

    public function restaurantTrackOrder(Request $request, $slug)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'tracking_code' => 'required|string|size:4'
        ]);

        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $user = Auth::user();
        
        \Log::info('Restaurant track order access attempt', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            'tracking_code' => $request->tracking_code,
        ]);
        
        // Check if user can access this restaurant
        $canAccess = \App\Models\Manager::canAccessRestaurant($user->id, $restaurant->id, 'manager');
        $isAdmin = $user->isAdmin();
        
        \Log::info('Restaurant track order authorization check', [
            'can_access' => $canAccess,
            'is_admin' => $isAdmin,
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
        ]);
        
        if (!$canAccess && !$isAdmin) {
            abort(403, 'Unauthorized access to restaurant tracking. You need manager privileges.');
        }

        $order = Order::byTrackingCode($request->tracking_code)
            ->where('restaurant_id', $restaurant->id)
            ->first();

        if (!$order) {
            return back()->withErrors(['tracking_code' => 'Invalid or expired tracking code for this restaurant.']);
        }

        return view('restaurant.track-result', compact('restaurant', 'order'));
    }

    public function qrAccess($code)
    {
        // Find the QR code in the database
        $tableQR = \App\Models\TableQR::where('qr_code', $code)
            ->orWhere('short_url', $code)
            ->first();

        if (!$tableQR) {
            abort(404, 'QR Code not found.');
        }

        // Check if QR code is active
        if (!$tableQR->is_active) {
            abort(404, 'QR Code is inactive.');
        }

        // Get the restaurant
        $restaurant = $tableQR->restaurant;
        if (!$restaurant || !$restaurant->is_active) {
            abort(404, 'Restaurant not found or inactive.');
        }

        // Increment usage count
        $tableQR->incrementUsage();

        // Store table information in session for pre-filling on checkout
        session([
            'qr_table_number' => $tableQR->table_number,
            'qr_restaurant_id' => $restaurant->id,
            'qr_table_qr_id' => $tableQR->id
        ]);

        // Redirect to the specific restaurant menu
        return redirect()->route('menu.restaurant', $restaurant->slug);
    }

    public function qrImage($code)
    {
        $tableQR = \App\Models\TableQR::where('qr_code', $code)
            ->orWhere('short_url', $code)
            ->first();
        
        if (!$tableQR || !$tableQR->qr_image || !Storage::disk('public')->exists($tableQR->qr_image)) {
            abort(404, 'QR Code image not found.');
        }
        
        return response()->file(Storage::disk('public')->path($tableQR->qr_image));
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
