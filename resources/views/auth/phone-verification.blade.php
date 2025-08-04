@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#f1ecdc] dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900">
                <i class="fas fa-mobile-alt text-orange-600 dark:text-orange-400 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Phone Verification
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Enter your phone number and verification code
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form class="space-y-6" id="phoneVerificationForm">
                @csrf
                
                <!-- Phone Number Input -->
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Phone Number
                    </label>
                    <div class="mt-1">
                        <input id="phone_number" name="phone_number" type="tel" required 
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                            placeholder="08012345678"
                            value="{{ old('phone_number') }}">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Enter your Nigerian phone number (e.g., 08012345678)
                    </p>
                </div>

                <!-- Verification Code Input -->
                <div>
                    <label for="verification_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Verification Code
                    </label>
                    <div class="mt-1">
                        <input id="verification_code" name="verification_code" type="text" required 
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                            placeholder="123456"
                            maxlength="6"
                            pattern="[0-9]{6}">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Enter the 6-digit code sent to your phone
                    </p>
                </div>

                <!-- Error Messages -->
                <div id="errorMessage" class="hidden">
                    <div class="rounded-md bg-red-50 dark:bg-red-900/20 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-800 dark:text-red-200" id="errorText"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Success Messages -->
                <div id="successMessage" class="hidden">
                    <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-800 dark:text-green-200" id="successText"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <button type="button" id="sendCodeBtn" 
                        class="flex-1 bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded-md text-sm font-medium transition">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Send Code
                    </button>
                    <button type="submit" id="verifyBtn" disabled
                        class="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white py-2 px-4 rounded-md text-sm font-medium transition">
                        <i class="fas fa-check mr-2"></i>
                        Verify
                    </button>
                </div>

                <!-- Resend Code -->
                <div class="text-center">
                    <button type="button" id="resendBtn" class="text-sm text-orange-600 hover:text-orange-500 hidden">
                        <i class="fas fa-redo mr-1"></i>
                        Resend Code
                    </button>
                    <div id="resendTimer" class="text-sm text-gray-500 dark:text-gray-400">
                        Resend available in <span id="countdown">60</span> seconds
                    </div>
                </div>
            </form>
        </div>

        <!-- Help Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Need Help?</h4>
            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-question-circle text-orange-500 mt-0.5 mr-2"></i>
                    <span>Make sure you entered the correct phone number</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-clock text-orange-500 mt-0.5 mr-2"></i>
                    <span>Codes expire after 10 minutes</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-mobile-alt text-orange-500 mt-0.5 mr-2"></i>
                    <span>Check your SMS messages</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('phoneVerificationForm');
    const sendCodeBtn = document.getElementById('sendCodeBtn');
    const verifyBtn = document.getElementById('verifyBtn');
    const resendBtn = document.getElementById('resendBtn');
    const resendTimer = document.getElementById('resendTimer');
    const countdown = document.getElementById('countdown');
    const errorMessage = document.getElementById('errorMessage');
    const successMessage = document.getElementById('successMessage');
    const errorText = document.getElementById('errorText');
    const successText = document.getElementById('successText');

    let countdownInterval;

    // Send verification code
    sendCodeBtn.addEventListener('click', function() {
        const phoneNumber = document.getElementById('phone_number').value;
        
        if (!phoneNumber) {
            showError('Please enter your phone number');
            return;
        }

        sendCodeBtn.disabled = true;
        sendCodeBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';

        fetch('{{ route("phone.verification.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ phone_number: phoneNumber })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message);
                startCountdown();
                verifyBtn.disabled = false;
            } else {
                showError(data.error);
            }
        })
        .catch(error => {
            showError('Network error. Please try again.');
        })
        .finally(() => {
            sendCodeBtn.disabled = false;
            sendCodeBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send Code';
        });
    });

    // Verify code
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const phoneNumber = document.getElementById('phone_number').value;
        const verificationCode = document.getElementById('verification_code').value;
        
        if (!phoneNumber || !verificationCode) {
            showError('Please fill in all fields');
            return;
        }

        verifyBtn.disabled = true;
        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Verifying...';

        fetch('{{ route("phone.verification.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                phone_number: phoneNumber,
                verification_code: verificationCode 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message);
                setTimeout(() => {
                    window.location.href = '{{ route("dashboard") }}';
                }, 2000);
            } else {
                showError(data.error);
            }
        })
        .catch(error => {
            showError('Network error. Please try again.');
        })
        .finally(() => {
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Verify';
        });
    });

    // Resend code
    resendBtn.addEventListener('click', function() {
        const phoneNumber = document.getElementById('phone_number').value;
        
        if (!phoneNumber) {
            showError('Please enter your phone number');
            return;
        }

        resendBtn.disabled = true;
        resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Resending...';

        fetch('{{ route("phone.verification.resend") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ phone_number: phoneNumber })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message);
                startCountdown();
            } else {
                showError(data.error);
            }
        })
        .catch(error => {
            showError('Network error. Please try again.');
        })
        .finally(() => {
            resendBtn.disabled = false;
            resendBtn.innerHTML = '<i class="fas fa-redo mr-1"></i>Resend Code';
        });
    });

    function showError(message) {
        errorText.textContent = message;
        errorMessage.classList.remove('hidden');
        successMessage.classList.add('hidden');
    }

    function showSuccess(message) {
        successText.textContent = message;
        successMessage.classList.remove('hidden');
        errorMessage.classList.add('hidden');
    }

    function startCountdown() {
        let timeLeft = 60;
        resendBtn.classList.add('hidden');
        resendTimer.classList.remove('hidden');
        
        countdownInterval = setInterval(() => {
            timeLeft--;
            countdown.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                resendBtn.classList.remove('hidden');
                resendTimer.classList.add('hidden');
            }
        }, 1000);
    }
});
</script>
@endsection 