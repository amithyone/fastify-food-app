@extends('layouts.app')

@section('title', $restaurant->name . ' - Order #' . $order->id)

@section('content')
<div class="w-full min-h-screen bg-[#f1ecdc] dark:bg-gray-900">
    <div class="max-w-md mx-auto px-4 py-4">
        <!-- Header Section -->
        <div class="text-center mb-6" style="margin-top: 20px;">
            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-check text-white text-xl"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                Order Found
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Order #{{ $order->id }} - {{ $order->tracking_code }}
            </p>
        </div>

        <!-- Restaurant Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <div class="flex items-center space-x-3">
                @if($restaurant->logo)
                    <img src="{{ $restaurant->logo_url ?? \App\Helpers\PWAHelper::getPlaceholderImage('square') }}" alt="{{ $restaurant->name }}" class="w-12 h-12 rounded-lg object-contain bg-gray-100 dark:bg-gray-700">
                @else
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-utensils text-white"></i>
                    </div>
                @endif
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $restaurant->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Order Management</p>
                </div>
            </div>
        </div>

        <!-- Order Status Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order Status</h3>
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

            <!-- Status Update Form -->
            <form method="POST" action="{{ route('restaurant.orders.status', [$restaurant->slug, $order->id]) }}" class="mb-4">
                @csrf
                @method('PUT')
                <div class="flex space-x-2">
                    <select name="status" class="flex-1 px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm">
                        Update
                    </button>
                </div>
            </form>

            <!-- Order Info -->
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Order ID:</span>
                    <span class="font-medium text-gray-900 dark:text-white">#{{ $order->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Tracking Code:</span>
                    <span class="font-mono font-medium text-gray-900 dark:text-white">{{ $order->tracking_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Date:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $order->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Customer:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $order->customer_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Phone:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $order->phone_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Total:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $restaurant->currency }}{{ number_format($order->total_amount / 100, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Order Items Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Items</h3>
            <div class="space-y-3">
                @foreach($order->orderItems as $item)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            @if($item->menuItem->image)
                                <img src="{{ $item->menuItem->image_url }}" alt="{{ $item->menuItem->name }}" class="w-10 h-10 rounded-lg object-cover">
                            @else
                                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
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
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $restaurant->currency }}{{ number_format($item->total_price) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Order Notes -->
        @if($order->notes)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Order Notes</h3>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->notes }}</p>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <a href="{{ route('restaurant.track-form', $restaurant->slug) }}" 
                class="flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-search mr-2"></i>
                Track Another
            </a>
            <a href="{{ route('restaurant.orders', $restaurant->slug) }}" 
                class="flex items-center justify-center px-4 py-3 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-600 transition-colors">
                <i class="fas fa-list mr-2"></i>
                All Orders
            </a>
        </div>
    </div>
</div>

<script>
// Auto-submit status updates
document.querySelector('select[name="status"]').addEventListener('change', function() {
    this.closest('form').submit();
});
</script>
@endsection 