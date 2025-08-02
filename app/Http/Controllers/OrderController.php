<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\UserReward;
use App\Models\TableQR;
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
        // Debug: Log the incoming request data
        \Log::info('Order creation request:', [
            'items' => $request->items,
            'customer_info' => $request->customer_info,
            'payment_method' => $request->payment_method
        ]);
        
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
                'user_id' => Auth::id(), // Will be null for guest users
                'order_number' => (new Order())->generateOrderNumber(),
                'customer_name' => $customerName,
                'phone_number' => $phoneNumber,
                'delivery_address' => $deliveryAddress,
                'allergies' => $customerInfo['instructions'] ?? null, // Re-purposed for delivery instructions
                'delivery_time' => $customerInfo['in_restaurant'] ? 'In Restaurant' : 'ASAP',
                'total_amount' => $total,
                'status' => 'pending',
                'notes' => $notes,
            ]);

            // Generate tracking code for guest orders (non-authenticated users)
            if (!Auth::check()) {
                $order->update([
                    'tracking_code' => $order->generateTrackingCode(),
                    'tracking_code_expires_at' => now()->addHours(24),
                ]);
            }

            // Create order items
            foreach ($request->items as $item) {
                // Debug: Log each item
                \Log::info('Processing order item:', $item);
                
                if (!isset($item['id'])) {
                    throw new \Exception('Item missing ID: ' . json_encode($item));
                }
                
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
        $user = Auth::user();
        
        // Check if user can view this order
        if ($user && $user->isAdmin()) {
            // Admin can view any order
        } elseif ($user && $user->isRestaurantOwner() && $order->restaurant_id === $user->restaurant_id) {
            // Restaurant owner can view their own restaurant's orders
        } elseif (Auth::check() && $order->user_id === Auth::id()) {
            // User can view their own orders
        } elseif ($order->user_id === null) {
            // Guest orders can be viewed by anyone (no private info)
        } else {
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
        elseif ($user && $user->isRestaurantOwner()) {
            $orders = Order::with(['orderItems.menuItem', 'restaurant'])
                ->where('restaurant_id', $user->restaurant_id)
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
        } elseif ($user && $user->isRestaurantOwner() && $order->restaurant_id === $user->restaurant_id) {
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
        } elseif ($user && $user->isRestaurantOwner() && $order->restaurant_id === $user->restaurant_id) {
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

    public function status($id)
    {
        $order = Order::findOrFail($id);
        $user = Auth::user();
        
        // Check if user can view this order
        if ($user && $user->isAdmin()) {
            // Admin can view any order
        } elseif ($user && $user->isRestaurantOwner() && $order->restaurant_id === $user->restaurant_id) {
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

    public function userOrderShow($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $order = Order::with('orderItems.menuItem')->findOrFail($id);
        
        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }
        
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
        ]);
        
        // Check if user can access this restaurant
        $canAccess = \App\Models\Manager::canAccessRestaurant($user->id, $restaurant->id, 'manager');
        $isAdmin = $user->isAdmin();
        
        \Log::info('Restaurant order show authorization check', [
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
        
        return view('restaurant.orders.show', compact('restaurant', 'order'));
    }

    public function restaurantOrderStatus(Request $request, $slug, $order)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled'
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
        
        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully!'
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

        // Redirect to the main restaurant menu
        return redirect()->route('menu.index', $restaurant->slug);
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
