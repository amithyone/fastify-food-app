@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-university text-blue-500 mr-2"></i>
                        Virtual Account Payment
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                        Complete your payment using the generated virtual account.
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
                        <p class="text-yellow-700 dark:text-yellow-300">Please complete your payment using the virtual account details below.</p>
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

                <!-- Virtual Account Details -->
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

                    <!-- Virtual Account Details -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-700">
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">
                            <i class="fas fa-credit-card mr-2"></i>
                            Virtual Account Details
                        </h3>
                        
                        <div id="virtualAccountDetails" class="hidden">
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">Bank Name:</span>
                                    <span id="bankName" class="font-semibold text-gray-900 dark:text-white"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">Account Number:</span>
                                    <span id="accountNumber" class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded font-semibold"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">Account Name:</span>
                                    <span id="accountName" class="font-semibold text-gray-900 dark:text-white"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">Amount:</span>
                                    <span id="amount" class="font-bold text-orange-600"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">Reference:</span>
                                    <span id="reference" class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-sm"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">Expires:</span>
                                    <span id="expiresAt" class="text-sm text-gray-600 dark:text-gray-400"></span>
                                </div>
                            </div>
                        </div>

                        <div id="virtualAccountLoading" class="text-center py-8">
                            <div class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Generating Virtual Account...
                            </div>
                        </div>

                        <div id="virtualAccountError" class="hidden">
                            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 border border-red-200 dark:border-red-700">
                                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                                <span id="errorMessage" class="text-red-700 dark:text-red-300"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex items-center justify-between">
                    <button onclick="generateVirtualAccount()" 
                            id="generateBtn"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                        <i class="fas fa-credit-card mr-2"></i>
                        Generate Virtual Account
                    </button>
                    
                    <button onclick="copyVirtualAccountDetails()" 
                            id="copyBtn"
                            class="hidden inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg transition-colors">
                        <i class="fas fa-copy mr-2"></i>
                        Copy Details
                    </button>
                </div>

                <!-- Payment Instructions -->
                <div class="mt-8 bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Payment Instructions
                    </h3>
                    
                    <div class="space-y-3 text-gray-700 dark:text-gray-300">
                        <div class="flex items-start">
                            <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">1</span>
                            <span>Use the generated virtual account details to make a bank transfer</span>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">2</span>
                            <span>Ensure you use the exact reference number provided</span>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">3</span>
                            <span>Payment will be automatically confirmed once received</span>
                        </div>
                        <div class="flex items-start">
                            <span class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5">4</span>
                            <span>Virtual account expires after 24 hours</span>
                        </div>
                    </div>
                </div>

                <!-- Alternative Payment Methods -->
                <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Alternative Payment Methods</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('restaurant.promotions.payment.payvibe', ['slug' => $restaurant->slug, 'paymentId' => $payment->id]) }}" 
                           class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors border border-blue-200 dark:border-blue-700">
                            <i class="fas fa-credit-card text-blue-500 mr-3"></i>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">PayVibe Secure Payment</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Pay with cards, bank transfer, or mobile money</div>
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
let virtualAccountData = null;

function generateVirtualAccount() {
    // Show loading state
    document.getElementById('generateBtn').classList.add('hidden');
    document.getElementById('virtualAccountLoading').classList.remove('hidden');
    document.getElementById('virtualAccountDetails').classList.add('hidden');
    document.getElementById('virtualAccountError').classList.add('hidden');

    // Make API call to generate virtual account
    fetch('{{ route("restaurant.payvibe.virtual-account", ["slug" => $restaurant->slug]) }}', {
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
            // Store virtual account data
            virtualAccountData = data;
            
            // Update UI with virtual account details
            document.getElementById('bankName').textContent = data.bank_name;
            document.getElementById('accountNumber').textContent = data.account_number;
            document.getElementById('accountName').textContent = data.account_name;
            document.getElementById('amount').textContent = data.amount;
            document.getElementById('reference').textContent = data.reference;
            document.getElementById('expiresAt').textContent = data.expires_at ? new Date(data.expires_at).toLocaleString() : '24 hours';
            
            // Show virtual account details
            document.getElementById('virtualAccountLoading').classList.add('hidden');
            document.getElementById('virtualAccountDetails').classList.remove('hidden');
            document.getElementById('copyBtn').classList.remove('hidden');
            
        } else {
            throw new Error(data.message || 'Virtual account generation failed');
        }
    })
    .catch(error => {
        console.error('Virtual account generation error:', error);
        
        // Show error state
        document.getElementById('virtualAccountLoading').classList.add('hidden');
        document.getElementById('virtualAccountError').classList.remove('hidden');
        document.getElementById('errorMessage').textContent = error.message || 'Virtual account generation failed. Please try again.';
        
        // Show generate button again
        document.getElementById('generateBtn').classList.remove('hidden');
    });
}

function copyVirtualAccountDetails() {
    if (!virtualAccountData) return;
    
    const details = `Bank: ${virtualAccountData.bank_name}\nAccount: ${virtualAccountData.account_number}\nName: ${virtualAccountData.account_name}\nAmount: ${virtualAccountData.amount}\nReference: ${virtualAccountData.reference}`;
    
    navigator.clipboard.writeText(details).then(() => {
        alert('Virtual account details copied to clipboard!');
    }).catch(() => {
        alert('Virtual account details copied to clipboard!');
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

// Auto-generate virtual account on page load if not already generated
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('virtualAccountDetails').classList.contains('hidden')) {
        generateVirtualAccount();
    }
});
</script>
@endsection 