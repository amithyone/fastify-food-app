@extends('layouts.app')

@section('title', $restaurant->name . ' - Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-8">
                <div class="flex items-center space-x-4">
                    @php
                        $logoUrl = $restaurant->logo_url ?? \App\Helpers\PWAHelper::getPlaceholderImage('square');
                        $hasLogo = !empty($restaurant->logo);
                        \Log::info('Dashboard logo URL', [
                            'restaurant_id' => $restaurant->id,
                            'logo_url' => $logoUrl,
                            'has_logo' => $hasLogo,
                            'logo_path' => $restaurant->logo
                        ]);
                    @endphp
                    <script>
                        console.log('Logo URL:', '{{ $logoUrl }}');
                        console.log('Has Logo:', {{ $hasLogo ? 'true' : 'false' }});
                        console.log('Logo Path:', '{{ $restaurant->logo }}');
                    </script>
                    
                    @if($hasLogo)
                        <div class="relative">
                            <img src="{{ $logoUrl }}" 
                                 alt="{{ $restaurant->name }}" 
                                 class="w-16 h-16 rounded-lg object-contain bg-gray-100 dark:bg-gray-700"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'; console.error('Logo failed to load:', this.src);"
                                 onload="console.log('Logo loaded successfully:', this.src);">
                            <!-- Fallback placeholder -->
                            <div class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center" style="display: none;">
                                <i class="fas fa-store text-2xl text-gray-400"></i>
                            </div>
                        </div>
                    @else
                        <div class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <i class="fas fa-store text-2xl text-gray-400"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $restaurant->name }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Restaurant Dashboard</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Online Status Indicator -->
                    <div class="flex items-center space-x-2">
                        <span id="onlineStatus" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            🌐 Online
                        </span>
                    </div>
                    
                    <!-- Desktop: Show all buttons -->
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="{{ $restaurant->getMenuUrl() }}" target="_blank" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            View Menu
                        </a>
                        
                        <a href="{{ route('restaurant.edit', $restaurant->slug) }}" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-cog mr-2"></i>
                            Settings
                        </a>
                    </div>
                    
                    <!-- Mobile & Desktop: Add Dropdown Button -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-plus mr-2"></i>
                            Add
                            <i class="fas fa-chevron-down ml-2 text-xs"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg z-50 border border-gray-200 dark:border-gray-700">
                            
                            <div class="py-1">
                                <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                                   class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-utensils mr-3 text-green-600"></i>
                                    Add Dish
                                </a>
                                
                                <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                                   class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-folder mr-3 text-blue-600"></i>
                                    Add Category
                                </a>
                                
                                <a href="#" 
                                   class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-toggle-on mr-3 text-purple-600"></i>
                                    Add Status
                                </a>
                                
                                <!-- Divider -->
                                <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                                
                                <!-- Mobile-only: View Menu and Settings -->
                                <div class="md:hidden">
                                    <a href="{{ $restaurant->getMenuUrl() }}" target="_blank" 
                                       class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-external-link-alt mr-3 text-blue-600"></i>
                                        View Menu
                                    </a>
                                    
                                    <a href="{{ route('restaurant.edit', $restaurant->slug) }}" 
                                       class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-cog mr-3 text-gray-600"></i>
                                        Settings
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-md flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-blue-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Orders</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_orders'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-md flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Orders</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['pending_orders'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-md flex items-center justify-center">
                            <i class="fas fa-utensils text-green-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Menu Items</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_menu_items'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-md flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-orange-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Today's Earnings</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $restaurant->currency }}{{ number_format($stats['today_earnings'] / 100, 2) }}</p>
                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <div class="flex justify-between">
                                <span>Pay on Delivery:</span>
                                <span>{{ $restaurant->currency }}{{ number_format($stats['pay_on_delivery_earnings'] / 100, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Online Payments:</span>
                                <span>{{ $restaurant->currency }}{{ number_format($stats['non_pay_on_delivery_earnings'] / 100, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <a href="{{ route('restaurant.qr-codes', $restaurant->slug) }}" 
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-qrcode text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">QR Codes</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Generate QR codes for tables</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-utensils text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Manage Menu</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Add and edit menu items</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('restaurant.orders', $restaurant->slug) }}" 
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-orange-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Orders</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">View and manage orders</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('restaurant.track-form', $restaurant->slug) }}" 
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-search text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Track Order</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Find order by tracking code</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('restaurant.stories', $restaurant->slug) }}" 
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-images text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Manage Stories</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Create and edit stories</p>
                    </div>
                </div>
            </a>

            <button onclick="openAIMenuModal()" 
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow text-left w-full">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-robot text-cyan-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">AI Menu Add</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Upload food image for AI recognition</p>
                    </div>
                </div>
            </button>

            <a href="{{ route('restaurant.wallet', $restaurant->slug) }}" 
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-wallet text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Wallet</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Manage payments & withdrawals</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('restaurant.promotions', $restaurant->slug) }}" 
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-star text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Promotions</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Boost your restaurant visibility</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('restaurant.delivery-settings.index', $restaurant->slug) }}" 
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-truck text-indigo-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Delivery Settings</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Configure delivery methods</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('restaurant.status.index', $restaurant->slug) }}" 
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-door-open text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Restaurant Status</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Manage open/close status</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Restaurant Promotion Ads Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8 border border-orange-200 dark:border-orange-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-orange-50 to-yellow-50 dark:from-orange-900/20 dark:to-yellow-900/20">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-star text-orange-500 mr-2"></i>
                            Promote Your Restaurant
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Boost your visibility and attract more customers</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                            Featured
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                @php
                    $currentFeatured = \App\Models\FeaturedRestaurant::where('restaurant_id', $restaurant->id)
                        ->currentlyFeatured()
                        ->first();
                @endphp

                @if($currentFeatured)
                    <!-- Current Active Promotion -->
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 mb-4 border border-green-200 dark:border-green-700">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <span class="font-medium text-green-800 dark:text-green-200">Active Promotion</span>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ $currentFeatured->badge_text }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ $currentFeatured->display_title }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $currentFeatured->display_description }}</p>
                                
                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span><i class="fas fa-eye mr-1"></i> {{ $currentFeatured->impression_count }} impressions</span>
                                    <span><i class="fas fa-mouse-pointer mr-1"></i> {{ $currentFeatured->click_count }} clicks</span>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                    <div>Started: {{ $currentFeatured->featured_from ? $currentFeatured->featured_from->format('M j, Y') : 'Now' }}</div>
                                    <div>Ends: {{ $currentFeatured->featured_until ? $currentFeatured->featured_until->format('M j, Y') : 'Ongoing' }}</div>
                                </div>
                                
                                <button onclick="editPromotion({{ $currentFeatured->id }})" 
                                        class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit Promotion
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- No Active Promotion -->
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-star text-orange-600 text-2xl"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Boost Your Restaurant's Visibility</h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                            Get featured on our homepage and attract more customers. Create custom promotions with badges, special offers, and targeted messaging.
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-eye text-blue-600"></i>
                                </div>
                                <h5 class="font-medium text-gray-900 dark:text-white text-sm">Increased Visibility</h5>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Appear on homepage</p>
                            </div>
                            
                            <div class="text-center">
                                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-chart-line text-green-600"></i>
                                </div>
                                <h5 class="font-medium text-gray-900 dark:text-white text-sm">More Orders</h5>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Attract new customers</p>
                            </div>
                            
                            <div class="text-center">
                                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-bullhorn text-purple-600"></i>
                                </div>
                                <h5 class="font-medium text-gray-900 dark:text-white text-sm">Custom Promotions</h5>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Special offers & badges</p>
                            </div>
                        </div>
                        
                        <button onclick="createPromotion()" 
                                class="inline-flex items-center px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors shadow-lg hover:shadow-xl">
                            <i class="fas fa-plus mr-2"></i>
                            Create Promotion
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Status Updates -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Status Updates</h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Update order statuses quickly</span>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @php
                        $statusCounts = [
                            'pending' => $recent_orders->where('status', 'pending')->count(),
                            'confirmed' => $recent_orders->where('status', 'confirmed')->count(),
                            'preparing' => $recent_orders->where('status', 'preparing')->count(),
                            'ready' => $recent_orders->where('status', 'ready')->count()
                        ];
                    @endphp
                    
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Pending</p>
                                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $statusCounts['pending'] }}</p>
                            </div>
                            <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600 text-sm"></i>
                            </div>
                        </div>
                        @if($statusCounts['pending'] > 0)
                            <button onclick="updateAllStatus('confirmed')" class="mt-3 w-full px-3 py-1 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700 transition-colors">
                                Mark All Confirmed
                            </button>
                        @endif
                    </div>
                    
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Confirmed</p>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $statusCounts['confirmed'] }}</p>
                            </div>
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-blue-600 text-sm"></i>
                            </div>
                        </div>
                        @if($statusCounts['confirmed'] > 0)
                            <button onclick="updateAllStatus('preparing')" class="mt-3 w-full px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors">
                                Mark All Preparing
                            </button>
                        @endif
                    </div>
                    
                    <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-orange-800 dark:text-orange-200">Preparing</p>
                                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $statusCounts['preparing'] }}</p>
                            </div>
                            <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center">
                                <i class="fas fa-utensils text-orange-600 text-sm"></i>
                            </div>
                        </div>
                        @if($statusCounts['preparing'] > 0)
                            <button onclick="updateAllStatus('ready')" class="mt-3 w-full px-3 py-1 bg-orange-600 text-white text-xs rounded hover:bg-orange-700 transition-colors">
                                Mark All Ready
                            </button>
                        @endif
                    </div>
                    
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-800 dark:text-green-200">Ready</p>
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $statusCounts['ready'] }}</p>
                            </div>
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-sm"></i>
                            </div>
                        </div>
                        @if($statusCounts['ready'] > 0)
                            <button onclick="updateAllStatus('delivered')" class="mt-3 w-full px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors">
                                Mark All Delivered
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Orders</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Order ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Table
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Customer
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Items
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recent_orders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    #{{ $order->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    @if($order->table_number)
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                            Table {{ $order->table_number }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 rounded-full">
                                            Takeaway
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    {{ $order->customer_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    {{ $order->orderItems->count() }} items
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    {{ $restaurant->currency }}{{ number_format($order->total / 100, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'preparing') bg-orange-100 text-orange-800
                                        @elseif($order->status === 'ready') bg-green-100 text-green-800
                                        @elseif($order->status === 'delivered') bg-gray-100 text-gray-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    {{ $order->created_at->format('M d, H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('restaurant.orders.show', [$restaurant->slug, $order->id]) }}" 
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            View
                                        </a>
                                        <button onclick="openStatusUpdateModal({{ $order->id }}, '{{ $order->status }}', '{{ $order->status_note ?? '' }}')" 
                                                class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-center text-sm text-gray-500 dark:text-gray-400">
                                    No orders yet. Orders will appear here when customers place them.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Restaurant Info -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Restaurant Information</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Status:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            @if($restaurant->is_active)
                                <span class="text-green-600">Active</span>
                            @else
                                <span class="text-red-600">Inactive</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Verification:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            @if($restaurant->is_verified)
                                <span class="text-green-600">Verified</span>
                            @else
                                <span class="text-yellow-600">Pending</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">WhatsApp:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $restaurant->whatsapp_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Address:</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $restaurant->full_address }}</span>
                    </div>
                    @if($restaurant->custom_domain)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Custom Domain:</span>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $restaurant->custom_domain }}</span>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($restaurant->custom_domain_status === 'verified') bg-green-100 text-green-800
                                    @elseif($restaurant->custom_domain_status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($restaurant->custom_domain_status) }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Links</h3>
                <div class="space-y-3">
                    <a href="{{ $restaurant->getMenuUrl() }}" target="_blank" 
                        class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">View Public Menu</span>
                        <i class="fas fa-external-link-alt text-gray-400"></i>
                    </a>
                    <a href="{{ route('restaurant.qr-codes', $restaurant->slug) }}" 
                        class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Manage QR Codes</span>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                    </a>
                    <a href="{{ route('restaurant.edit', $restaurant->slug) }}" 
                        class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Edit Restaurant</span>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                    </a>
                    <a href="{{ route('restaurant.custom-domain', $restaurant->slug) }}" 
                        class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Custom Domain</span>
                        <div class="flex items-center space-x-2">
                            @if($restaurant->custom_domain)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($restaurant->custom_domain_status === 'verified') bg-green-100 text-green-800
                                    @elseif($restaurant->custom_domain_status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($restaurant->custom_domain_status ?? 'Not Set') }}
                                </span>
                            @endif
                            <i class="fas fa-arrow-right text-gray-400"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusUpdateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-6 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Update Order Status</h3>
            <form id="statusUpdateForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="orderStatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select id="orderStatus" name="status" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="preparing">Preparing</option>
                            <option value="ready">Ready</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="statusNote" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Note (Optional)</label>
                        <textarea id="statusNote" name="status_note" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                  placeholder="Add a note about the status update..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeStatusUpdateModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors font-medium">
                        <i class="fas fa-save mr-2"></i>Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentOrderId = null;

function openStatusUpdateModal(orderId, currentStatus, currentNote = '') {
    currentOrderId = orderId;
    document.getElementById('orderStatus').value = currentStatus;
    document.getElementById('statusNote').value = currentNote;
    document.getElementById('statusUpdateModal').classList.remove('hidden');
}

function closeStatusUpdateModal() {
    document.getElementById('statusUpdateModal').classList.add('hidden');
    currentOrderId = null;
}

function updateAllStatus(newStatus) {
    if (!confirm(`Are you sure you want to mark all orders as ${newStatus}?`)) {
        return;
    }
    
    const orderIds = [];
    @foreach($recent_orders as $order)
        @if($order->status !== 'delivered' && $order->status !== 'cancelled')
            orderIds.push({{ $order->id }});
        @endif
    @endforeach
    
    if (orderIds.length === 0) {
        alert('No orders to update');
        return;
    }
    
    // Update each order status
    const updatePromises = orderIds.map(orderId => {
        const formData = new FormData();
        formData.append('status', newStatus);
        formData.append('status_note', `Bulk updated to ${newStatus}`);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        return fetch(`{{ route('restaurant.orders.status', ['slug' => $restaurant->slug, 'order' => 'ORDER_ID']) }}`.replace('ORDER_ID', orderId), {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });
    });
    
    Promise.all(updatePromises).then(() => {
        window.location.reload();
    }).catch(error => {
        console.error('Bulk update error:', error);
        alert('Error updating order statuses');
    });
}

// Status update form submission
document.addEventListener('DOMContentLoaded', function() {
    const statusUpdateForm = document.getElementById('statusUpdateForm');
    if (statusUpdateForm) {
        statusUpdateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!currentOrderId) {
                alert('No order selected');
                return;
            }
            
            const formData = new FormData(e.target);
            
            fetch(`{{ route('restaurant.orders.status', ['slug' => $restaurant->slug, 'order' => 'ORDER_ID']) }}`.replace('ORDER_ID', currentOrderId), {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            }).then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    return response.text().then(text => {
                        console.error('Error response:', text);
                        throw new Error('Status update failed - Server returned: ' + text.substring(0, 100));
                    });
                }
            }).then(data => {
                console.log('Status update success:', data);
                closeStatusUpdateModal();
                window.location.reload();
            }).catch(error => {
                console.error('Status update error:', error);
                alert('Error updating order status: ' + error.message);
            });
        });
    }
});

