@extends('layouts.app')

@section('title', $restaurant->name . ' - Order #' . $order->id)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-8">
                <div class="flex items-center space-x-4">
                    @if($restaurant->logo)
                        <img src="{{ Storage::url($restaurant->logo) }}" alt="{{ $restaurant->name }}" class="w-16 h-16 rounded-lg object-contain bg-gray-100 dark:bg-gray-700">
                    @else
                        <img src="{{ \App\Helpers\PWAHelper::getPlaceholderImage('square') }}" alt="{{ $restaurant->name }}" class="w-16 h-16 rounded-lg object-contain bg-gray-100 dark:bg-gray-700">
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $restaurant->name }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Order #{{ $order->id }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('restaurant.orders', $restaurant->slug) }}" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Details -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Details</h3>
                    </div>
                    <div class="p-6">
                        <!-- Order Status -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Status</h4>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                @elseif($order->status === 'preparing') bg-orange-100 text-orange-800
                                @elseif($order->status === 'ready') bg-green-100 text-green-800
                                @elseif($order->status === 'delivered') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <!-- Table Number -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Table</h4>
                            @if($order->table_number)
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Table {{ $order->table_number }}
                                </span>
                            @else
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                    Takeaway Order
                                </span>
                            @endif
                        </div>

                        <!-- Customer Information -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Customer Information</h4>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Name</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->customer_name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Phone</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->phone_number }}</p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Address</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->delivery_address }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Order Items</h4>
                            <div class="space-y-3">
                                @foreach($order->orderItems as $item)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            @if($item->menuItem->image)
                                                <img src="{{ Storage::url($item->menuItem->image) }}" alt="{{ $item->menuItem->name }}" class="w-12 h-12 rounded-lg object-cover">
                                            @else
                                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-utensils text-gray-400"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->menuItem->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->menuItem->category->name ?? 'Uncategorized' }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">x{{ $item->quantity }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $restaurant->currency }}{{ number_format($item->total_price / 100, 2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Order Notes -->
                        @if($order->notes)
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</h4>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Summary</h3>
                    </div>
                    <div class="p-6">
                        <!-- Order Info -->
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Order ID</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">#{{ $order->id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Table</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    @if($order->table_number)
                                        Table {{ $order->table_number }}
                                    @else
                                        Takeaway
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Date</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Delivery Time</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->delivery_time }}</span>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-6">
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Subtotal</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $restaurant->currency }}{{ number_format(($order->total_amount - 500) / 100, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Delivery Fee</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $restaurant->currency }}{{ number_format(500 / 100, 2) }}</span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-2">
                                    <span class="text-base font-medium text-gray-900 dark:text-white">Total</span>
                                    <span class="text-base font-medium text-gray-900 dark:text-white">{{ $restaurant->currency }}{{ number_format($order->total_amount / 100, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Update -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Update Status</h4>
                            <form id="statusForm" class="space-y-3">
                                <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                                    <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors">
                                    Update Status
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const status = document.getElementById('status').value;
    
    fetch(`/restaurant/{{ $restaurant->slug }}/orders/{{ $order->id }}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to update order status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update order status');
    });
});
</script>
@endsection 