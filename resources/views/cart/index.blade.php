@extends('layouts.app')

@section('title', 'Shopping Cart - Fastify')

@section('content')
<!-- Fixed/Sticky Top Bar: always at the very top -->
<div class="fixed top-0 left-0 right-0 z-50 bg-[#f1ecdc] dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 py-2 shadow-lg max-w-md mx-auto w-full mt-15">
    <div class="flex items-center gap-2 px-4">
        <!-- Back Button -->
        <button onclick="history.back()" class="w-10 h-10 flex items-center justify-center rounded-full bg-orange-100 dark:bg-gray-700 text-orange-500 dark:text-orange-300 transition hover:bg-orange-200 dark:hover:bg-gray-600">
            <i class="fas fa-arrow-left"></i>
        </button>
        <!-- Title -->
        <div class="flex-1 text-center">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Shopping Cart</h1>
        </div>
        <!-- Theme Toggle Button -->
        <button id="themeToggle" class="w-10 h-10 flex items-center justify-center rounded-full bg-orange-100 dark:bg-gray-700 text-orange-500 dark:text-yellow-300 transition hover:bg-orange-200 dark:hover:bg-gray-600">
            <i id="themeIcon" class="fas fa-moon"></i>
        </button>
    </div>
</div>

<div class="w-full min-h-screen bg-[#f1ecdc] dark:bg-gray-900">
    <div class="max-w-md mx-auto px-4 py-4">
        <!-- Content starts after fixed header -->
        <div style="margin-top: 60px;">
            @php
                $cartItems = $cartItems ?? [];
            @endphp

            <!-- Email Verification Notice -->
            @auth
                @if(!Auth::user()->hasVerifiedEmail())
                    <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span>Please verify your email address to complete orders.</span>
                            </div>
                            <a href="{{ route('verification.notice') }}" class="text-yellow-800 hover:text-yellow-900 font-medium">
                                Verify Now
                            </a>
                        </div>
                    </div>
                @endif
            @endauth

            @if($cartItems && count($cartItems) > 0)
                <div class="space-y-4">
                    <!-- Cart Items -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Cart Items</h2>
                        </div>
                        
                        <div class="p-4">
                            @foreach($cartItems as $restaurantCart)
                                <div class="mb-4 last:mb-0">
                                    <!-- Restaurant Header -->
                                    <div class="flex items-center mb-3 p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                                        @if($restaurantCart['restaurant']->logo)
                                            <img src="{{ Storage::url($restaurantCart['restaurant']->logo) }}" 
                                                 alt="{{ $restaurantCart['restaurant']->name }}" 
                                                 class="w-10 h-10 rounded-lg object-cover mr-3">
                                        @else
                                            <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-utensils text-white"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $restaurantCart['restaurant']->name }}</h3>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $restaurantCart['restaurant']->cuisine_type ?? 'Restaurant' }}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Restaurant Items -->
                                    <div class="space-y-2">
                                        @foreach($restaurantCart['items'] as $item)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                                <div class="flex items-center">
                                                    @if($item['image'])
                                                        <img src="{{ Storage::url($item['image']) }}" 
                                                             alt="{{ $item['name'] }}" 
                                                             class="w-12 h-12 rounded-lg object-cover mr-3">
                                                    @else
                                                        <img src="{{ \App\Helpers\PWAHelper::getPlaceholderImage('square') }}" 
                                                             alt="{{ $item['name'] }}" 
                                                             class="w-12 h-12 rounded-lg object-cover mr-3">
                                                    @endif
                                                    <div>
                                                        <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ $item['name'] }}</h4>
                                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $restaurantCart['restaurant']->currency }}{{ number_format($item['price'] / 100, 2) }}</p>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex items-center space-x-2">
                                                    <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800">
                                                        <button onclick="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})" 
                                                                class="px-2 py-1 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            <i class="fas fa-minus text-xs"></i>
                                                        </button>
                                                        <span class="px-2 py-1 text-gray-900 dark:text-white font-medium text-sm">{{ $item['quantity'] }}</span>
                                                        <button onclick="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})" 
                                                                class="px-2 py-1 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            <i class="fas fa-plus text-xs"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="text-right">
                                                        <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $restaurantCart['restaurant']->currency }}{{ number_format($item['total'] / 100, 2) }}</p>
                                                    </div>
                                                    
                                                    <button onclick="removeItem({{ $item['id'] }})" 
                                                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20">
                                                        <i class="fas fa-trash text-sm"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Restaurant Total -->
                                    <div class="mt-3 p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                                        <div class="flex justify-between items-center">
                                            <span class="font-semibold text-gray-900 dark:text-white text-sm">Restaurant Total:</span>
                                            <span class="font-bold text-orange-600 dark:text-orange-400 text-sm">{{ $restaurantCart['restaurant']->currency }}{{ number_format($restaurantCart['total'] / 100, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Order Summary</h2>
                        </div>
                        
                        <div class="p-4">
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400 text-sm">Subtotal:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ $cartItems[0]['restaurant']->currency ?? '₦' }}{{ number_format($total / 100, 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400 text-sm">Delivery Fee:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ $cartItems[0]['restaurant']->currency ?? '₦' }}0.00</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400 text-sm">Tax:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ $cartItems[0]['restaurant']->currency ?? '₦' }}0.00</span>
                                </div>
                                
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                    <div class="flex justify-between">
                                        <span class="text-base font-bold text-gray-900 dark:text-white">Total:</span>
                                        <span class="text-base font-bold text-orange-600 dark:text-orange-400">{{ $cartItems[0]['restaurant']->currency ?? '₦' }}{{ number_format($total / 100, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 space-y-3">
                                <button onclick="proceedToCheckout()" 
                                        class="w-full bg-orange-500 text-white py-3 px-4 rounded-lg hover:bg-orange-600 transition-colors font-semibold shadow-lg text-sm">
                                    <i class="fas fa-credit-card mr-2"></i>Proceed to Checkout
                                </button>
                                
                                <button onclick="clearCart()" 
                                        class="w-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-sm">
                                    <i class="fas fa-trash mr-2"></i>Clear Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty Cart -->
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shopping-cart text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Your cart is empty</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 text-sm">Start adding items from our amazing restaurants</p>
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors shadow-lg text-sm">
                        <i class="fas fa-utensils mr-2"></i>Explore Restaurants
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Theme toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    
    // Check for saved theme preference or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.classList.toggle('dark', currentTheme === 'dark');
    updateThemeIcon(currentTheme);
    
    themeToggle.addEventListener('click', function() {
        const isDark = document.documentElement.classList.toggle('dark');
        const theme = isDark ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        updateThemeIcon(theme);
    });
    
    function updateThemeIcon(theme) {
        themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }
});

function updateQuantity(itemId, newQuantity) {
    if (newQuantity < 0) return;
    
    fetch('{{ route("cart.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            menu_item_id: itemId,
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function removeItem(itemId) {
    if (!confirm('Are you sure you want to remove this item?')) return;
    
    fetch('{{ route("cart.remove") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            menu_item_id: itemId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function clearCart() {
    if (!confirm('Are you sure you want to clear your cart?')) return;
    
    fetch('{{ route("cart.clear") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function proceedToCheckout() {
    // Implement checkout logic
    alert('Checkout feature coming soon!');
}
</script>
@endsection