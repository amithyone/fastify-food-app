@extends('layouts.app')

@section('title', 'Order Details - Abuja Eat')

@section('content')
<div class="container mx-auto px-2 py-4 max-w-md bg-white dark:bg-gray-900 min-h-screen">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6 sticky top-0 z-40 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 py-2">
        <div class="flex items-center gap-3">
            <a href="{{ route('orders.index') }}" class="text-gray-600 dark:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Order Details</h1>
        </div>
        <button id="darkModeToggle" class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
            <i class="fas fa-sun"></i>
        </button>
    </div>

    <!-- Order Header Card -->
    <div class="bg-gradient-to-br from-orange-200 to-orange-400 dark:from-gray-700 dark:to-gray-900 rounded-xl p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-white">Order #{{ $order->order_number }}</h2>
                <p class="text-sm text-white opacity-80">{{ $order->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $order->status_badge }}">
                    {{ ucfirst($order->status) }}
                </span>
                <p class="text-lg font-bold text-white mt-1">{{ $order->formatted_total }}</p>
            </div>
        </div>
    </div>

    <!-- Order Status Timeline -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 mb-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Status</h3>
        <div class="space-y-4">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-check text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Order Placed</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
            
            @if($order->status !== 'pending')
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-check text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Order Confirmed</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Restaurant confirmed your order</p>
                </div>
            </div>
            @endif
            
            @if(in_array($order->status, ['preparing', 'ready', 'delivered']))
            <div class="flex items-center">
                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-check text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Preparing</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Chef is preparing your order</p>
                </div>
            </div>
            @endif
            
            @if(in_array($order->status, ['ready', 'delivered']))
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-check text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Ready</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Your order is ready</p>
                </div>
            </div>
            @endif
            
            @if($order->status === 'delivered')
            <div class="flex items-center">
                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-check text-white text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Delivered</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Order has been delivered</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Customer Information -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 mb-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Customer Information</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Name:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->customer_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Phone:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->phone_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Order Type:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($order->order_type) }}</span>
            </div>
            @if($order->order_type === 'delivery')
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Address:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white text-right">{{ $order->delivery_address }}</span>
            </div>
            @endif
            @if($order->order_type === 'pickup')
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Pickup Code:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->pickup_code }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Pickup Time:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->pickup_time ? $order->pickup_time->format('M d, Y h:i A') : 'ASAP' }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 mb-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Items</h3>
        <div class="space-y-3">
            @foreach($order->orderItems as $item)
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center gap-3">
                    @if($item->menuItem->image)
                    <img src="{{ asset('storage/' . $item->menuItem->image) }}" alt="{{ $item->menuItem->name }}" class="w-12 h-12 rounded-lg object-cover">
                    @else
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-utensils text-orange-500 dark:text-orange-300"></i>
                    </div>
                    @endif
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->menuItem->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->quantity }}x @ ₦{{ number_format($item->unit_price, 0) }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">₦{{ number_format($item->total_price, 0) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Payment Information -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 mb-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Details</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">₦{{ number_format($order->subtotal, 0) }}</span>
            </div>
            @if($order->service_charge > 0)
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Service Charge:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">₦{{ number_format($order->service_charge, 0) }}</span>
            </div>
            @endif
            @if($order->tax_amount > 0)
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Tax:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">₦{{ number_format($order->tax_amount, 0) }}</span>
            </div>
            @endif
            @if($order->delivery_fee > 0)
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Delivery Fee:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">₦{{ number_format($order->delivery_fee, 0) }}</span>
            </div>
            @endif
            @if($order->discount_amount > 0)
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Discount:</span>
                <span class="text-sm font-medium text-green-600 dark:text-green-400">-₦{{ number_format($order->discount_amount, 0) }}</span>
            </div>
            @endif
            <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                <div class="flex justify-between">
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">Total:</span>
                    <span class="text-lg font-bold text-orange-500 dark:text-orange-300">₦{{ number_format($order->total_amount, 0) }}</span>
                </div>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Payment Method:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($order->payment_method) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Payment Status:</span>
                <span class="text-sm font-medium {{ $order->payment_status === 'paid' ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Bank Transfer Payment Details -->
    @if($order->payment_method === 'transfer' && $order->payment_status === 'pending' && $order->bankTransferPayment)
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-200 mb-4">Bank Transfer Details</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-sm text-blue-700 dark:text-blue-300">Account Number:</span>
                <div class="flex items-center gap-2">
                    <span class="text-lg font-mono font-bold text-blue-600 dark:text-blue-400">{{ $order->bankTransferPayment->account_number }}</span>
                    <button onclick="copyAccountNumber()" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-blue-700 dark:text-blue-300">Bank Name:</span>
                <span class="text-sm font-medium text-blue-900 dark:text-blue-200">{{ $order->bankTransferPayment->bank_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-blue-700 dark:text-blue-300">Amount:</span>
                <span class="text-sm font-bold text-blue-900 dark:text-blue-200">₦{{ number_format($order->bankTransferPayment->amount, 0) }}</span>
            </div>
            @if($order->bankTransferPayment->isExpired())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">Account Expired</p>
                        <p class="text-xs text-red-600 dark:text-red-300">This account number has expired</p>
                    </div>
                    <button onclick="regenerateAccount()" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-refresh mr-2"></i>
                        Generate New Account
                    </button>
                </div>
            </div>
            @else
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Time Remaining</p>
                        <p class="text-xs text-yellow-600 dark:text-yellow-300" id="countdown">{{ $order->bankTransferPayment->time_remaining }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Notes -->
    @if($order->notes)
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 mb-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Notes</h3>
        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $order->notes }}</p>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex gap-3 mb-6">
        <a href="{{ route('orders.index') }}" class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-3 px-4 rounded-lg text-center font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Orders
        </a>
        @if($order->order_type === 'pickup' && $order->pickup_code)
        <button onclick="copyPickupCode()" class="flex-1 bg-orange-500 text-white py-3 px-4 rounded-lg text-center font-medium hover:bg-orange-600 transition-colors">
            <i class="fas fa-copy mr-2"></i>
            Copy Code
        </button>
        @endif
    </div>
</div>

<script>
function copyAccountNumber() {
    const accountNumber = '{{ $order->bankTransferPayment->account_number ?? "" }}';
    navigator.clipboard.writeText(accountNumber).then(() => {
        alert('Account number copied to clipboard!');
    });
}

function copyPickupCode() {
    const pickupCode = '{{ $order->pickup_code ?? "" }}';
    navigator.clipboard.writeText(pickupCode).then(() => {
        alert('Pickup code copied to clipboard!');
    });
}

function regenerateAccount() {
    if (confirm('Generate a new bank account for this order?')) {
        fetch('{{ route("bank-transfer.generate-new-account-for-order", $order->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error generating new account');
        });
    }
}

// Update countdown timer
function updateCountdown() {
    const countdownElement = document.getElementById('countdown');
    if (countdownElement) {
        // This would need to be implemented with actual countdown logic
        // For now, just show the time remaining from the server
    }
}

// Update countdown every second
setInterval(updateCountdown, 1000);
</script>
@endsection 