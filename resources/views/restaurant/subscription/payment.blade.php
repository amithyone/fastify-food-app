@extends('layouts.app')

@section('title', 'Subscription Payment - ' . $restaurant->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Subscription Payment</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Complete payment to upgrade your subscription</p>
                </div>
                <a href="{{ route('restaurant.subscription.index', $restaurant->slug) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Subscription
                </a>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Payment Summary</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Plan Details</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Plan Type:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $payment->plan_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                                <span class="font-bold text-lg text-gray-900 dark:text-white">{{ $payment->formatted_amount }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Payment Reference:</span>
                                <span class="font-mono text-sm text-gray-500 dark:text-gray-400">{{ $payment->payment_reference }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    bg-{{ $payment->status_color }}-100 text-{{ $payment->status_color }}-800 dark:bg-{{ $payment->status_color }}-900 dark:text-{{ $payment->status_color }}-200">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Plan Features</h4>
                        <div class="space-y-2">
                            @php
                                $plan = \App\Models\SubscriptionPlan::where('slug', $payment->plan_type)->first();
                            @endphp
                            @if($plan)
                                @foreach(array_slice($plan->features, 0, 5) as $feature)
                                    <div class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2 text-sm"></i>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                                    </div>
                                @endforeach
                                @if(count($plan->features) > 5)
                                    <div class="text-sm text-gray-500 dark:text-gray-400">+{{ count($plan->features) - 5 }} more features</div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Payment Method</h3>
            </div>
            <div class="p-6">
                <!-- Virtual Account Payment -->
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Bank Transfer (PayVibe)</h4>
                    
                    @if(!$payment->virtual_account_number)
                        <!-- Generate Virtual Account Button -->
                        <div class="text-center py-8">
                            <button id="generateBtn" onclick="generateVirtualAccount()" 
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <i class="fas fa-credit-card mr-2"></i>
                                Generate Virtual Account
                            </button>
                        </div>
                        
                        <!-- Loading State -->
                        <div id="virtualAccountLoading" class="hidden text-center py-8">
                            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-blue-600">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Generating virtual account...
                            </div>
                        </div>
                        
                        <!-- Error State -->
                        <div id="virtualAccountError" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <div class="flex">
                                <i class="fas fa-exclamation-triangle text-red-400 mt-1"></i>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Error</h3>
                                    <p id="errorMessage" class="mt-2 text-sm text-red-700 dark:text-red-300"></p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Virtual Account Details -->
                    <div id="virtualAccountDetails" class="{{ $payment->virtual_account_number ? '' : 'hidden' }}">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-university text-blue-600 mr-2"></i>
                                <h5 class="text-lg font-medium text-blue-900 dark:text-blue-100">Virtual Account Details</h5>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">Bank Name</label>
                                    <p id="bankName" class="text-lg font-semibold text-blue-900 dark:text-blue-100">{{ $payment->bank_name ?? 'Loading...' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">Account Number</label>
                                    <div class="flex items-center">
                                        <p id="accountNumber" class="text-lg font-mono font-semibold text-blue-900 dark:text-blue-100 mr-2">{{ $payment->virtual_account_number ?? 'Loading...' }}</p>
                                        <button id="copyBtn" onclick="copyAccountNumber()" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">Account Name</label>
                                    <p id="accountName" class="text-lg font-semibold text-blue-900 dark:text-blue-100">{{ $payment->account_name ?? 'Loading...' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">Amount to Pay</label>
                                    <p id="amount" class="text-lg font-bold text-blue-900 dark:text-blue-100">{{ $payment->formatted_amount }}</p>
                                </div>
                            </div>
                            
                            <div class="bg-white dark:bg-gray-700 rounded-lg p-4 mb-4">
                                <h6 class="font-medium text-gray-900 dark:text-white mb-2">Payment Instructions:</h6>
                                <ol class="list-decimal list-inside space-y-1 text-sm text-gray-700 dark:text-gray-300">
                                    <li>Transfer exactly <strong>{{ $payment->formatted_amount }}</strong> to the account above</li>
                                    <li>Use <strong>{{ $payment->payment_reference }}</strong> as the payment reference</li>
                                    <li>Payment will be confirmed automatically within 5-10 minutes</li>
                                    <li>Your subscription will be upgraded once payment is confirmed</li>
                                </ol>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-clock mr-1"></i>
                                    Expires: {{ $payment->expires_at ? $payment->expires_at->format('M d, Y H:i') : '24 hours' }}
                                </div>
                                <button onclick="checkPaymentStatus()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    <i class="fas fa-sync mr-2"></i>
                                    Check Payment Status
                                </button>
                            </div>
                        </div>
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
    fetch('{{ route("restaurant.subscription.virtual-account", ["slug" => $restaurant->slug, "paymentId" => $payment->id]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
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
            
            // Show virtual account details
            document.getElementById('virtualAccountLoading').classList.add('hidden');
            document.getElementById('virtualAccountDetails').classList.remove('hidden');
            document.getElementById('copyBtn').classList.remove('hidden');
            
        } else {
            throw new Error(data.message || 'Virtual account generation failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('virtualAccountLoading').classList.add('hidden');
        document.getElementById('errorMessage').textContent = error.message;
        document.getElementById('virtualAccountError').classList.remove('hidden');
        document.getElementById('generateBtn').classList.remove('hidden');
    });
}

function copyAccountNumber() {
    const accountNumber = document.getElementById('accountNumber').textContent;
    navigator.clipboard.writeText(accountNumber).then(() => {
        // Show success message
        const copyBtn = document.getElementById('copyBtn');
        const originalIcon = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="fas fa-check"></i>';
        copyBtn.classList.add('text-green-600');
        
        setTimeout(() => {
            copyBtn.innerHTML = originalIcon;
            copyBtn.classList.remove('text-green-600');
        }, 2000);
    });
}

function checkPaymentStatus() {
    // Redirect to subscription page to check status
    window.location.href = '{{ route("restaurant.subscription.index", $restaurant->slug) }}';
}

// Auto-generate virtual account if not already generated
@if(!$payment->virtual_account_number)
    document.addEventListener('DOMContentLoaded', function() {
        generateVirtualAccount();
    });
@endif
</script>
@endsection
