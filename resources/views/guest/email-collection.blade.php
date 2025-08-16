@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12">
    <div class="max-w-md mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-orange-500 to-red-500 px-6 py-8 text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-2xl text-orange-500"></i>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Order Successful!</h1>
                <p class="text-orange-100">Order #{{ $order->order_number }}</p>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="text-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        Track Your Order
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        Enter your email to create a guest account and track your order anytime.
                    </p>
                </div>

                <!-- Order Summary -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Order Summary</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Restaurant:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $order->restaurant->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Total:</span>
                            <span class="font-bold text-orange-600">₦{{ number_format($order->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Status:</span>
                            <span class="font-medium text-green-600">{{ ucfirst($order->status) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Email Collection Form -->
                <form id="emailCollectionForm" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Email Address *
                        </label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               placeholder="your@email.com">
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Full Name (Optional)
                        </label>
                        <input type="text" id="name" name="name"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               placeholder="Your full name">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Phone Number (Optional)
                        </label>
                        <input type="tel" id="phone" name="phone"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               placeholder="08012345678">
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                            <div class="text-sm text-blue-700 dark:text-blue-300">
                                <p class="font-medium mb-1">What you'll get:</p>
                                <ul class="space-y-1">
                                    <li>• Order tracking and updates</li>
                                    <li>• Order history access</li>
                                    <li>• Easy reordering</li>
                                    <li>• Email notifications</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn"
                            class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white py-3 px-4 rounded-lg font-semibold hover:from-orange-600 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-colors">
                        <span id="btnText">Create Account & Track Order</span>
                        <span id="btnLoading" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Creating Account...
                        </span>
                    </button>

                    <div class="text-center">
                        <a href="/" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            Skip for now
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-2xl text-green-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Account Created!</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                Check your email for order details and tracking information.
            </p>
            <div id="qrCodeContainer" class="mb-4">
                <!-- QR Code will be generated here -->
            </div>
            <button onclick="closeSuccessModal()" 
                    class="w-full bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-colors">
                Continue
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('emailCollectionForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');
    const successModal = document.getElementById('successModal');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Show loading state
        submitBtn.disabled = true;
        btnText.classList.add('hidden');
        btnLoading.classList.remove('hidden');

        try {
            const formData = new FormData(form);
            const response = await fetch(`{{ route('guest.collect-email', $order->id) }}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                // Show success modal
                successModal.classList.remove('hidden');
                
                // Generate QR code if data provided
                if (data.qr_code_data) {
                    generateQRCode(data.qr_code_data);
                }
                
                // Redirect after a delay
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 3000);
            } else {
                alert(data.message || 'Something went wrong. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Something went wrong. Please try again.');
        } finally {
            // Reset button state
            submitBtn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoading.classList.add('hidden');
        }
    });
});

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
}

function generateQRCode(data) {
    const container = document.getElementById('qrCodeContainer');
    
    // Create QR code using a simple library or service
    // For now, we'll show the data as text
    container.innerHTML = `
        <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-lg">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">QR Code Data:</p>
            <p class="text-xs font-mono text-gray-800 dark:text-gray-200 break-all">
                ${JSON.stringify(data)}
            </p>
        </div>
    `;
}
</script>
@endpush