// AI Menu Modal
function openAIMenuModal() {
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'ai-menu-modal' }));
}

function closeAIMenuModal() {
    window.dispatchEvent(new CustomEvent('close-modal', { detail: 'ai-menu-modal' }));
    document.getElementById('aiMenuForm').reset();
    document.getElementById('recognitionResult').classList.add('hidden');
    document.getElementById('correctionForm').classList.add('hidden');
    
    // Reset AI variables
    currentImageHash = null;
    currentRecognitionData = null;
}

function recognizeFood() {
    const fileInput = document.getElementById('foodImage');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Please select an image first');
        return;
    }
    
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Show loading state
    document.getElementById('recognizeBtn').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Recognizing...';
    document.getElementById('recognizeBtn').disabled = true;
    
    fetch('{{ route("restaurant.ai.recognize", $restaurant->slug) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Store current recognition data for corrections
            currentRecognitionData = {
                food_name: data.food_name,
                category: data.category,
                description: data.description,
                ingredients: data.ingredients,
                allergens: data.allergens,
                is_vegetarian: data.is_vegetarian,
                is_spicy: data.is_spicy
            };
            
            // Generate image hash (simplified - in real app, this would come from server)
            currentImageHash = btoa(file.name + file.size + file.lastModified);
            
            // Populate form with AI results
            document.getElementById('menuName').value = data.food_name;
            document.getElementById('menuDescription').value = data.description;
            document.getElementById('menuPrice').value = data.suggested_price;
            document.getElementById('menuIngredients').value = data.ingredients;
            document.getElementById('menuAllergens').value = data.allergens;
            document.getElementById('menuIsVegetarian').checked = data.is_vegetarian;
            document.getElementById('menuIsSpicy').checked = data.is_spicy;
            
            // Show confidence level
            document.getElementById('confidenceLevel').textContent = data.confidence + '%';
            document.getElementById('recognitionResult').classList.remove('hidden');
            
            // Auto-select category if available
            if (data.category) {
                const categorySelect = document.getElementById('menuCategory');
                for (let option of categorySelect.options) {
                    if (option.text.toLowerCase() === data.category.toLowerCase()) {
                        option.selected = true;
                        break;
                    }
                }
            }
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Recognition error:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack
        });
        alert('Error recognizing food. Please try again. Check console for details.');
    })
    .finally(() => {
        document.getElementById('recognizeBtn').innerHTML = '<i class="fas fa-robot mr-2"></i>Recognize Food';
        document.getElementById('recognizeBtn').disabled = false;
    });
}

