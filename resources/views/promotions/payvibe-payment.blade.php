@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-credit-card text-blue-500 mr-2"></i>
                        PayVibe Payment
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        Complete your payment securely with PayVibe.
                    </p>
                </div>

                <!-- Payment Status -->
                <div id="paymentStatus" class="mb-8">
                    @if($payment->status === 'pending')
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6 border border-yellow-200 dark:border-yellow-700">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-clock text-yellow-600 mr-2"></i>
                            <h3 class="text-lg font-medium text-yellow-800 dark:text-yellow-200">Payment Pending</h3>
                        </div>
                        <p class="text-yellow-700 dark:text-yellow-300">Please complete your payment to activate your promotion.</p>
                    </div>
                    @elseif($payment->status === 'paid')
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 border border-green-200 dark:border-green-700">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <h3 class="text-lg font-medium text-green-800 dark:text-green-200">Payment Confirmed</h3>
                        </div>
                        <p class="text-green-700 dark:text-green-300">Your promotion is now active!</p>
                    </div>
                    @endif
                </div>

                <!-- Payment Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Payment Information -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Information</h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Payment Reference:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $payment->payment_reference }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                                <span class="font-bold text-lg text-orange-600 dark:text-orange-400">{{ $payment->formatted_amount }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Plan:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $payment->promotionPlan->name }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Restaurant:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $restaurant->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- PayVibe Payment Options -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-700">
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Secure Payment Options
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-gray-700 dark:text-gray-300">PayVibe Secure Gateway</span>
                                    <i class="fas fa-lock text-green-500"></i>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Multiple payment methods available including cards, bank transfers, and mobile money.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PayVibe Payment Button -->
                <div class="mt-8">
                    <div id="payvibeButton" class="text-center">
                        <button onclick="initializePayVibePayment()" 
                                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-lg font-semibold rounded-lg transition-colors shadow-lg">
                            <i class="fas fa-credit-card mr-2"></i>
                            Pay with PayVibe
                        </button>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            Secure payment powered by PayVibe
                        </p>
                    </div>

                    <div id="payvibeLoading" class="text-center hidden">
                        <div class="inline-flex items-center px-6 py-3 bg-blue-600 text-white text-lg font-semibold rounded-lg">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Initializing Payment...
                        </div>
                    </div>

                    <div id="payvibeError" class="text-center hidden">
                        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 border border-red-200 dark:border-red-700">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                            <span id="errorMessage" class="text-red-700 dark:text-red-300"></span>
                        </div>
                    </div>
                </div>

                <!-- Alternative Payment Methods -->
                <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Alternative Payment Methods</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('restaurant.promotions.payment.virtual-account', ['slug' => $restaurant->slug, 'paymentId' => $payment->id]) }}" 
                           class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors border border-green-200 dark:border-green-700">
                            <i class="fas fa-university text-green-500 mr-3"></i>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Virtual Account</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Generate virtual account for bank transfer</div>
                            </div>
                        </a>
                        <a href="{{ route('restaurant.promotions.payment.show', ['slug' => $restaurant->slug, 'paymentId' => $payment->id]) }}" 
                           class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <i class="fas fa-university text-blue-500 mr-3"></i>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Bank Transfer</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Pay via manual bank transfer</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function initializePayVibePayment() {
    // Show loading state
    document.getElementById('payvibeButton').classList.add('hidden');
    document.getElementById('payvibeLoading').classList.remove('hidden');
    document.getElementById('payvibeError').classList.add('hidden');

    // Make API call to initialize PayVibe payment
    fetch('{{ route("restaurant.payvibe.initialize", ["slug" => $restaurant->slug]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            payment_id: {{ $payment->id }}
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to PayVibe payment page
            window.location.href = data.authorization_url;
        } else {
            throw new Error(data.message || 'Payment initialization failed');
        }
    })
    .catch(error => {
        console.error('PayVibe payment error:', error);
        
        // Show error state
        document.getElementById('payvibeLoading').classList.add('hidden');
        document.getElementById('payvibeButton').classList.remove('hidden');
        document.getElementById('payvibeError').classList.remove('hidden');
        document.getElementById('errorMessage').textContent = error.message || 'Payment initialization failed. Please try again.';
    });
}

function checkPaymentStatus() {
    fetch('{{ route("restaurant.promotions.payment.status", ["slug" => $restaurant->slug, "paymentId" => $payment->id]) }}')
        .then(response => response.json())
        .then(data => {
            if (data.is_paid) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
        });
}

// Auto-check payment status every 30 seconds
setInterval(checkPaymentStatus, 30000);
</script>
@endsection 