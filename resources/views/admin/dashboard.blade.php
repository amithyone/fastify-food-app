@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-8">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tools text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @if(Auth::user()->isAdmin())
                                System Administration
                            @else
                                Restaurant Management
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.orders') }}" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Manage Orders
                    </a>
                    
                    @if(Auth::user()->isRestaurantOwner())
                        <a href="{{ route('restaurant.dashboard', Auth::user()->restaurant->slug) }}" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-store mr-2"></i>
                            Restaurant Dashboard
                        </a>
                    @endif
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
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            @if(Auth::user()->isAdmin())
                                Total Orders
                            @else
                                My Orders
                            @endif
                        </p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            @if(Auth::user()->isAdmin())
                                {{ \App\Models\Order::count() }}
                            @else
                                {{ \App\Models\Order::where('restaurant_id', Auth::user()->restaurant_id)->count() }}
                            @endif
                        </p>
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
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            @if(Auth::user()->isAdmin())
                                {{ \App\Models\Order::where('status', 'pending')->count() }}
                            @else
                                {{ \App\Models\Order::where('restaurant_id', Auth::user()->restaurant_id)->where('status', 'pending')->count() }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-md flex items-center justify-center">
                            <i class="fas fa-store text-green-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            @if(Auth::user()->isAdmin())
                                Restaurants
                            @else
                                Menu Items
                            @endif
                        </p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            @if(Auth::user()->isAdmin())
                                {{ \App\Models\Restaurant::count() }}
                            @else
                                {{ \App\Models\MenuItem::where('restaurant_id', Auth::user()->restaurant_id)->count() }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-md flex items-center justify-center">
                            <i class="fas fa-users text-purple-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            @if(Auth::user()->isAdmin())
                                Users
                            @else
                                Today's Earnings
                            @endif
                        </p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            @if(Auth::user()->isAdmin())
                                {{ \App\Models\User::count() }}
                            @else
                                ₦{{ number_format(\App\Models\Order::where('restaurant_id', Auth::user()->restaurant_id)->where('status', 'confirmed')->whereDate('created_at', today())->sum('total_amount') / 100, 2) }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <a href="{{ route('admin.orders') }}" 
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Manage Orders</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">View and update order statuses</p>
                    </div>
                </div>
            </a>

            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.menu') }}" 
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
            @else
                <a href="{{ route('restaurant.menu', Auth::user()->restaurant->slug) }}" 
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
            @endif

            @if(Auth::user()->isRestaurantOwner())
                <a href="{{ route('restaurant.qr-codes', Auth::user()->restaurant->slug) }}" 
                    class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-qrcode text-orange-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">QR Codes</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Generate QR codes for tables</p>
                        </div>
                    </div>
                </a>
            @endif

            @if(Auth::user()->isRestaurantOwner())
                <a href="{{ route('restaurant.wallet', Auth::user()->restaurant->slug) }}" 
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
            @endif
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @if(Auth::user()->isAdmin())
                        @php
                            $recentOrders = \App\Models\Order::with('restaurant')->latest()->take(5)->get();
                        @endphp
                    @else
                        @php
                            $recentOrders = \App\Models\Order::where('restaurant_id', Auth::user()->restaurant_id)->latest()->take(5)->get();
                        @endphp
                    @endif

                    @forelse($recentOrders as $order)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        Order #{{ $order->id }} - {{ $order->customer_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $order->created_at->diffForHumans() }}
                                        @if(Auth::user()->isAdmin())
                                            - {{ $order->restaurant->name }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'preparing') bg-orange-100 text-orange-800
                                    @elseif($order->status === 'ready') bg-green-100 text-green-800
                                    @elseif($order->status === 'delivered') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    ₦{{ number_format($order->total_amount / 100, 2) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400">No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 