// AI Menu form submission
document.addEventListener('DOMContentLoaded', function() {
    const aiMenuForm = document.getElementById('aiMenuForm');
    if (aiMenuForm) {
        aiMenuForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            fetch('{{ route("restaurant.ai.store", $restaurant->slug) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Menu item added successfully!');
                    closeAIMenuModal();
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Add menu item error:', error);
                alert('Error adding menu item. Please try again.');
            });
        });
    }
});

// AI Correction Functions
let currentImageHash = null;
let currentRecognitionData = null;

function markAsCorrect() {
    // Save positive feedback for learning
    if (currentImageHash && currentRecognitionData) {
        const correctionData = {
            image_hash: currentImageHash,
            corrected_food: currentRecognitionData,
            user_feedback: 'Correct recognition'
        };
        
        submitCorrectionToServer(correctionData);
        
        // Update confidence level to show it was confirmed correct
        const confidenceLevel = document.getElementById('confidenceLevel');
        if (confidenceLevel) {
            confidenceLevel.textContent = '100%';
        }
        
        // Update the recognition result text
        const recognitionResult = document.getElementById('recognitionResult');
        if (recognitionResult) {
            const resultText = recognitionResult.querySelector('span');
            if (resultText) {
                resultText.innerHTML = `AI Recognition: <span id="confidenceLevel" class="font-semibold">100%</span> confidence (Confirmed)`;
            }
        }
        
        alert('Thank you! The AI has learned from your confirmation.');
    } else {
        console.log('No recognition data available for learning');
        alert('Thank you for confirming the recognition!');
    }
    
    // Hide correction form
    document.getElementById('correctionForm').classList.add('hidden');
}

