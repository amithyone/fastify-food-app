@extends('layouts.app')

@section('title', 'Profile - Fastify')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md bg-white dark:bg-gray-900 min-h-screen">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <button id="darkModeToggle" class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-white">
                <i class="fas fa-sun"></i>
            </button>
        </div>
        <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user text-3xl text-gray-900 dark:text-white"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Profile</h1>
        <p class="text-gray-600 dark:text-white">Manage your account settings</p>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 gap-3">
            <!-- Dashboard Button -->
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center gap-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg">
                <i class="fas fa-home text-lg"></i>
                <span>Go to Dashboard</span>
            </a>
            
            <!-- Restaurant Onboarding Button -->
            <a href="{{ route('restaurant.onboarding') }}" class="flex items-center justify-center gap-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg">
                <i class="fas fa-store text-lg"></i>
                <span>Add Your Restaurant</span>
            </a>
            
            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt text-lg"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Email Verification Notice -->
    @if(!Auth::user()->hasVerifiedEmail())
        <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>Please verify your email address to access all features.</span>
                </div>
                <a href="{{ route('verification.notice') }}" class="text-yellow-800 hover:text-yellow-900 font-medium">
                    Verify Now
                </a>
            </div>
        </div>
    @endif

    <!-- Profile Information -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Profile Information</h2>
        @include('profile.partials.update-profile-information-form')
    </div>

    <!-- Address Management -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 mb-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">My Addresses</h2>
            <a href="{{ route('addresses.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                <i class="fas fa-plus mr-1"></i>Add Address
            </a>
        </div>
        
        @php
            $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        @endphp
        
        @if($addresses->count() > 0)
            <div class="space-y-3">
                @foreach($addresses as $address)
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 {{ $address->is_default ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-700' : '' }}">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="font-medium text-gray-900 dark:text-white">{{ $address->label ?: 'Address' }}</h3>
                                    @if($address->is_default)
                                        <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full">Default</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 dark:text-white mb-1">{{ $address->address_line_1 }}</p>
                                @if($address->address_line_2)
                                    <p class="text-sm text-gray-600 dark:text-white mb-1">{{ $address->address_line_2 }}</p>
                                @endif
                                <p class="text-sm text-gray-600 dark:text-white mb-1">{{ $address->city }}, {{ $address->state }}</p>
                                @if($address->postal_code)
                                    <p class="text-sm text-gray-600 dark:text-white mb-1">{{ $address->postal_code }}</p>
                                @endif
                                @if($address->phone_number)
                                    <p class="text-sm text-gray-600 dark:text-white mb-1">{{ $address->phone_number }}</p>
                                @endif
                                @if($address->additional_instructions)
                                    <p class="text-sm text-gray-500 dark:text-gray-300 italic">Note: {{ $address->additional_instructions }}</p>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('addresses.edit', $address) }}" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$address->is_default)
                                    <form action="{{ route('addresses.default', $address) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-500 hover:text-green-600 dark:text-green-400 dark:hover:text-green-300">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this address?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-map-marker-alt text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-500 dark:text-white mb-4">No addresses saved yet</p>
                <a href="{{ route('addresses.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                    Add Your First Address
                </a>
            </div>
        @endif
    </div>

    <!-- Change Password -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Change Password</h2>
        @include('profile.partials.update-password-form')
    </div>

    <!-- Delete Account -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 mb-24">
        <h2 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-4">Delete Account</h2>
        @include('profile.partials.delete-user-form')
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
