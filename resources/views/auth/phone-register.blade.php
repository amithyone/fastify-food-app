@extends('layouts.app')

@section('title', 'Phone Registration - Abuja Eat')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md bg-white dark:bg-gray-900 min-h-screen">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="flex justify-between items-center mb-6">
            <a href="/menu" class="text-gray-600 dark:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <button id="darkModeToggle" class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                <i class="fas fa-sun"></i>
            </button>
        </div>
        <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-plus text-3xl text-gray-900 dark:text-white"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Create Account</h1>
        <p class="text-gray-800 dark:text-gray-400">Register with your phone number</p>
    </div>

    <!-- Phone Registration Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <form id="phoneRegisterForm" class="space-y-6">
            <!-- Name Input -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Full Name
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name"
                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    placeholder="Enter your full name"
                    required
                >
            </div>

            <!-- Phone Number Input -->
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Phone Number
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">+234</span>
                    </div>
                    <input 
                        type="tel" 
                        id="phone_number" 
                        name="phone_number"
                        class="block w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                        placeholder="8012345678"
                        maxlength="11"
                        required
                    >
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Enter your Nigerian phone number (e.g., 08012345678)
                </p>
            </div>

            <!-- Send Code Button -->
            <button 
                type="button" 
                id="sendCodeBtn"
                class="w-full bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-lg font-semibold transition duration-200 flex items-center justify-center gap-2"
                style="background-color: #10b981 !important; color: white !important;"
            >
                <i class="fab fa-whatsapp text-lg"></i>
                Send Verification Code
            </button>

            <!-- Verification Code Section (Hidden initially) -->
            <div id="verificationSection" class="hidden space-y-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                        Enter the 6-digit code sent to your WhatsApp
                    </p>
                    <div id="countdown" class="text-xs text-green-600 dark:text-green-400 font-medium"></div>
                </div>

                <!-- Verification Code Input -->
                <div>
                    <label for="verification_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Verification Code
                    </label>
                    <input 
                        type="text" 
                        id="verification_code" 
                        name="verification_code"
                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white text-center text-lg font-mono tracking-widest"
                        placeholder="000000"
                        maxlength="6"
                        pattern="[0-9]{6}"
                    >
                </div>

                <!-- Verify Button -->
                <button 
                    type="button" 
                    id="verifyBtn"
                    class="w-full bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-lg font-semibold transition duration-200 flex items-center justify-center gap-2"
                >
                    <i class="fas fa-check text-lg"></i>
                    Verify & Create Account
                </button>

                <!-- Resend Code Button -->
                <button 
                    type="button" 
                    id="resendBtn"
                    class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg text-sm transition duration-200"
                    disabled
                >
                    Resend Code (60s)
                </button>
            </div>
        </form>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="hidden text-center py-4">
            <div class="inline-flex items-center gap-2">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-green-500"></div>
                <span class="text-gray-600 dark:text-gray-400">Processing...</span>
            </div>
        </div>

        <!-- Error/Success Messages -->
        <div id="messageContainer" class="mt-4"></div>
    </div>

    <!-- Alternative Options -->
    <div class="mt-6 text-center">
        <p class="text-gray-600 dark:text-gray-400 mb-4">Already have an account?</p>
        <a href="{{ route('phone.login') }}" class="inline-flex items-center bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-lg font-medium transition duration-200">
            <i class="fas fa-sign-in-alt mr-2"></i>
            Login Instead
        </a>
    </div>

    <!-- Back to Menu -->
    <div class="mt-6 text-center">
        <a href="{{ route('menu.index') }}" class="text-green-500 dark:text-green-400 hover:text-green-600 dark:hover:text-green-300 text-sm font-medium">
            <i class="fas fa-arrow-left mr-1"></i>
            Back to Menu
        </a>
    </div>
</div>