function showCorrectionForm() {
    document.getElementById('correctionForm').classList.remove('hidden');
}

function hideCorrectionForm() {
    const correctionForm = document.getElementById('correctionForm');
    if (correctionForm) {
        correctionForm.classList.add('hidden');
        
        // Clear the correction form fields
        const correctFoodName = document.getElementById('correctFoodName');
        const correctCategory = document.getElementById('correctCategory');
        const correctionFeedback = document.getElementById('correctionFeedback');
        
        if (correctFoodName) correctFoodName.value = '';
        if (correctCategory) correctCategory.value = '';
        if (correctionFeedback) correctionFeedback.value = '';
    }
}

function submitCorrection() {
    const correctFoodNameElement = document.getElementById('correctFoodName');
    const correctCategoryElement = document.getElementById('correctCategory');
    const correctionFeedbackElement = document.getElementById('correctionFeedback');
    
    if (!correctFoodNameElement || !correctCategoryElement) {
        console.error('Correction form elements not found');
        alert('Error: Form elements not found. Please refresh the page and try again.');
        return;
    }
    
    const correctFoodName = correctFoodNameElement.value;
    const correctCategory = correctCategoryElement.value;
    const correctionFeedback = correctionFeedbackElement ? correctionFeedbackElement.value : '';
    
    if (!correctFoodName || !correctCategory) {
        alert('Please provide the correct food name and category');
        return;
    }
    
    // Get category name from select
    const categorySelect = document.getElementById('correctCategory');
    const selectedCategory = categorySelect.options[categorySelect.selectedIndex].text;
    
    // Generate a simple hash if none exists
    const imageHash = currentImageHash || btoa(Date.now() + Math.random());
    
    const correctionData = {
        image_hash: imageHash,
        corrected_food: {
            food_name: correctFoodName,
            category: selectedCategory,
            description: `Corrected: ${correctFoodName}`,
            ingredients: '',
            allergens: '',
            is_vegetarian: false,
            is_spicy: false
        },
        user_feedback: correctionFeedback || 'User correction'
    };
    
    // Apply the correction to the form fields
    applyCorrectionToForm(correctFoodName, selectedCategory, correctionData.corrected_food);
    
    // Submit the correction to the server
    submitCorrectionToServer(correctionData);
}

