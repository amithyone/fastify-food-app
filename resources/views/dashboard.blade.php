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

        <!-- Email Verification Notice -->
        @auth
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
        @endauth

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            @auth
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

            <!-- Recently Visited -->
            <div class="rounded-lg shadow p-4 border border-purple-300 dark:border-purple-700 cursor-pointer hover:scale-105 transition-transform" style="background: linear-gradient(to bottom right, #a78bfa, #7c3aed) !important;" onclick="window.location.href='{{ route('restaurants.recent') }}'">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-xl text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-white">Recently Visited</h3>
                        <p class="text-xl font-bold text-white">{{ Auth::user()->orders()->select('restaurant_id')->distinct()->count() }}</p>
                    </div>
                </div>
            </div>
            @else
            <!-- Login to See Stats -->
            <div class="rounded-lg shadow p-4 border border-red-300 dark:border-red-700" style="background: linear-gradient(to bottom right, #f87171, #dc2626) !important;">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-utensils text-xl text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-white">Restaurants Visited</h3>
                        <p class="text-xs text-white opacity-80">Login to see your stats</p>
                    </div>
                </div>
            </div>

            <!-- Login to See Orders -->
            <div class="rounded-lg shadow p-4 border border-emerald-300 dark:border-emerald-700" style="background: linear-gradient(to bottom right, #34d399, #059669) !important;">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shopping-bag text-xl text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-white">Total Orders</h3>
                        <p class="text-xs text-white opacity-80">Login to see your orders</p>
                    </div>
                </div>
            </div>

            <!-- Login to See Recent -->
            <div class="rounded-lg shadow p-4 border border-purple-300 dark:border-purple-700 cursor-pointer hover:scale-105 transition-transform" style="background: linear-gradient(to bottom right, #a78bfa, #7c3aed) !important;" onclick="window.location.href='{{ route('login') }}'">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-xl text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-white">Recently Visited</h3>
                        <p class="text-xs text-white opacity-80">Login to see your history</p>
                    </div>
                </div>
            </div>
            @endauth

            <!-- Featured Restaurants -->
            <div class="rounded-lg shadow p-4 border border-blue-300 dark:border-blue-700 cursor-pointer hover:scale-105 transition-transform" style="background: linear-gradient(to bottom right, #60a5fa, #2563eb) !important;" onclick="window.location.href='{{ route('restaurants.all') }}'">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-star text-xl text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-white">Featured Restaurants</h3>
                        <p class="text-xl font-bold text-white">{{ \App\Models\FeaturedRestaurant::currentlyFeatured()->count() }}</p>
                    </div>
                </div>
            </div>

            @auth
            @if(Auth::user()->is_admin)
            <!-- Manage Restaurants (Admin Only) -->
            <div class="rounded-lg shadow p-4 border border-purple-300 dark:border-purple-700 cursor-pointer hover:scale-105 transition-transform" style="background: linear-gradient(to bottom right, #a855f7, #7c3aed) !important;" onclick="window.location.href='{{ route('admin.restaurants') }}'">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-cog text-xl text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-white">Manage Restaurants</h3>
                        <p class="text-xl font-bold text-white">{{ \App\Models\Restaurant::count() }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if(Auth::user()->primaryRestaurant)
            <!-- Manage My Restaurant (Restaurant Owner) -->
            <div class="rounded-lg shadow p-4 border border-green-300 dark:border-green-700 cursor-pointer hover:scale-105 transition-transform" style="background: linear-gradient(to bottom right, #10b981, #059669) !important;" onclick="window.location.href='{{ route('restaurant.dashboard', Auth::user()->primaryRestaurant->slug) }}'">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-store text-xl text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-white">Manage My Restaurant</h3>
                        <p class="text-xs text-white opacity-80">{{ Auth::user()->primaryRestaurant->name }}</p>
                    </div>
                </div>
            </div>
            @endif
            @endauth

            @auth
            <!-- Favorite Restaurants -->
            <div class="rounded-lg shadow p-4 border border-amber-300 dark:border-amber-700" style="background: linear-gradient(to bottom right, #fbbf24, #d97706) !important;">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-star text-xl text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-white">Favorite Restaurants</h3>
                        <p class="text-xl font-bold text-white">0</p>
                    </div>
                </div>
            </div>
            @else
            <!-- Login to See Favorites -->
            <div class="rounded-lg shadow p-4 border border-amber-300 dark:border-amber-700 cursor-pointer hover:scale-105 transition-transform" style="background: linear-gradient(to bottom right, #fbbf24, #d97706) !important;" onclick="window.location.href='{{ route('login') }}'">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-star text-xl text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-white">Favorite Restaurants</h3>
                        <p class="text-xs text-white opacity-80">Login to see your favorites</p>
                    </div>
                </div>
            </div>
            @endauth
        </div>

        <!-- Featured Restaurants Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8 border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Featured Restaurants</h2>
                        <p class="text-gray-600 dark:text-gray-400">Discover amazing restaurants on Fastify</p>
                    </div>
                    <a href="{{ route('restaurants.all') }}" class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 text-sm font-medium">
                        View All →
                    </a>
                </div>
            </div>
            
            <div class="p-6">
                @php
                    $featuredRestaurants = \App\Models\FeaturedRestaurant::currentlyFeatured()
                        ->with('restaurant.ratings')
                        ->ordered()
                        ->get();
                @endphp

                @if($featuredRestaurants->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="featuredRestaurantsGrid">
                        @foreach($featuredRestaurants as $featured)
                            @include('components.restaurant-card', ['restaurant' => $featured->restaurant, 'featured' => $featured])
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-star text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400">No featured restaurants available</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500">Check back later for featured restaurants</p>
                        <a href="{{ route('restaurants.all') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-utensils mr-2"></i>
                            View All Restaurants
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Restaurants Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8 border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Restaurants</h2>
                <p class="text-gray-600 dark:text-gray-400">
                    @auth
                        Restaurants you've visited recently
                    @else
                        <span class="text-orange-600 dark:text-orange-400">Login to see your recent restaurants</span>
                    @endauth
                </p>
            </div>
            
            <div class="p-6">
                @auth
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
                            @foreach($recentRestaurants as $order)
                                @php
                                    $restaurant = $order->restaurant;
                                @endphp
                                @if($restaurant)
                                    @include('components.restaurant-card', ['restaurant' => $restaurant])
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-utensils text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                            <p class="text-gray-600 dark:text-gray-400">No recent restaurants yet</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500">Start ordering to see your history here</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-lock text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Login to see your recent restaurants</p>
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login Now
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Rewards Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8 border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Rewards & Points</h2>
                <p class="text-gray-600 dark:text-gray-400">
                    @auth
                        Earn points with every order
                    @else
                        <span class="text-orange-600 dark:text-orange-400">Login to see your reward points</span>
                    @endauth
                </p>
            </div>
            
            <div class="p-6">
                @auth
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Current Points -->
                        <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg p-4 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold">Current Points</h3>
                                    <p class="text-2xl font-bold">{{ Auth::user()->rewards()->sum('points') ?? 0 }}</p>
                                </div>
                                <i class="fas fa-gift text-2xl opacity-80"></i>
                            </div>
                        </div>

                        <!-- Points to Next Reward -->
                        <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg p-4 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold">Next Reward</h3>
                                    <p class="text-2xl font-bold">100 pts</p>
                                </div>
                                <i class="fas fa-star text-2xl opacity-80"></i>
                            </div>
                        </div>

                        <!-- Total Earned -->
                        <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-4 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold">Total Earned</h3>
                                    <p class="text-2xl font-bold">{{ Auth::user()->rewards()->sum('points') ?? 0 }}</p>
                                </div>
                                <i class="fas fa-trophy text-2xl opacity-80"></i>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('wallet.rewards') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                            <i class="fas fa-gift mr-2"></i>
                            View All Rewards
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-gift text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Login to see your reward points and earn points with every order</p>
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login to See Rewards
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Restaurant Management Section -->
        @auth
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8 border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Restaurant Management</h2>
                    <p class="text-gray-600 dark:text-gray-400">Manage your restaurants and business</p>
                </div>
                
                <div class="p-6">
                    @php
                        $userRestaurants = Auth::user()->restaurants ?? collect();
                    @endphp

                    @if($userRestaurants->count() > 0)
                        <!-- User has restaurants - show management options -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Manage Restaurants Card -->
                            <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-6 text-white cursor-pointer hover:scale-105 transition-transform" onclick="window.location.href='{{ route('restaurant.onboarding') }}'">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold mb-2">Manage Your Restaurants</h3>
                                        <p class="text-sm opacity-90">{{ $userRestaurants->count() }} restaurant(s)</p>
                                        <p class="text-xs opacity-75 mt-2">View and manage your restaurant businesses</p>
                                    </div>
                                    <i class="fas fa-store text-3xl opacity-80"></i>
                                </div>
                            </div>

                            <!-- Add New Restaurant Card -->
                            <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg p-6 text-white cursor-pointer hover:scale-105 transition-transform" onclick="window.location.href='{{ route('restaurant.onboarding') }}'">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold mb-2">Add New Restaurant</h3>
                                        <p class="text-sm opacity-90">Expand your business</p>
                                        <p class="text-xs opacity-75 mt-2">Register another restaurant location</p>
                                    </div>
                                    <i class="fas fa-plus-circle text-3xl opacity-80"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Restaurant List -->
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Your Restaurants</h3>
                            <div class="space-y-3">
                                @foreach($userRestaurants as $restaurant)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center">
                                            @if($restaurant->logo)
                                                <img src="{{ $restaurant->logo_url ?? \App\Helpers\PWAHelper::getPlaceholderImage('square') }}" alt="{{ $restaurant->name }}" class="w-12 h-12 rounded-lg object-cover mr-3">
                                            @else
                                                <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center mr-3">
                                                    <i class="fas fa-utensils text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $restaurant->name }}</h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $restaurant->city }}, {{ $restaurant->state }}</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('restaurant.dashboard', $restaurant->slug) }}" class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition-colors">
                                                <i class="fas fa-chart-line mr-1"></i>Dashboard
                                            </a>
                                            <a href="{{ route('restaurant.edit', $restaurant->slug) }}" class="px-3 py-1 bg-gray-500 text-white text-sm rounded hover:bg-gray-600 transition-colors">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <!-- User has no restaurants - show add restaurant card -->
                        <div class="text-center py-8">
                            <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg p-8 text-white cursor-pointer hover:scale-105 transition-transform mb-6" onclick="window.location.href='{{ route('restaurant.onboarding') }}'">
                                <i class="fas fa-store text-5xl mb-4 opacity-80"></i>
                                <h3 class="text-2xl font-semibold mb-2">Add Your Restaurant</h3>
                                <p class="text-lg opacity-90 mb-4">Start your restaurant business with Fastify</p>
                                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                                    <h4 class="font-semibold mb-2">What you'll get:</h4>
                                    <ul class="text-sm space-y-1 text-left">
                                        <li><i class="fas fa-check mr-2"></i>Digital menu management</li>
                                        <li><i class="fas fa-check mr-2"></i>Order management system</li>
                                        <li><i class="fas fa-check mr-2"></i>QR code ordering</li>
                                        <li><i class="fas fa-check mr-2"></i>Payment processing</li>
                                        <li><i class="fas fa-check mr-2"></i>Customer analytics</li>
                                    </ul>
                                </div>
                            </div>
                            <a href="{{ route('restaurant.onboarding') }}" class="inline-flex items-center px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors font-semibold">
                                <i class="fas fa-plus mr-2"></i>
                                Get Started Now
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endauth

        <!-- Restaurant Section -->
        @auth
            @if(Auth::user()->primaryRestaurant)
                <!-- User has a restaurant - show dashboard link and add another option -->
                <div class="bg-gradient-to-br from-green-400 via-green-500 to-green-600 dark:from-green-500 dark:via-green-600 dark:to-green-700 rounded-xl shadow-2xl border-2 border-green-300 dark:border-green-600 overflow-hidden relative" style="padding: 3rem !important; background: linear-gradient(135deg, #4ade80, #22c55e, #16a34a) !important;">
                    <div class="text-center relative z-10">
                        <div class="mb-8">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-6">
                                <i class="fas fa-store text-4xl text-white"></i>
                            </div>
                            <h2 class="text-3xl font-bold text-white mb-4 drop-shadow-lg" style="color: white !important;">Manage Your Restaurant</h2>
                            <p class="text-white mb-8 text-lg leading-relaxed" style="color: white !important;">Access your restaurant dashboard and manage orders, menu, and settings</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                            <a href="{{ route('restaurant.dashboard', Auth::user()->primaryRestaurant->slug) }}" 
                               class="inline-flex items-center justify-center px-12 py-4 bg-white text-green-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-200 transform hover:scale-105 shadow-lg text-lg border-2 border-white" style="background-color: white !important; color: #16a34a !important;">
                                <i class="fas fa-tachometer-alt mr-3"></i>
                                Go to Dashboard
                            </a>
                            <a href="{{ route('restaurant.edit', Auth::user()->primaryRestaurant->slug) }}" 
                               class="inline-flex items-center justify-center px-12 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 text-lg border-2 border-blue-600" style="background-color: #2563eb !important; color: white !important;">
                                <i class="fas fa-edit mr-3"></i>
                                Edit Restaurant
                            </a>
                            <a href="{{ route('restaurant.onboarding') }}" 
                               class="inline-flex items-center justify-center px-12 py-4 bg-orange-500 text-white font-semibold rounded-lg hover:bg-orange-600 transition-all duration-200 text-lg border-2 border-orange-500" style="background-color: #f97316 !important; color: white !important;">
                                <i class="fas fa-plus mr-3"></i>
                                Add Another Restaurant
                            </a>
                        </div>
                    </div>
                    <!-- Decorative elements -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-12 -translate-x-12"></div>
                </div>
            @else
                <!-- User is authenticated but doesn't have a restaurant -->
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
            @endif
        @else
            <!-- User is not authenticated -->
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
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center justify-center px-12 py-4 bg-white text-orange-600 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-200 transform hover:scale-105 shadow-lg text-lg border-2 border-white" style="background-color: white !important; color: #ea580c !important;">
                            <i class="fas fa-sign-in-alt mr-3"></i>
                            Login to Add Restaurant
                        </a>
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center justify-center px-12 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 text-lg border-2 border-blue-600" style="background-color: #2563eb !important; color: white !important;">
                            <i class="fas fa-user-plus mr-3"></i>
                            Create Account
                        </a>
                    </div>
                </div>
                <!-- Decorative elements -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-12 -translate-x-12"></div>
            </div>
        @endauth
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
        <!-- Dashboard/Home - Always visible and prominent -->
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Navigation</h3>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-3 rounded-lg bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-300 hover:bg-orange-100 dark:hover:bg-orange-900/30 transition">
                <i class="fas fa-home text-lg"></i>
                <span class="font-medium">Dashboard</span>
            </a>
        </div>
        
        <!-- Restaurants -->
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Restaurants</h3>
            <a href="{{ route('restaurants.all') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-store text-lg"></i>
                <span>All Restaurants</span>
            </a>
            <a href="{{ route('restaurants.recent') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-clock text-lg"></i>
                <span>Recent Restaurants</span>
            </a>
            @auth
                @if(Auth::user()->primaryRestaurant)
                    <a href="{{ route('restaurant.dashboard', Auth::user()->primaryRestaurant->slug) }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <i class="fas fa-tachometer-alt text-lg"></i>
                        <span>My Restaurant</span>
                    </a>
                @endif
                <a href="{{ route('restaurant.onboarding') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-plus-circle text-lg"></i>
                    <span>{{ Auth::user()->primaryRestaurant ? 'Add Another Restaurant' : 'Add Restaurant' }}</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-sign-in-alt text-lg"></i>
                    <span>Login to Add Restaurant</span>
                </a>
            @endauth
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
            <a href="{{ route('wallet.rewards') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-gift text-lg"></i>
                <span>Rewards</span>
            </a>
        </div>
        
        <!-- Account -->
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Account</h3>
            @auth
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-user text-lg"></i>
                    <span>Profile</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        <i class="fas fa-sign-out-alt text-lg"></i>
                        <span>Logout</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-sign-in-alt text-lg"></i>
                    <span>Login</span>
                </a>
                <a href="{{ route('register') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-user-plus text-lg"></i>
                    <span>Register</span>
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

<!-- Quick Actions - Only for restaurant owners -->
@auth
@if(Auth::user()->isRestaurantOwner())
<div class="fixed bottom-6 right-6 z-50">
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
@endif
@endauth
@endsection
