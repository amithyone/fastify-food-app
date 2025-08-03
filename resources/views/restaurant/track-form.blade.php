@extends('layouts.app')

@section('title', $restaurant->name . ' - Track Order')

@section('content')
<!-- Fixed/Sticky Top Bar: always at the very top -->
<div class="fixed top-0 left-0 right-0 z-50 bg-[#f1ecdc] dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 py-2 shadow-lg max-w-md mx-auto w-full mt-15">
    <div class="flex items-center gap-2 px-4">
        <!-- Back Button -->
        <a href="{{ route('restaurant.dashboard', $restaurant->slug) }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-orange-100 dark:bg-gray-700 text-orange-500 dark:text-orange-300 transition hover:bg-orange-200 dark:hover:bg-gray-600">
            <i class="fas fa-arrow-left"></i>
        </a>
        <!-- Search Bar -->
        <div class="flex-1 relative">
            <input type="text" id="searchInput" placeholder="Search for dishes..." class="w-full px-4 py-2 pl-10 border border-gray-200 dark:border-gray-700 rounded-full focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-300 bg-gray-50 dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-100">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-300"></i>
        </div>
        <!-- Theme Toggle Button -->
        <button id="themeToggle" class="w-10 h-10 flex items-center justify-center rounded-full bg-orange-100 dark:bg-gray-700 text-orange-500 dark:text-yellow-300 transition hover:bg-orange-200 dark:hover:bg-gray-600">
            <i id="themeIcon" class="fas fa-moon"></i>
        </button>
    </div>
</div>

<div class="w-full min-h-screen bg-[#f1ecdc] dark:bg-gray-900">
    <div class="max-w-md mx-auto px-4 py-4">
        <!-- Header Section -->
        <div class="text-center mb-8" style="margin-top: 60px;">
            <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-search text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Track Order
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Enter tracking code to find customer order
            </p>
        </div>

        <!-- Restaurant Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <div class="flex items-center space-x-3">
                @if($restaurant->logo)
                    <img src="{{ $restaurant->logo_url ?? \App\Helpers\PWAHelper::getPlaceholderImage('square') }}" alt="{{ $restaurant->name }}" class="w-12 h-12 rounded-lg object-contain bg-gray-100 dark:bg-gray-700">
                @else
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-utensils text-white"></i>
                    </div>
                @endif
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $restaurant->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Order Tracking</p>
                </div>
            </div>
        </div>

        <!-- Tracking Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            @if ($errors->any())
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-200 px-4 py-3 rounded-md">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('restaurant.track', $restaurant->slug) }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="tracking_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-key mr-2 text-green-500"></i>
                        Tracking Code
                    </label>
                    <div class="relative">
                        <input id="tracking_code" name="tracking_code" type="text" required 
                            class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white text-center text-lg font-mono tracking-widest"
                            placeholder="XXXX"
                            maxlength="4"
                            pattern="[A-Z0-9]{4}"
                            style="text-transform: uppercase; letter-spacing: 0.5em;"
                            oninput="this.value = this.value.toUpperCase()">
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-center">
                        Enter the 4-digit code from customer's order
                    </p>
                </div>

                <div>
                    <button type="submit" 
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-sm font-medium text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-search mr-2"></i>
                        Find Order
                    </button>
                </div>
            </form>

            <!-- Additional Info Cards -->
            <div class="grid grid-cols-1 gap-4 mt-6">
                <!-- Quick Actions -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4 border border-green-200 dark:border-green-700">
                    <div class="flex items-center mb-2">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-list text-white text-sm"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-green-900 dark:text-green-200">Quick Actions</h3>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('restaurant.orders', $restaurant->slug) }}" 
                            class="block text-xs text-green-700 dark:text-green-300 hover:text-green-800 dark:hover:text-green-200">
                            <i class="fas fa-shopping-cart mr-1"></i>
                            View All Orders
                        </a>
                        <a href="{{ route('restaurant.dashboard', $restaurant->slug) }}" 
                            class="block text-xs text-green-700 dark:text-green-300 hover:text-green-800 dark:hover:text-green-200">
                            <i class="fas fa-chart-line mr-1"></i>
                            Dashboard
                        </a>
                    </div>
                </div>

                <!-- Order Management -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                    <div class="flex items-center mb-2">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-cog text-white text-sm"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-200">Order Management</h3>
                    </div>
                    <p class="text-xs text-blue-700 dark:text-blue-300">
                        Use this tool to quickly find and manage customer orders by their tracking code. Only works for orders from this restaurant.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- Bottom padding for fixed bottom menu -->
    <div class="h-20"></div>
</div>

<!-- Bottom Navigation Menu -->
<x-restaurant-bottom-nav :restaurant="$restaurant" />

<script>
// Tracking code input handling
document.getElementById('tracking_code').addEventListener('input', function(e) {
    // Remove any non-alphanumeric characters
    this.value = this.value.replace(/[^A-Z0-9]/gi, '');
    
    // Limit to 4 characters
    if (this.value.length > 4) {
        this.value = this.value.slice(0, 4);
    }
});

// Theme toggle functionality
document.getElementById('themeToggle').addEventListener('click', function() {
    const html = document.documentElement;
    const themeIcon = document.getElementById('themeIcon');
    
    if (html.classList.contains('dark')) {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
        themeIcon.className = 'fas fa-moon';
    } else {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
        themeIcon.className = 'fas fa-sun';
    }
});

// Initialize theme
document.addEventListener('DOMContentLoaded', function() {
    const theme = localStorage.getItem('theme') || 'light';
    const html = document.documentElement;
    const themeIcon = document.getElementById('themeIcon');
    
    if (theme === 'dark') {
        html.classList.add('dark');
        themeIcon.className = 'fas fa-sun';
    } else {
        html.classList.remove('dark');
        themeIcon.className = 'fas fa-moon';
    }
});
</script>
@endsection 