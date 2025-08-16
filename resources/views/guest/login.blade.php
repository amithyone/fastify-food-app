@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12">
    <div class="max-w-md mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-orange-500 to-red-500 px-6 py-8 text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-circle text-2xl text-orange-500"></i>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Welcome Back!</h1>
                <p class="text-orange-100">Access your order history</p>
            </div>

            <!-- Content -->
            <div class="p-6">
                @if(session('success'))
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2"></i>
                            <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-2"></i>
                            <div>
                                @foreach($errors->all() as $error)
                                    <p class="text-red-700 dark:text-red-300">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="text-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        Login to Your Account
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        Enter your email address to receive a secure login link
                    </p>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('guest.login') }}" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               placeholder="your@email.com"
                               value="{{ old('email') }}">
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                            <div class="text-sm text-blue-700 dark:text-blue-300">
                                <p class="font-medium mb-1">How it works:</p>
                                <ul class="space-y-1">
                                    <li>• Enter your email address</li>
                                    <li>• We'll send you a secure login link</li>
                                    <li>• Click the link to access your account</li>
                                    <li>• No password required!</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn"
                            class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white py-3 px-4 rounded-lg font-semibold hover:from-orange-600 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-colors">
                        <span id="btnText">Send Login Link</span>
                        <span id="btnLoading" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Sending...
                        </span>
                    </button>

                    <div class="text-center">
                        <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            Back to Home
                        </a>
                    </div>
                </form>

                <!-- QR Code Login Section -->
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            <i class="fas fa-qrcode mr-2 text-orange-500"></i>QR Code Login
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            If you have a QR code from a previous order, you can scan it to login instantly
                        </p>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="w-32 h-32 bg-white rounded-lg flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-qrcode text-4xl text-gray-400"></i>
                            </div>
                            <p class="text-xs text-gray-500">QR Code Scanner</p>
                            <p class="text-xs text-gray-400 mt-1">Coming soon...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');

    form.addEventListener('submit', function(e) {
        // Show loading state
        submitBtn.disabled = true;
        btnText.classList.add('hidden');
        btnLoading.classList.remove('hidden');
    });
});
</script>
@endpush
