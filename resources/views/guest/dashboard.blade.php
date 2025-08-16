@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Orders</h1>
                    <p class="text-gray-600 dark:text-gray-400">Welcome back, {{ $guestUser->name ?? 'Guest' }}!</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Account</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $guestUser->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('guest.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- QR Code Access Section -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        <i class="fas fa-qrcode mr-2 text-orange-500"></i>Quick Access
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        Use this QR code to quickly access your account from any device
                    </p>
                </div>
                <div class="text-center">
                    <div id="qrCode" class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg inline-block">
                        <!-- QR Code will be generated here -->
                        <div class="w-32 h-32 bg-white rounded-lg flex items-center justify-center">
                            <i class="fas fa-qrcode text-4xl text-gray-400"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Scan to login</p>
                </div>
            </div>
        </div>

        <!-- Order History -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-history mr-2 text-orange-500"></i>Order History
                </h2>
            </div>

            @if($guestUser->orders->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($guestUser->orders as $order)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-utensils text-orange-600 dark:text-orange-400"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2">
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                                    Order #{{ $order->order_number }}
                                                </h3>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                    @if($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                                    @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $order->restaurant->name }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-500">
                                                {{ $order->created_at->format('M d, Y \a\t g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Order Items Preview -->
                                    <div class="mt-3 ml-16">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            @foreach($order->items->take(3) as $item)
                                                <span class="inline-block bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded mr-2 mb-1">
                                                    {{ $item->quantity }}x {{ $item->menuItem->name }}
                                                </span>
                                            @endforeach
                                            @if($order->items->count() > 3)
                                                <span class="text-gray-500">+{{ $order->items->count() - 3 }} more</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                                            ₦{{ number_format($order->total_amount, 2) }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-500">
                                            {{ ucfirst($order->order_type) }}
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('guest.orders.show', $order->id) }}" 
                                           class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors text-sm">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                        @if($order->status === 'completed')
                                            <button onclick="reorder({{ $order->id }})" 
                                                    class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors text-sm">
                                                <i class="fas fa-redo mr-1"></i>Reorder
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-utensils text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No orders yet</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Start exploring restaurants and place your first order!
                    </p>
                    <a href="{{ route('home') }}" 
                       class="bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600 transition-colors">
                        <i class="fas fa-search mr-2"></i>Browse Restaurants
                    </a>
                </div>
            @endif
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

// Generate QR code for session access
function generateQRCode() {
    const qrData = {
        type: 'guest_session',
        token: '{{ $guestUser->session_token }}',
        user_id: {{ $guestUser->id }},
        expires_at: '{{ $guestUser->session_expires_at?->toISOString() }}'
    };
    
    const qrContainer = document.getElementById('qrCode');
    
    // For now, display the data as text (you can integrate a QR code library later)
    qrContainer.innerHTML = `
        <div class="text-center">
            <div class="w-32 h-32 bg-white rounded-lg flex items-center justify-center mb-2">
                <i class="fas fa-qrcode text-4xl text-gray-400"></i>
            </div>
            <p class="text-xs text-gray-500">Session Token:</p>
            <p class="text-xs font-mono text-gray-700 dark:text-gray-300 break-all">
                ${qrData.token.substring(0, 8)}...
            </p>
        </div>
    `;
}

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

// Initialize QR code on page load
document.addEventListener('DOMContentLoaded', function() {
    generateQRCode();
});
</script>
@endpush
