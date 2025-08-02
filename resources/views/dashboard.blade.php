@extends('layouts.app')

@section('title', 'Dashboard - Fastify')

@section('content')
<!-- Fixed/Sticky Top Bar: always at the very top -->
<div class="fixed top-0 left-0 right-0 z-50 bg-[#f1ecdc] dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 py-2 shadow-lg max-w-md mx-auto w-full mt-15">
    <div class="flex items-center gap-2 px-4">
        <!-- Menu Toggle Button -->
        <button id="menuToggle" class="w-10 h-10 flex items-center justify-center rounded-full bg-orange-100 dark:bg-gray-700 text-orange-500 dark:text-orange-300 transition hover:bg-orange-200 dark:hover:bg-gray-600">
            <i class="fas fa-bars"></i>
        </button>
        <!-- Search Bar -->
        <div class="flex-1 relative">
            <input type="text" id="searchInput" placeholder="Search restaurants..." class="w-full px-4 py-2 pl-10 border border-gray-200 dark:border-gray-700 rounded-full focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-300 bg-gray-50 dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-100">
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
        <!-- Header -->
        <div class="mb-8" style="margin-top: 60px;">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Welcome to Fastify</h1>
            <p class="text-gray-600 dark:text-gray-400">Discover amazing restaurants and order delicious food</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <!-- Restaurants Visited -->
            <div class="rounded-lg shadow p-4 border border-red-300 dark:border-red-700" style="background: linear-gradient(to bottom right, #f87171, #dc2626) !important;">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-utensils text-xl text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-white">Restaurants Visited</h3>
                        <p class="text-xl font-bold text-white">{{ Auth::user()->orders()->select('restaurant_id')->distinct()->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="rounded-lg shadow p-4 border border-emerald-300 dark:border-emerald-700" style="background: linear-gradient(to bottom right, #34d399, #059669) !important;">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shopping-bag text-xl text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-white">Total Orders</h3>
                        <p class="text-xl font-bold text-white">{{ Auth::user()->orders()->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Favorite Restaurants -->
            <div class="rounded-lg shadow p-4 border border-amber-300 dark:border-amber-700" style="background: linear-gradient(to bottom right, #fbbf24, #d97706) !important;">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-star text-xl text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-white">Favorite Restaurants</h3>
                        <p class="text-xl font-bold text-white">{{ Auth::user()->orders()->select('restaurant_id')->distinct()->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Reward Points -->
            <div class="rounded-lg shadow p-4 border border-indigo-300 dark:border-indigo-700" style="background: linear-gradient(to bottom right, #818cf8, #4338ca) !important;">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-gift text-xl text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-white">Reward Points</h3>
                        <p class="text-xl font-bold text-white">{{ Auth::user()->rewards()->sum('points') ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>



        <!-- Recent Restaurants Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8 border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Restaurants</h2>
                <p class="text-gray-600 dark:text-gray-400">Restaurants you've visited recently</p>
            </div>
            
            <div class="p-6">
                @php
                    $recentRestaurants = Auth::user()->orders()
                        ->with('restaurant')
                        ->select('restaurant_id', \DB::raw('MAX(created_at) as last_visited'))
                        ->groupBy('restaurant_id')
                        ->orderByDesc('last_visited')
                        ->limit(6)
                        ->get();
                @endphp

                @if($recentRestaurants->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($recentRestaurants as $restaurant)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center mb-3">
                                    @if($restaurant->logo)
                                        <img src="{{ Storage::url($restaurant->logo) }}" alt="{{ $restaurant->name }}" 
                                             class="w-12 h-12 rounded-lg object-cover mr-3">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-utensils text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $restaurant->name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $restaurant->cuisine_type ?? 'Restaurant' }}</p>
                                        @if($restaurant->address)
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($restaurant->address, 30) }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-sm"></i>
                                            @endfor
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">(4.5)</span>
                                    </div>
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Open</span>
                                </div>
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('menu.index', $restaurant->slug) }}" 
                                       class="flex-1 bg-orange-500 text-white text-center py-2 px-3 rounded-lg hover:bg-orange-600 transition-colors text-sm">
                                        <i class="fas fa-eye mr-1"></i>View Menu
                                    </a>
                                    <button onclick="addToFavorites({{ $restaurant->id }})" 
                                            class="px-3 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-utensils text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400">No restaurants available</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500">Check back later for new restaurants</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- All Restaurants Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8 border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">All Restaurants</h2>
                        <p class="text-gray-600 dark:text-gray-400">Discover amazing restaurants on Fastify</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                @php
                    $allRestaurants = \App\Models\Restaurant::where('is_active', true)->orderBy('name')->get();
                @endphp

                @if($allRestaurants->count() > 0)
                    <div class="grid grid-cols-1 gap-4" id="restaurantsGrid">
                        @foreach($allRestaurants as $restaurant)
                            <div class="restaurant-card bg-gray-50 dark:bg-gray-700 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors border border-gray-200 dark:border-gray-600" 
                                 data-name="{{ strtolower($restaurant->name) }}" 
                                 data-cuisine="{{ strtolower($restaurant->cuisine_type ?? '') }}">
                                <div class="flex items-center mb-3">
                                    @if($restaurant->logo)
                                        <img src="{{ Storage::url($restaurant->logo) }}" alt="{{ $restaurant->name }}" 
                                             class="w-12 h-12 rounded-lg object-cover mr-3">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-utensils text-white"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $restaurant->name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $restaurant->cuisine_type ?? 'Restaurant' }}</p>
                                        @if($restaurant->address)
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($restaurant->address, 30) }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 px-2 py-1 rounded-full">Active</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-sm"></i>
                                            @endfor
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">(4.5)</span>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $restaurant->menuItems()->count() }} items
                                    </div>
                                </div>
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('menu.restaurant', $restaurant->slug) }}" 
                                       class="flex-1 bg-orange-500 text-white text-center py-2 px-3 rounded-lg hover:bg-orange-600 transition-colors text-sm">
                                        <i class="fas fa-eye mr-1"></i>View Menu
                                    </a>
                                    <button onclick="addToFavorites({{ $restaurant->id }})" 
                                            class="px-3 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-utensils text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400">No restaurants available</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500">Check back later for new restaurants</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Restaurant Signup CTA -->
        <div class="bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600 dark:from-orange-500 dark:via-orange-600 dark:to-orange-700 rounded-xl shadow-2xl border-2 border-orange-300 dark:border-orange-600 overflow-hidden relative" style="padding: 3rem !important; background: linear-gradient(135deg, #fb923c, #f97316, #ea580c) !important;">
            <div class="text-center relative z-10">
                <div class="mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-6">
                        <i class="fas fa-store text-4xl text-white"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-white mb-4 drop-shadow-lg" style="color: white !important;">Are You a Restaurant Owner?</h2>
                    <p class="text-white mb-8 text-lg leading-relaxed" style="color: white !important;">Join Fastify and start serving customers with your digital menu</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    <a href="{{ route('restaurant.onboarding') }}" 
                       class="inline-flex items-center justify-center px-12 py-4 bg-white text-orange-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-200 transform hover:scale-105 shadow-lg text-lg border-2 border-white" style="background-color: white !important; color: #ea580c !important;">
                        <i class="fas fa-plus mr-3"></i>
                        Sign Up Your Restaurant
                    </a>
                    <a href="#" 
                       class="inline-flex items-center justify-center px-12 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 text-lg border-2 border-blue-600" style="background-color: #2563eb !important; color: white !important;">
                        <i class="fas fa-info-circle mr-3"></i>
                        Learn More
                    </a>
                </div>
            </div>
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-12 -translate-x-12"></div>
        </div>
    </div>
</div>

<!-- Side Menu -->
<div id="sideMenu" class="fixed top-0 left-0 h-full w-80 max-w-[85vw] bg-white dark:bg-gray-800 shadow-2xl backdrop-blur-sm transition-transform duration-300 ease-in-out z-50" style="transform: translateX(-100%);">
    <!-- Menu Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Menu</h2>
        <button id="closeMenu" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <!-- Menu Items -->
    <div class="p-4 space-y-4">
        <!-- Home -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-3 rounded-lg bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-300 hover:bg-orange-100 dark:hover:bg-orange-900/30 transition">
            <i class="fas fa-home text-lg"></i>
            <span class="font-medium">Home</span>
        </a>
        <!-- Restaurants -->
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Restaurants</h3>
            <a href="{{ route('menu.index') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-utensils text-lg"></i>
                <span>Browse Restaurants</span>
            </a>
            <a href="{{ route('restaurant.onboarding') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-store text-lg"></i>
                <span>Add Restaurant</span>
            </a>
        </div>
        <!-- Quick Actions -->
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Quick Actions</h3>
            <a href="{{ route('cart.index') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-shopping-cart text-lg"></i>
                <span>My Cart</span>
                <span id="menuCartCount" class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 hidden" style="background-color: #ef4444 !important; color: white !important;">0</span>
            </a>
            <a href="{{ route('user.orders') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-receipt text-lg"></i>
                <span>My Orders</span>
            </a>
            <a href="{{ route('wallet.index') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-wallet text-lg"></i>
                <span>My Wallet</span>
            </a>
            @auth
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-user text-lg"></i>
                    <span>Profile</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-sign-in-alt text-lg"></i>
                    <span>Login</span>
                </a>
            @endauth
        </div>
        <!-- Contact -->
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Contact</h3>
            <a href="https://wa.me/" target="_blank" class="flex items-center gap-3 p-3 rounded-lg text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition">
                <i class="fab fa-whatsapp text-lg"></i>
                <span>WhatsApp Support</span>
            </a>
            <a href="tel:+234" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-phone text-lg"></i>
                <span>Call Us</span>
            </a>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="fixed bottom-6 left-6 z-50">
    <div class="flex space-x-3">
        <!-- Cart Link -->
        <a href="{{ route('cart.index') }}" class="w-14 h-14 bg-purple-500 hover:bg-purple-600 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 transform hover:scale-110">
            <i class="fas fa-shopping-cart text-xl"></i>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
        </a>
        
        <!-- Orders Link -->
        <a href="{{ route('orders.index') }}" class="w-14 h-14 bg-blue-500 hover:bg-blue-600 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 transform hover:scale-110">
            <i class="fas fa-list-alt text-xl"></i>
        </a>
    </div>
</div>

<script>
// Menu functionality
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const sideMenu = document.getElementById('sideMenu');
    const closeMenu = document.getElementById('closeMenu');
    const searchInput = document.getElementById('searchInput');
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    
    // Menu toggle functionality
    if (menuToggle && sideMenu) {
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle menu visibility
            const isOpen = sideMenu.style.transform === 'translateX(0px)';
            sideMenu.style.transform = isOpen ? 'translateX(-100%)' : 'translateX(0px)';
            
            // Add/remove backdrop
            if (!isOpen) {
                const backdrop = document.createElement('div');
                backdrop.id = 'menuBackdrop';
                backdrop.className = 'fixed inset-0 bg-black bg-opacity-50 z-40';
                backdrop.addEventListener('click', closeSideMenu);
                document.body.appendChild(backdrop);
            } else {
                const backdrop = document.getElementById('menuBackdrop');
                if (backdrop) backdrop.remove();
            }
        });
    }
    
    // Close menu functionality
    function closeSideMenu() {
        if (sideMenu) {
            sideMenu.style.transform = 'translateX(-100%)';
        }
        const backdrop = document.getElementById('menuBackdrop');
        if (backdrop) backdrop.remove();
    }
    
    if (closeMenu) {
        closeMenu.addEventListener('click', closeSideMenu);
    }
    
    // Close menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSideMenu();
        }
    });
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const restaurantCards = document.querySelectorAll('.restaurant-card');
            
            restaurantCards.forEach(card => {
                const restaurantName = card.querySelector('.restaurant-name').textContent.toLowerCase();
                const cuisineType = card.querySelector('.cuisine-type')?.textContent.toLowerCase() || '';
                
                if (restaurantName.includes(searchTerm) || cuisineType.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
    
    // Theme toggle functionality
    if (themeToggle && themeIcon) {
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
    }
});

// Add to favorites functionality
function addToFavorites(restaurantId) {
    console.log('Adding restaurant to favorites:', restaurantId);
    // Implement add to favorites logic
    alert('Restaurant added to favorites!');
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Any additional initialization
});
</script>
@endsection
