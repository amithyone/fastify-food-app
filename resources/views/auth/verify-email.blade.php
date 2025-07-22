@extends('layouts.app')

@section('title', 'Verify Email - Fastify')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md bg-white dark:bg-gray-900 min-h-screen">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('profile.edit') }}" class="text-gray-600 dark:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <button id="darkModeToggle" class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                <i class="fas fa-sun"></i>
            </button>
        </div>
        <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-envelope text-3xl text-gray-900 dark:text-white"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Verify Your Email</h1>
        <p class="text-gray-600 dark:text-gray-400">Check your email for a verification link</p>
    </div>

    <!-- Email Verification Content -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 mb-24">
        <div class="text-center">
            <div class="mb-6">
                <i class="fas fa-envelope-open text-4xl text-orange-500 mb-4"></i>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Thanks for signing up!
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg">
                    <p class="text-green-800 dark:text-green-200 text-sm">
                        A new verification link has been sent to the email address you provided during registration.
                    </p>
                </div>
            @endif

            @if (session('message'))
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                    <p class="text-blue-800 dark:text-blue-200 text-sm">
                        {{ session('message') }}
                    </p>
                </div>
            @endif

            <div class="space-y-4">
                <!-- Resend Verification Email -->
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Resend Verification Email
                    </button>
                </form>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-2xl z-50 flex justify-around items-center py-1 px-2 max-w-md mx-auto w-full">
        <a href="{{ route('menu.index') }}" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
            <!-- Home Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0h6" />
            </svg>
            <span class="text-xs mt-0.5">Home</span>
        </a>
        <a href="#" onclick="openCart()" class="flex flex-col items-center text-gray-400 dark:text-gray-400 relative">
            <!-- Cart Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1" />
                <circle cx="20" cy="21" r="1" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M1 1h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 8m12-8l2 8" />
            </svg>
            <span class="text-xs mt-0.5">Cart</span>
        </a>
        <a href="{{ route('user.orders') }}" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
            <!-- Orders Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="text-xs mt-0.5">Orders</span>
        </a>
        <a href="{{ route('wallet.index') }}" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
            <!-- Wallet Icon -->
            <i class="fas fa-wallet text-xl"></i>
            <span class="text-xs mt-0.5">Wallet</span>
        </a>
        <a href="https://wa.me/" target="_blank" class="flex flex-col items-center text-green-500 dark:text-green-400">
            <!-- WhatsApp Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.72 13.06a6.5 6.5 0 10-2.72 2.72l3.85 1.1a1 1 0 001.26-1.26l-1.1-3.85z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 11a3.5 3.5 0 005 0" />
            </svg>
            <span class="text-xs mt-0.5">WhatsApp</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center text-orange-500 dark:text-orange-300">
            <!-- Profile Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 20v-1a4 4 0 014-4h8a4 4 0 014 4v1" />
            </svg>
            <span class="text-xs mt-0.5">Profile</span>
        </a>
    </nav>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dark mode toggle
    const darkModeToggle = document.getElementById('darkModeToggle');
    const html = document.documentElement;
    
    // Check for saved theme preference or default to light mode
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        html.classList.add('dark');
        darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
    } else {
        html.classList.remove('dark');
        darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    }
    
    darkModeToggle.addEventListener('click', function() {
        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            localStorage.theme = 'light';
            darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        } else {
            html.classList.add('dark');
            localStorage.theme = 'dark';
            darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        }
    });
});
</script>
@endsection
