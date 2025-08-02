@extends('layouts.app')

@section('title', $restaurant->name . ' - Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-8">
                <div class="flex items-center space-x-4">
                    <img src="{{ \App\Helpers\PWAHelper::getRestaurantImage($restaurant->logo, 'square') }}" alt="{{ $restaurant->name }}" class="w-16 h-16 rounded-lg object-contain bg-gray-100 dark:bg-gray-700">
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
document.getElementById('statusUpdateForm').addEventListener('submit', function(e) {
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
</script>
@endsection 