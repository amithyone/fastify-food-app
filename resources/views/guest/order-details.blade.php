@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Order Details</h1>
                    <p class="text-gray-600 dark:text-gray-400">Order #{{ $order->order_number }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('guest.dashboard') }}" 
                       class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                    </a>
                </div>
            </div>
        </div>

        <!-- Order Status -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Order Status</h2>
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 text-sm font-medium rounded-full 
                            @if($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                            @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $order->created_at->format('M d, Y \a\t g:i A') }}
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        ₦{{ number_format($order->total_amount, 2) }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ ucfirst($order->order_type) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Restaurant Info -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Restaurant</h2>
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-utensils text-2xl text-orange-600 dark:text-orange-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $order->restaurant->name }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ $order->restaurant->address }}</p>
                    @if($order->restaurant->phone)
                        <p class="text-sm text-gray-500 dark:text-gray-500">
                            <i class="fas fa-phone mr-1"></i>{{ $order->restaurant->phone }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Order Items</h2>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($order->items as $item)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                @if($item->menuItem->image)
                                    <img src="{{ asset('storage/' . $item->menuItem->image) }}" 
                                         alt="{{ $item->menuItem->name }}"
                                         class="w-16 h-16 object-cover rounded-lg">
                                @else
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-utensils text-gray-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ $item->menuItem->name }}
                                    </h3>
                                    @if($item->menuItem->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $item->menuItem->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-medium text-gray-900 dark:text-white">
                                    ₦{{ number_format($item->total_price, 2) }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">
                                    Qty: {{ $item->quantity }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                    <span class="font-medium text-gray-900 dark:text-white">₦{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->service_charge > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Service Charge</span>
                        <span class="font-medium text-gray-900 dark:text-white">₦{{ number_format($order->service_charge, 2) }}</span>
                    </div>
                @endif
                @if($order->tax_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Tax</span>
                        <span class="font-medium text-gray-900 dark:text-white">₦{{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                @endif
                @if($order->delivery_fee > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Delivery Fee</span>
                        <span class="font-medium text-gray-900 dark:text-white">₦{{ number_format($order->delivery_fee, 2) }}</span>
                    </div>
                @endif
                @if($order->discount_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Discount</span>
                        <span class="font-medium text-green-600">-₦{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                    <div class="flex justify-between">
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">Total</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">₦{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Customer Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Name</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Phone</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $order->phone_number }}</p>
                </div>
                @if($order->delivery_address)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Delivery Address</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $order->delivery_address }}</p>
                    </div>
                @endif
                @if($order->pickup_name)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Pickup Name</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $order->pickup_name }}</p>
                    </div>
                @endif
                @if($order->pickup_code)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Pickup Code</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $order->pickup_code }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Payment Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Payment Method</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ ucfirst($order->payment_method) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Payment Status</p>
                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                        @if($order->payment_status === 'paid') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400 @endif">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                @if($order->payment_reference)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Payment Reference</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $order->payment_reference }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                @if($order->status === 'completed')
                    <button onclick="reorder({{ $order->id }})" 
                            class="flex-1 bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fas fa-redo mr-2"></i>Reorder
                    </button>
                @endif
                <a href="{{ route('guest.dashboard') }}" 
                   class="flex-1 bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors text-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Reorder Modal -->
<div id="reorderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reorder Items</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Would you like to add all items from this order to your cart?
            </p>
            <div class="flex space-x-3">
                <button onclick="closeReorderModal()" 
                        class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button onclick="confirmReorder()" 
                        class="flex-1 bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors">
                    Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentOrderId = null;

function reorder(orderId) {
    currentOrderId = orderId;
    document.getElementById('reorderModal').classList.remove('hidden');
}

function closeReorderModal() {
    document.getElementById('reorderModal').classList.add('hidden');
    currentOrderId = null;
}

function confirmReorder() {
    if (!currentOrderId) return;
    
    // Add items to cart (you'll need to implement this endpoint)
    fetch(`/guest/orders/${currentOrderId}/reorder`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeReorderModal();
            // Redirect to restaurant menu
            window.location.href = data.redirect_url;
        } else {
            alert(data.message || 'Failed to add items to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add items to cart');
    });
}
</script>
@endpush