<!-- Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-lg z-50 flex justify-around items-center py-1 px-2 max-w-md mx-auto w-full">
    <a href="/menu" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0h6" />
        </svg>
        <span class="text-xs mt-0.5">Home</span>
    </a>
    <a href="/cart" class="flex flex-col items-center text-gray-400 dark:text-gray-400 relative">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M1 1h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 8m12-8l2 8" />
        </svg>
        <span id="cartCount" class="absolute -top-1 -right-1 bg-yellow-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
        <span class="text-xs mt-0.5">Cart</span>
    </a>
    <a href="{{ route('user.orders') }}" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span class="text-xs mt-0.5">Orders</span>
    </a>
    <a href="https://wa.me/" target="_blank" class="flex flex-col items-center text-green-500 dark:text-green-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.72 13.06a6.5 6.5 0 10-2.72 2.72l3.85 1.1a1 1 0 001.26-1.26l-1.1-3.85z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 11a3.5 3.5 0 005 0" />
        </svg>
        <span class="text-xs mt-0.5">WhatsApp</span>
    </a>
    <a href="{{ route('login') }}" class="flex flex-col items-center text-orange-500 dark:text-orange-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="8" r="4" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20v-1a4 4 0 014-4h8a4 4 0 014 4v1" />
        </svg>
        <span class="text-xs mt-0.5">Login</span>
    </a>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const phoneInput = document.getElementById('phone_number');
    const sendCodeBtn = document.getElementById('sendCodeBtn');
    const verifyBtn = document.getElementById('verifyBtn');
    const resendBtn = document.getElementById('resendBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const messageContainer = document.getElementById('messageContainer');
    const countdownElement = document.getElementById('countdown');
    const verificationSection = document.getElementById('verificationSection');

    // Debug: Check if elements are found
    console.log('Elements found:', {
        sendCodeBtn: !!sendCodeBtn,
        verifyBtn: !!verifyBtn,
        resendBtn: !!resendBtn,
        loadingSpinner: !!loadingSpinner,
        messageContainer: !!messageContainer,
        countdownElement: !!countdownElement,
        verificationSection: !!verificationSection
    });

    let countdownInterval;
    let resendCountdown = 60;

    // Format phone number input
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        e.target.value = value;
    });

    // Send verification code
    sendCodeBtn.addEventListener('click', async function() {
        const name = nameInput.value.trim();
        const phoneNumber = phoneInput.value.trim();
        
        console.log('Send code button clicked', { name, phoneNumber });
        
        if (!name) {
            showMessage('Please enter your full name', 'error');
            return;
        }
        
        if (!phoneNumber || phoneNumber.length < 10) {
            showMessage('Please enter a valid phone number', 'error');
            return;
        }

        setLoading(true);
        
        try {
            console.log('Making API request to:', '/api/phone/send-code');
            
            const response = await fetch('/api/phone/send-code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    phone_number: phoneNumber,
                    is_login: false
                })
            });

            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);

            if (data.success) {
                console.log('Success! Showing messages and verification section');
                showMessage(data.message, 'success');
                
                // Show WhatsApp status if available
                if (data.whatsapp_status) {
                    let whatsappMessage = `WhatsApp Status: ${data.whatsapp_status}`;
                    if (data.whatsapp_error) {
                        whatsappMessage += ` (Error: ${data.whatsapp_error})`;
                    }
                    showMessage(whatsappMessage, 'info');
                }
                
                // In development, show the debug code
                if (data.debug_code) {
                    console.log('Debug code found:', data.debug_code);
                    showMessage(`Verification Code: ${data.debug_code}`, 'info');
                }
                
                console.log('Removing hidden class from verification section');
                verificationSection.classList.remove('hidden');
                console.log('Verification section visible:', !verificationSection.classList.contains('hidden'));
                
                startCountdown(data.expires_in);
                startResendCountdown();
            } else {
                console.log('Error response:', data.message);
                showMessage(data.message, 'error');
            }
        } catch (error) {
            console.error('Network error:', error);
            showMessage('Network error. Please try again.', 'error');
        } finally {
            setLoading(false);
        }
    });

    // Verify code
    verifyBtn.addEventListener('click', async function() {
        const name = nameInput.value.trim();
        const phoneNumber = phoneInput.value.trim();
        const verificationCode = document.getElementById('verification_code').value.trim();

        if (!verificationCode || verificationCode.length !== 6) {
            showMessage('Please enter the 6-digit verification code', 'error');
            return;
        }

        setLoading(true);

        try {
            const response = await fetch('/api/phone/verify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    name: name,
                    phone_number: phoneNumber,
                    verification_code: verificationCode,
                    is_registration: true
                })
            });

            const data = await response.json();

            if (data.success) {
                showMessage(data.message, 'success');
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);
            } else {
                showMessage(data.message, 'error');
            }
        } catch (error) {
            showMessage('Network error. Please try again.', 'error');
        } finally {
            setLoading(false);
        }
    });

    // Resend code
    resendBtn.addEventListener('click', async function() {
        const phoneNumber = phoneInput.value.trim();
        
        setLoading(true);

        try {
            const response = await fetch('/api/phone/resend', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    phone_number: phoneNumber
                })
            });

            const data = await response.json();

            if (data.success) {
                showMessage(data.message, 'success');
                startCountdown(data.expires_in);
                startResendCountdown();
            } else {
                showMessage(data.message, 'error');
            }
        } catch (error) {
            showMessage('Network error. Please try again.', 'error');
        } finally {
            setLoading(false);
        }
    });

    function startCountdown(minutes) {
        let timeLeft = minutes * 60;
        
        if (countdownInterval) clearInterval(countdownInterval);
        
        countdownInterval = setInterval(() => {
            const mins = Math.floor(timeLeft / 60);
            const secs = timeLeft % 60;
            
            countdownElement.textContent = `Code expires in ${mins}:${secs.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                countdownElement.textContent = 'Code expired. Please request a new one.';
                countdownElement.classList.add('text-red-600', 'dark:text-red-400');
            }
            
            timeLeft--;
        }, 1000);
    }

    function startResendCountdown() {
        resendCountdown = 60;
        resendBtn.disabled = true;
        resendBtn.textContent = `Resend Code (${resendCountdown}s)`;
        
        const resendInterval = setInterval(() => {
            resendCountdown--;
            resendBtn.textContent = `Resend Code (${resendCountdown}s)`;
            
            if (resendCountdown <= 0) {
                clearInterval(resendInterval);
                resendBtn.disabled = false;
                resendBtn.textContent = 'Resend Code';
            }
        }, 1000);
    }

    function setLoading(loading) {
        if (loading) {
            loadingSpinner.classList.remove('hidden');
            sendCodeBtn.disabled = true;
            verifyBtn.disabled = true;
        } else {
            loadingSpinner.classList.add('hidden');
            sendCodeBtn.disabled = false;
            verifyBtn.disabled = false;
        }
    }

    function showMessage(message, type) {
        let alertClass;
        let icon;
        
        switch(type) {
            case 'success':
                alertClass = 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-200';
                icon = 'fa-check-circle';
                break;
            case 'info':
                alertClass = 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-200';
                icon = 'fa-info-circle';
                break;
            default: // error
                alertClass = 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-200';
                icon = 'fa-exclamation-circle';
                break;
        }

        messageContainer.innerHTML = `
            <div class="p-4 rounded-lg border ${alertClass}">
                <div class="flex items-center">
                    <i class="fas ${icon} mr-2"></i>
                    <span>${message}</span>
                </div>
            </div>
        `;

        setTimeout(() => {
            messageContainer.innerHTML = '';
        }, 5000);
    }

    // Update cart count
    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const cartCount = document.getElementById('cartCount');
        
        if (cartCount) {
            cartCount.textContent = totalItems;
            cartCount.classList.toggle('hidden', totalItems === 0);
        }
    }

    updateCartCount();
});

// Dark Mode Toggle Script
const darkModeToggle = document.getElementById('darkModeToggle');
const html = document.documentElement;

const currentTheme = localStorage.getItem('theme') || 'light';
html.classList.toggle('dark', currentTheme === 'dark');
updateToggleIcon();

darkModeToggle.addEventListener('click', () => {
    html.classList.toggle('dark');
    const theme = html.classList.contains('dark') ? 'dark' : 'light';
    localStorage.setItem('theme', theme);
    updateToggleIcon();
});

function updateToggleIcon() {
    const isDark = html.classList.contains('dark');
    darkModeToggle.innerHTML = isDark 
        ? '<i class="fas fa-moon text-yellow-400"></i>' 
        : '<i class="fas fa-sun text-gray-600"></i>';
}
</script>
@endsection 