function applyCorrectionToForm(correctFoodName, correctCategory, correctedFood) {
    // Update the main form fields with the corrected information
    const menuNameField = document.getElementById('menuName');
    const menuCategoryField = document.getElementById('menuCategory');
    const menuDescriptionField = document.getElementById('menuDescription');
    
    if (menuNameField) {
        menuNameField.value = correctFoodName;
    }
    
    if (menuDescriptionField) {
        menuDescriptionField.value = `Corrected: ${correctFoodName}`;
    }
    
    // Update category selection
    if (menuCategoryField) {
        for (let option of menuCategoryField.options) {
            if (option.text.toLowerCase() === correctCategory.toLowerCase()) {
                option.selected = true;
                break;
            }
        }
    }
    
    // Update the recognition result display
    const confidenceLevel = document.getElementById('confidenceLevel');
    if (confidenceLevel) {
        confidenceLevel.textContent = '98%'; // High confidence for corrections
    }
    
    // Update the recognition result text
    const recognitionResult = document.getElementById('recognitionResult');
    if (recognitionResult) {
        const resultText = recognitionResult.querySelector('span');
        if (resultText) {
            resultText.innerHTML = `AI Recognition: <span id="confidenceLevel" class="font-semibold">98%</span> confidence (Corrected)`;
        }
    }
    
    // Hide the correction form
    hideCorrectionForm();
    
    // Show success message
    alert('Correction applied! The form has been updated with the correct information.');
}

