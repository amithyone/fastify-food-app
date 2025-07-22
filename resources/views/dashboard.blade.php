@extends('layouts.app')

@section('title', 'Dashboard - Abuja Eat')

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
            <i class="fas fa-user text-3xl text-white"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Welcome back!</h1>
        <p class="text-gray-600 dark:text-gray-400">{{ Auth::user()->name ?? 'User' }}</p>
    </div>

    <!-- Dashboard Cards -->
    <div class="space-y-4">
        <!-- Menu Link -->
        <a href="{{ route('menu.index') }}" class="block p-6 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800 hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-utensils text-2xl text-orange-500"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Order Food</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Browse our delicious menu</p>
                </div>
            </div>
        </a>

        <!-- Cart Link -->
        <a href="{{ route('cart') }}" class="block p-6 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-shopping-cart text-2xl text-purple-500"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">My Cart</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">View your cart items</p>
                </div>
            </div>
        </a>

        <!-- WhatsApp Link -->
        <a href="https://wa.me/" target="_blank" class="block p-6 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fab fa-whatsapp text-2xl text-green-500"></i>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Contact Us</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Chat on WhatsApp</p>
                </div>
            </div>
        </a>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="block">
            @csrf
            <button type="submit" class="w-full p-6 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors text-left">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-sign-out-alt text-2xl text-red-500"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Logout</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Sign out of your account</p>
                    </div>
                </div>
            </button>
        </form>
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
    <a href="/orders" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <span class="text-xs mt-0.5">Orders</span>
    </a>
    <a href="https://wa.me/" target="_blank" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
        <i class="fab fa-whatsapp text-xl"></i>
        <span class="text-xs mt-0.5">WhatsApp</span>
    </a>
    <a href="/phone/login" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <span class="text-xs mt-0.5">Login</span>
    </a>
</nav>

<script>
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
