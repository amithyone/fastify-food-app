@extends('layouts.app')

@section('title', 'Order #' . $order->id . ' - Tracking')

@section('content')
<div class="min-h-screen bg-[#f1ecdc] dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Order #{{ $order->id }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Tracked with code: <span class="font-mono font-bold text-orange-600 dark:text-orange-400">{{ $order->tracking_code }}</span>
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Copy Tracking Code Button -->
                    <button onclick="copyTrackingCode()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        <i class="fas fa-copy mr-2"></i>
                        Copy Code
                    </button>
                    
                    @if(!Auth::check())
                        <a href="{{ route('register') }}" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>
                            Create Account
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Status</h2>
            <div class="flex items-center space-x-4">
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                    @elseif($order->status === 'preparing') bg-orange-100 text-orange-800
                    @elseif($order->status === 'ready') bg-green-100 text-green-800
                    @elseif($order->status === 'delivered') bg-gray-100 text-gray-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst($order->status) }}
                </span>
                
                @if($order->isTrackingCodeActive())
                    <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        <i class="fas fa-clock mr-1"></i>
                        Active until {{ $order->tracking_code_expires_at->format('M d, H:i') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Code expired
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Details -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Details</h3>
                    </div>
                    <div class="p-6">
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
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->restaurant->currency }}{{ number_format($item->total_price / 100, 2) }}</p>
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
                                <span class="text-sm text-gray-500 dark:text-gray-400">Tracking Code</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $order->tracking_code }}</span>
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
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->restaurant->currency }}{{ number_format(($order->total_amount - 500) / 100, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Delivery Fee</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->restaurant->currency }}{{ number_format(500 / 100, 2) }}</span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-2">
                                    <span class="text-base font-medium text-gray-900 dark:text-white">Total</span>
                                    <span class="text-base font-medium text-gray-900 dark:text-white">{{ $order->restaurant->currency }}{{ number_format($order->total_amount / 100, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        @if(!Auth::check())
                            <!-- Registration Encouragement -->
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-orange-900 dark:text-orange-200 mb-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Don't lose your orders!
                                    </h4>
                                    <p class="text-xs text-orange-700 dark:text-orange-300 mb-3">
                                        Create an account to save your orders and get better tracking. Your tracking code expires in 24 hours.
                                    </p>
                                    <a href="{{ route('register') }}" 
                                        class="w-full inline-flex justify-center items-center px-3 py-2 border border-transparent text-xs font-medium rounded-md text-orange-700 bg-orange-100 hover:bg-orange-200 dark:text-orange-200 dark:bg-orange-800 dark:hover:bg-orange-700 transition-colors">
                                        <i class="fas fa-user-plus mr-1"></i>
                                        Create Account
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bottom padding for fixed bottom menu -->
    <div class="h-20"></div>
</div>

<!-- Bottom Navigation Menu -->
<x-bottom-nav />

<script>
function copyTrackingCode() {
    const trackingCode = '{{ $order->tracking_code }}';
    navigator.clipboard.writeText(trackingCode).then(function() {
        // Show success message
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
        button.classList.add('bg-green-600', 'hover:bg-green-700');
        button.classList.remove('bg-gray-600', 'hover:bg-gray-700');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-gray-600', 'hover:bg-gray-700');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endsection 