function submitCorrectionToServer(correctionData) {
    fetch('{{ route("restaurant.ai.correct", $restaurant->slug) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(correctionData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Thank you! The AI has learned from your correction.');
            hideCorrectionForm();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Correction error:', error);
        alert('Error submitting correction. Please try again.');
    });
}
</script>

<!-- AI Menu Modal -->
<x-modal name="ai-menu-modal" maxWidth="md">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">AI Food Recognition</h3>
            <button onclick="closeAIMenuModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="aiMenuForm" class="space-y-4">
            @csrf
            
            <!-- Image Upload -->
            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                <label for="foodImage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Take Food Photo</label>
                <input type="file" id="foodImage" name="image" accept="image/*" capture="environment" 
                       class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 dark:file:bg-orange-900 dark:file:text-orange-300">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">📸 Click to open camera and take a photo</p>
            </div>
            
            <div class="bg-orange-100 dark:bg-orange-900/20 p-2 rounded-lg">
                <button type="button" onclick="recognizeFood()" id="recognizeBtn"
                        class="w-full px-3 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 font-medium text-sm">
                    <i class="fas fa-robot mr-1"></i>Recognize Food
                </button>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 text-center">Click to analyze image</p>
            </div>
            
            <!-- Recognition Result -->
            <div id="recognitionResult" class="hidden p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700">
                <div class="flex items-center text-sm text-green-700 dark:text-green-300 mb-2">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>AI Recognition: <span id="confidenceLevel" class="font-semibold">95%</span> confidence</span>
                </div>
                
                <!-- Correction Section -->
                <div class="border-t border-green-200 dark:border-green-700 pt-2 mt-2">
                    <p class="text-xs text-green-600 dark:text-green-400 mb-2">Was this recognition correct?</p>
                    <div class="flex space-x-2">
                        <button type="button" onclick="markAsCorrect()" 
                                class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">
                            ✅ Yes, Correct
                        </button>
                        <button type="button" onclick="showCorrectionForm()" 
                                class="px-2 py-1 bg-orange-500 text-white text-xs rounded hover:bg-orange-600">
                            ❌ No, Wrong
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Correction Form -->
            <div id="correctionForm" class="hidden p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-700">
                <h5 class="text-sm font-medium text-orange-800 dark:text-orange-200 mb-2">Help us improve! What is this food?</h5>
                
                <div class="space-y-2">
                    <div>
                        <label for="correctFoodName" class="block text-xs font-medium text-orange-700 dark:text-orange-300">Correct Food Name</label>
                        <input type="text" id="correctFoodName" 
                               class="mt-1 block w-full rounded-md border-orange-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-orange-700 dark:border-orange-600 dark:text-white text-sm">
                    </div>
                    
                    <div>
                        <label for="correctCategory" class="block text-xs font-medium text-orange-700 dark:text-orange-300">Correct Category</label>
                        <select id="correctCategory" 
                                class="mt-1 block w-full rounded-md border-orange-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-orange-700 dark:border-orange-600 dark:text-white text-sm">
                            <option value="">Select Category</option>
                            @foreach($restaurant->categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="correctionFeedback" class="block text-xs font-medium text-orange-700 dark:text-orange-300">Additional Feedback (Optional)</label>
                        <textarea id="correctionFeedback" rows="2"
                                  class="mt-1 block w-full rounded-md border-orange-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-orange-700 dark:border-orange-600 dark:text-white text-sm"
                                  placeholder="What should the AI have recognized?"></textarea>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button type="button" onclick="submitCorrection()" 
                                class="px-3 py-1 bg-orange-600 text-white text-xs rounded hover:bg-orange-700">
                            Submit Correction
                        </button>
                        <button type="button" onclick="hideCorrectionForm()" 
                                class="px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Menu Item Details -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Menu Item Details</h4>
                
                <div class="space-y-1">
                    <label for="menuName" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input type="text" id="menuName" name="name" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                </div>
                
                <div class="space-y-1">
                    <label for="menuCategory" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Category</label>
                    <select id="menuCategory" name="category_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                        <option value="">Select Category</option>
                        @foreach($restaurant->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="space-y-1">
                    <label for="menuPrice" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Price (₦)</label>
                    <input type="number" id="menuPrice" name="price" required min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                </div>
                
                <div class="space-y-1">
                    <label for="menuDescription" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea id="menuDescription" name="description" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"></textarea>
                </div>
                
                <div class="space-y-1">
                    <label for="menuIngredients" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Ingredients</label>
                    <input type="text" id="menuIngredients" name="ingredients"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                </div>
                
                <div class="space-y-1">
                    <label for="menuAllergens" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Allergens</label>
                    <input type="text" id="menuAllergens" name="allergens"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded-lg">
                    <h5 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Options</h5>
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center">
                            <input type="checkbox" id="menuIsVegetarian" name="is_vegetarian" value="1"
                                   class="h-3 w-3 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="menuIsVegetarian" class="ml-1 block text-xs text-gray-900 dark:text-white">Vegetarian</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="menuIsSpicy" name="is_spicy" value="1"
                                   class="h-3 w-3 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="menuIsSpicy" class="ml-1 block text-xs text-gray-900 dark:text-white">Spicy</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="menuIsAvailable" name="is_available" value="1" checked
                                   class="h-3 w-3 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="menuIsAvailable" class="ml-1 block text-xs text-gray-900 dark:text-white">Available</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-2 pt-3">
                <button type="button" onclick="closeAIMenuModal()" 
                        class="px-2 py-1 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-2 py-1 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                    Add Menu Item
                </button>
            </div>
        </form>
    </div>
</x-modal>

<!-- Promotion Modal -->
<x-modal name="promotion-modal" maxWidth="lg">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="promotionModalTitle">Create Promotion</h3>
            <button onclick="closePromotionModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="promotionForm" onsubmit="submitPromotion(event)">
            <div class="space-y-4">
                <div>
                    <label for="promotionTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Promotion Title</label>
                    <input type="text" id="promotionTitle" name="title" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p class="text-xs text-gray-500 mt-1">This will be displayed as the main headline</p>
                </div>
                
                <div>
                    <label for="promotionDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea id="promotionDescription" name="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                              placeholder="Describe your promotion, special offers, or unique selling points"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="promotionBadge" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Badge Text</label>
                        <select id="promotionBadge" name="badge_text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">No Badge</option>
                            <option value="New">New</option>
                            <option value="Popular">Popular</option>
                            <option value="Limited Time">Limited Time</option>
                            <option value="Special Offer">Special Offer</option>
                            <option value="Best Seller">Best Seller</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="promotionBadgeColor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Badge Color</label>
                        <select id="promotionBadgeColor" name="badge_color"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="orange">Orange</option>
                            <option value="green">Green</option>
                            <option value="red">Red</option>
                            <option value="blue">Blue</option>
                            <option value="purple">Purple</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="promotionCtaText" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Button Text</label>
                        <input type="text" id="promotionCtaText" name="cta_text" value="Order Now"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="promotionCtaLink" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Custom Link (Optional)</label>
                        <input type="url" id="promotionCtaLink" name="cta_link"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="Leave empty to use menu link">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="promotionStartDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                        <input type="datetime-local" id="promotionStartDate" name="featured_from"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="promotionEndDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                        <input type="datetime-local" id="promotionEndDate" name="featured_until"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>
                
                <div>
                    <label for="promotionImage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Promotion Image (Optional)</label>
                    <input type="file" id="promotionImage" name="ad_image" accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    <p class="text-xs text-gray-500 mt-1">Upload a custom image for your promotion. If not provided, your restaurant logo will be used.</p>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closePromotionModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                    <span id="promotionSubmitText">Create Promotion</span>
                </button>
            </div>
        </form>
    </div>
</x-modal>

<script>
// Promotion functions
let currentPromotionId = null;

function createPromotion() {
    currentPromotionId = null;
    document.getElementById('promotionModalTitle').textContent = 'Create Promotion';
    document.getElementById('promotionSubmitText').textContent = 'Create Promotion';
    document.getElementById('promotionForm').reset();
    
    // Set default dates
    const now = new Date();
    const future = new Date();
    future.setDate(future.getDate() + 30);
    
    document.getElementById('promotionStartDate').value = now.toISOString().slice(0, 16);
    document.getElementById('promotionEndDate').value = future.toISOString().slice(0, 16);
    
    // Show modal
    const modal = document.getElementById('promotion-modal');
    modal.classList.remove('hidden');
}

function editPromotion(promotionId) {
    currentPromotionId = promotionId;
    document.getElementById('promotionModalTitle').textContent = 'Edit Promotion';
    document.getElementById('promotionSubmitText').textContent = 'Update Promotion';
    
    // Fetch promotion data and populate form
    fetch(`/api/featured-restaurants/${promotionId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('promotionTitle').value = data.title || '';
            document.getElementById('promotionDescription').value = data.description || '';
            document.getElementById('promotionBadge').value = data.badge_text || '';
            document.getElementById('promotionBadgeColor').value = data.badge_color || 'orange';
            document.getElementById('promotionCtaText').value = data.cta_text || 'Order Now';
            document.getElementById('promotionCtaLink').value = data.cta_link || '';
            
            if (data.featured_from) {
                document.getElementById('promotionStartDate').value = data.featured_from.slice(0, 16);
            }
            if (data.featured_until) {
                document.getElementById('promotionEndDate').value = data.featured_until.slice(0, 16);
            }
        })
        .catch(error => {
            console.error('Error fetching promotion:', error);
            alert('Error loading promotion data');
        });
    
    // Show modal
    const modal = document.getElementById('promotion-modal');
    modal.classList.remove('hidden');
}

function closePromotionModal() {
    const modal = document.getElementById('promotion-modal');
    modal.classList.add('hidden');
    currentPromotionId = null;
}

function submitPromotion(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('restaurant_id', {{ $restaurant->id }});
    
    const url = currentPromotionId 
        ? `/api/featured-restaurants/${currentPromotionId}` 
        : '/api/featured-restaurants';
    
    const method = currentPromotionId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closePromotionModal();
            // Reload page to show updated promotion
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to save promotion'));
        }
    })
    .catch(error => {
        console.error('Error saving promotion:', error);
        alert('Error saving promotion');
    });
}
</script>
@endsection 