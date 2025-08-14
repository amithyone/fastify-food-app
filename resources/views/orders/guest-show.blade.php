@extends('layouts.app')
@section('title', 'Order Confirmation - Fastify')
@section('content')

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-2xl mx-auto px-4">
        <!-- Success Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-2xl text-green-600 dark:text-green-400"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Order Confirmed!</h1>
            <p class="text-gray-600 dark:text-gray-400">Your order has been placed successfully</p>
        </div>

        <!-- Order Details Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Order Details</h2>
                <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-200 rounded-full text-sm font-medium">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            <!-- Order Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Order Number</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $order->order_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tracking Code</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $order->tracking_code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Restaurant</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $order->restaurant->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Order Type</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Payment Method</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($order->payment_method) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Order Date</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Customer Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $order->phone_number }}</p>
                    </div>
                    @if($order->delivery_address && $order->delivery_address !== 'In Restaurant - Table 5')
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Delivery Address</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $order->delivery_address }}</p>
                    </div>
                    @endif
                    @if($order->order_type === 'in_restaurant' && str_contains($order->delivery_address, 'Table'))
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Table Number</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ str_replace('In Restaurant - Table ', '', $order->delivery_address) }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Order Items</h3>
                <div class="space-y-3">
                    @foreach($order->orderItems as $item)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center">
                            @if($item->menuItem->image)
                            <img src="{{ $item->menuItem->image_url }}" alt="{{ $item->menuItem->name }}" class="w-12 h-12 rounded-lg object-cover mr-3">
                            @else
                            <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-utensils text-gray-400"></i>
                            </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $item->menuItem->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Qty: {{ $item->quantity }}</p>
                            </div>
                        </div>
                        <p class="font-semibold text-gray-900 dark:text-white">₦{{ number_format($item->total_price) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                    <span class="font-medium text-gray-900 dark:text-white">₦{{ number_format($order->subtotal) }}</span>
                </div>
                @if($order->service_charge > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Service Charge</span>
                    <span class="font-medium text-gray-900 dark:text-white">₦{{ number_format($order->service_charge) }}</span>
                </div>
                @endif
                @if($order->tax_amount > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Tax</span>
                    <span class="font-medium text-gray-900 dark:text-white">₦{{ number_format($order->tax_amount) }}</span>
                </div>
                @endif
                @if($order->delivery_fee > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Delivery Fee</span>
                    <span class="font-medium text-gray-900 dark:text-white">₦{{ number_format($order->delivery_fee) }}</span>
                </div>
                @endif
                @if($order->discount_amount > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Discount</span>
                    <span class="font-medium text-green-600 dark:text-green-400">-₦{{ number_format($order->discount_amount) }}</span>
                </div>
                @endif
                <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                        <span class="font-bold text-lg text-gray-900 dark:text-white">₦{{ number_format($order->total_amount) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Information -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-1 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">Important Information</h4>
                    <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                        <li>• <strong>Save your tracking code:</strong> {{ $order->tracking_code }} - You'll need this to track your order</li>
                        <li>• You can track your order status using the tracking code on our website</li>
                        <li>• For any questions, contact the restaurant directly</li>
                        @if($order->order_type === 'in_restaurant')
                        <li>• Your order will be prepared and served at your table</li>
                        @elseif($order->order_type === 'pickup')
                        <li>• Your order will be ready for pickup at the specified time</li>
                        @else
                        <li>• Your order will be delivered to the provided address</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('orders.track-form') }}" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg text-center transition-colors">
                <i class="fas fa-search mr-2"></i>
                Track Order
            </a>
            <a href="{{ route('menu.index', $order->restaurant->slug) }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg text-center transition-colors">
                <i class="fas fa-utensils mr-2"></i>
                Order More
            </a>
        </div>

        <!-- QR Code for Easy Access -->
        <div class="text-center mt-8">
            <div class="inline-block p-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Scan to track your order</p>
                <div class="w-32 h-32 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-qrcode text-4xl text-gray-400"></i>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $order->tracking_code }}</p>
            </div>
        </div>
    </div>
</div>

@endsection
