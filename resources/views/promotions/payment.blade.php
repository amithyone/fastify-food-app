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
                        Payment Details
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        Complete your payment to activate your promotion.
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
                                <span class="text-gray-600 dark:text-gray-400">Account Number:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $payment->account_number }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                                <span class="font-bold text-lg text-orange-600 dark:text-orange-400">{{ $payment->formatted_amount }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Plan:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $payment->promotionPlan->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Transfer Instructions -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-700">
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">
                            <i class="fas fa-university mr-2"></i>
                            Bank Transfer Instructions
                        </h3>
                        
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                            <div class="space-y-2 text-gray-600 dark:text-gray-400">
                                <div><strong>Bank Name:</strong> Fastify Bank</div>
                                <div><strong>Account Number:</strong> <span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $payment->account_number }}</span></div>
                                <div><strong>Account Name:</strong> Fastify Food Services</div>
                                <div><strong>Amount:</strong> <span class="font-bold text-orange-600">{{ $payment->formatted_amount }}</span></div>
                                <div><strong>Reference:</strong> <span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $payment->payment_reference }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex items-center justify-between">
                    <button onclick="checkPaymentStatus()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Check Payment Status
                    </button>
                    
                    <button onclick="copyPaymentDetails()" 
                            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg transition-colors">
                        <i class="fas fa-copy mr-2"></i>
                        Copy Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkPaymentStatus() {
    fetch('{{ route("restaurant.promotions.payment.status", ["slug" => $restaurant->slug, "paymentId" => $payment->id]) }}')
        .then(response => response.json())
        .then(data => {
            if (data.is_paid) {
                location.reload();
            } else {
                alert('Payment is still pending. Please complete your bank transfer.');
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
            alert('Error checking payment status. Please try again.');
        });
}

function copyPaymentDetails() {
    const details = `Bank: Fastify Bank\nAccount: ${@json($payment->account_number)}\nAmount: ${@json($payment->formatted_amount)}\nReference: ${@json($payment->payment_reference)}`;
    
    navigator.clipboard.writeText(details).then(() => {
        alert('Payment details copied to clipboard!');
    }).catch(() => {
        alert('Payment details copied to clipboard!');
    });
}

// Auto-check payment status every 30 seconds
setInterval(checkPaymentStatus, 30000);
</script>
@endsection 