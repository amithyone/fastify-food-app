@extends('layouts.app')

@section('title', 'Edit Address - Fastify')

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
            <i class="fas fa-edit text-3xl text-gray-900 dark:text-white"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Edit Address</h1>
        <p class="text-gray-600 dark:text-gray-400">Update your delivery address</p>
    </div>

    <!-- Address Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 mb-24">
        <form action="{{ route('addresses.update', $address) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Label -->
            <div>
                <label for="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Address Label (Optional)
                </label>
                <input 
                    type="text" 
                    id="label" 
                    name="label"
                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    placeholder="e.g., Home, Work, Office"
                    value="{{ old('label', $address->label) }}"
                >
                @error('label')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address Line 1 -->
            <div>
                <label for="address_line_1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Street Address *
                </label>
                <input 
                    type="text" 
                    id="address_line_1" 
                    name="address_line_1"
                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    placeholder="Enter street address"
                    value="{{ old('address_line_1', $address->address_line_1) }}"
                    required
                >
                @error('address_line_1')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address Line 2 -->
            <div>
                <label for="address_line_2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Apartment, Suite, etc. (Optional)
                </label>
                <input 
                    type="text" 
                    id="address_line_2" 
                    name="address_line_2"
                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    placeholder="Apartment, suite, unit, etc."
                    value="{{ old('address_line_2', $address->address_line_2) }}"
                >
                @error('address_line_2')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- City and State -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        City *
                    </label>
                    <input 
                        type="text" 
                        id="city" 
                        name="city"
                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                        placeholder="City"
                        value="{{ old('city', $address->city) }}"
                        required
                    >
                    @error('city')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        State *
                    </label>
                    <input 
                        type="text" 
                        id="state" 
                        name="state"
                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                        placeholder="State"
                        value="{{ old('state', $address->state) }}"
                        required
                    >
                    @error('state')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Postal Code and Country -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Postal Code
                    </label>
                    <input 
                        type="text" 
                        id="postal_code" 
                        name="postal_code"
                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                        placeholder="Postal code"
                        value="{{ old('postal_code', $address->postal_code) }}"
                    >
                    @error('postal_code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Country *
                    </label>
                    <input 
                        type="text" 
                        id="country" 
                        name="country"
                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                        placeholder="Country"
                        value="{{ old('country', $address->country) }}"
                        required
                    >
                    @error('country')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Phone Number (Optional)
                </label>
                <input 
                    type="tel" 
                    id="phone_number" 
                    name="phone_number"
                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    placeholder="Phone number for delivery"
                    value="{{ old('phone_number', $address->phone_number) }}"
                >
                @error('phone_number')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Additional Instructions -->
            <div>
                <label for="additional_instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Delivery Instructions (Optional)
                </label>
                <textarea 
                    id="additional_instructions" 
                    name="additional_instructions"
                    rows="3"
                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    placeholder="Any special delivery instructions..."
                >{{ old('additional_instructions', $address->additional_instructions) }}</textarea>
                @error('additional_instructions')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Set as Default -->
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    id="is_default" 
                    name="is_default"
                    class="rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500 dark:focus:ring-orange-400 bg-white dark:bg-gray-700"
                    value="1"
                    {{ old('is_default', $address->is_default) ? 'checked' : '' }}
                >
                <label for="is_default" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                    Set as default address
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                <i class="fas fa-save mr-2"></i>
                Update Address
            </button>
        </form>
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