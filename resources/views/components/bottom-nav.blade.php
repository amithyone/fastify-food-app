<!-- Bottom Navigation Bar -->
<nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-2xl z-50 flex justify-around items-center py-1 px-2 max-w-md mx-auto w-full">
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center {{ request()->routeIs('dashboard') ? 'text-orange-500 dark:text-orange-300' : 'text-gray-400 dark:text-gray-400' }}">
        <!-- Home Icon (modern) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0h6" />
        </svg>
        <span class="text-xs mt-0.5">Home</span>
    </a>
    
    @php
        // Determine the current restaurant context
        $currentRestaurant = null;
        
        // Check if we're on a restaurant-specific page
        if (isset($restaurant)) {
            $currentRestaurant = $restaurant;
        } elseif (request()->routeIs('menu.index') && request()->segment(2)) {
            // We're on a restaurant-specific menu page
            $currentRestaurant = \App\Models\Restaurant::where('slug', request()->segment(2))->first();
        } elseif (session('qr_restaurant_id')) {
            // We're in a QR code context
            $currentRestaurant = \App\Models\Restaurant::find(session('qr_restaurant_id'));
        } elseif (session('current_restaurant_id')) {
            // We have a current restaurant in session
            $currentRestaurant = \App\Models\Restaurant::find(session('current_restaurant_id'));
        } elseif (session('current_restaurant_slug')) {
            // We have a current restaurant slug in session
            $currentRestaurant = \App\Models\Restaurant::where('slug', session('current_restaurant_slug'))->first();
        } elseif (request()->has('restaurant')) {
            // Restaurant passed as query parameter
            $currentRestaurant = \App\Models\Restaurant::where('slug', request()->get('restaurant'))->first();
        }
        
        // Determine menu URL
        if ($currentRestaurant) {
            $menuUrl = route('menu.restaurant', $currentRestaurant->slug);
            $isRestaurantMenu = true;
        } else {
            $menuUrl = route('menu.index');
            $isRestaurantMenu = false;
        }
    @endphp
    
    <a href="{{ $menuUrl }}" class="flex flex-col items-center {{ request()->routeIs('menu.*') ? 'text-orange-500 dark:text-orange-300' : 'text-gray-400 dark:text-gray-400' }}">
        <!-- Menu Icon (modern) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <span class="text-xs mt-0.5">Menu</span>
    </a>
    
    <a href="{{ route('cart.index') }}" onclick="openCart()" class="flex flex-col items-center {{ request()->routeIs('cart.*') ? 'text-orange-500 dark:text-orange-300' : 'text-gray-400 dark:text-gray-400' }} relative">
        <!-- Cart Icon (modern) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M1 1h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 8m12-8l2 8" />
        </svg>
        <span id="cartCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden" style="background-color: #ef4444 !important; color: white !important;">0</span>
        <span class="text-xs mt-0.5">Cart</span>
    </a>
    
    <a href="{{ route('user.orders') }}" class="flex flex-col items-center {{ request()->routeIs('user.orders*') ? 'text-orange-500 dark:text-orange-300' : 'text-gray-400 dark:text-gray-400' }}">
        <!-- Orders Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span class="text-xs mt-0.5">Orders</span>
    </a>
    
    <a href="{{ route('wallet.index') }}" class="flex flex-col items-center {{ request()->routeIs('wallet.*') ? 'text-orange-500 dark:text-orange-300' : 'text-gray-400 dark:text-gray-400' }}">
        <!-- Wallet Icon -->
        <i class="fas fa-wallet text-xl"></i>
        <span class="text-xs mt-0.5">Wallet</span>
    </a>
    
    <a href="https://wa.me/" target="_blank" class="flex flex-col items-center text-green-500 dark:text-green-400">
        <!-- WhatsApp Icon (modern) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.72 13.06a6.5 6.5 0 10-2.72 2.72l3.85 1.1a1 1 0 001.26-1.26l-1.1-3.85z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 11a3.5 3.5 0 005 0" />
        </svg>
        <span class="text-xs mt-0.5">WhatsApp</span>
    </a>
    
    <a href="{{ Auth::check() ? route('profile.edit') : route('login') }}" class="flex flex-col items-center {{ request()->routeIs('profile.*') || request()->routeIs('login') ? 'text-orange-500 dark:text-orange-300' : 'text-gray-400 dark:text-gray-400' }}">
        <!-- Login/User Icon (modern) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="8" r="4" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20v-1a4 4 0 014-4h8a4 4 0 014 4v1" />
        </svg>
        <span class="text-xs mt-0.5">{{ Auth::check() ? 'Profile' : 'Login' }}</span>
    </a>
</nav>

<script>
// Cart functionality
function openCart() {
    window.location.href = '{{ route("cart.index") }}';
}

// Update cart count
function updateCartCount() {
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            const cartCount = document.getElementById('cartCount');
            if (data.count > 0) {
                cartCount.textContent = data.count;
                cartCount.classList.remove('hidden');
            } else {
                cartCount.classList.add('hidden');
            }
        })
        .catch(error => console.error('Error fetching cart count:', error));
}

// Update cart count on page load
updateCartCount();
</script> 