@extends('layouts.app')

@section('title', 'My Orders - Abuja Eat')

@section('content')
<div class="container mx-auto px-2 py-4 max-w-md bg-white dark:bg-gray-900 min-h-screen">
    <!-- Top Bar with Title and Dark Mode Toggle -->
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">My Orders</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('menu.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-orange-100 dark:bg-gray-700 text-orange-500 dark:text-orange-300 transition">
                <i class="fas fa-plus"></i>
            </a>
            <button id="themeToggle" class="w-10 h-10 flex items-center justify-center rounded-full bg-orange-100 dark:bg-gray-700 text-orange-500 dark:text-yellow-300 transition">
                <i id="themeIcon" class="fas fa-sun"></i>
            </button>
        </div>
    </div>

    <!-- Order Status Filter -->
    <div class="mb-4">
        <div class="flex gap-2 overflow-x-auto hide-scrollbar" id="statusFilter">
            <button class="status-btn active px-4 py-2 bg-orange-500 text-white dark:text-white rounded-full font-semibold text-xs whitespace-nowrap" data-status="all">All Orders</button>
            <button class="status-btn px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full font-semibold text-xs text-gray-700 dark:text-gray-200 whitespace-nowrap" data-status="pending">Pending</button>
            <button class="status-btn px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full font-semibold text-xs text-gray-700 dark:text-gray-200 whitespace-nowrap" data-status="preparing">Preparing</button>
            <button class="status-btn px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full font-semibold text-xs text-gray-700 dark:text-gray-200 whitespace-nowrap" data-status="ready">Ready</button>
            <button class="status-btn px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full font-semibold text-xs text-gray-700 dark:text-gray-200 whitespace-nowrap" data-status="delivered">Delivered</button>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Email Verification Notice -->
    @if(!Auth::user()->hasVerifiedEmail())
        <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>Please verify your email address to view your orders.</span>
                </div>
                <a href="{{ route('verification.notice') }}" class="text-yellow-800 hover:text-yellow-900 font-medium">
                    Verify Now
                </a>
            </div>
        </div>
    @endif

    @if($orders->count() > 0)
        <!-- Orders Grid -->
        <div class="space-y-4 mb-24" id="ordersGrid">
            @foreach($orders as $order)
                <div class="order-card bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition-all duration-200 overflow-hidden flex flex-col cursor-pointer transform hover:scale-105 border-2 border-transparent" 
                     data-status="{{ $order->status }}"
                     onclick="viewOrder({{ $order->id }})">
                    <!-- Order Header -->
                    <div class="h-16 bg-gradient-to-br from-orange-200 to-orange-400 dark:from-gray-700 dark:to-gray-900 flex items-center justify-between px-4">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-receipt text-xl text-white opacity-80"></i>
                            <div>
                                <h3 class="text-sm font-bold text-white">Order #{{ $order->order_number }}</h3>
                                <p class="text-xs text-white opacity-80">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->status_badge }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            <i class="fas fa-chevron-right text-white opacity-60"></i>
                        </div>
                    </div>
                    
                    <!-- Order Content -->
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Customer Info -->
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $order->customer_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->phone_number }}</p>
                                </div>
                                <span class="text-lg font-bold text-orange-500 dark:text-orange-300">{{ $order->formatted_total }}</span>
                            </div>
                            
                            <!-- Order Items Preview -->
                            <div class="mb-3">
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Items:</p>
                                <div class="space-y-1">
                                    @foreach($order->orderItems->take(2) as $item)
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-gray-800 dark:text-white">
                                                {{ $item->quantity }}x {{ $item->menuItem->name }}
                                            </span>
                                            <span class="text-gray-600 dark:text-gray-400">
                                                ₦{{ number_format($item->total_price, 0) }}
                                            </span>
                                        </div>
                                    @endforeach
                                    @if($order->orderItems->count() > 2)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 italic">
                                            +{{ $order->orderItems->count() - 2 }} more items
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Delivery Address -->
                            <div class="mb-3">
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ Str::limit($order->delivery_address, 40) }}
                                </p>
                            </div>
                            
                            <!-- Progress Indicator -->
                            <div class="mb-3">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-600 dark:text-gray-400">Progress:</span>
                                    <div class="flex items-center gap-1">
                                        <!-- Step 1: Order Placed -->
                                        <div class="w-3 h-3 rounded-full bg-green-500 flex items-center justify-center">
                                            <i class="fas fa-check text-white text-xs"></i>
                                        </div>
                                        
                                        <!-- Step 2: Confirmed -->
                                        <div class="w-3 h-3 rounded-full {{ $order->status === 'pending' ? 'bg-gray-300 dark:bg-gray-600' : 'bg-green-500' }} flex items-center justify-center">
                                            @if($order->status === 'pending')
                                                <i class="fas fa-clock text-gray-500 dark:text-gray-400 text-xs"></i>
                                            @else
                                                <i class="fas fa-check text-white text-xs"></i>
                                            @endif
                                        </div>
                                        
                                        <!-- Step 3: Preparing -->
                                        <div class="w-3 h-3 rounded-full {{ in_array($order->status, ['pending']) ? 'bg-gray-300 dark:bg-gray-600' : (in_array($order->status, ['confirmed', 'preparing', 'ready', 'delivered']) ? 'bg-green-500' : 'bg-orange-500') }} flex items-center justify-center">
                                            @if(in_array($order->status, ['pending']))
                                                <i class="fas fa-clock text-gray-500 dark:text-gray-400 text-xs"></i>
                                            @elseif(in_array($order->status, ['confirmed', 'preparing', 'ready', 'delivered']))
                                                <i class="fas fa-check text-white text-xs"></i>
                                            @else
                                                <i class="fas fa-utensils text-white text-xs"></i>
                                            @endif
                                        </div>
                                        
                                        <!-- Step 4: Ready -->
                                        <div class="w-3 h-3 rounded-full {{ in_array($order->status, ['pending', 'confirmed']) ? 'bg-gray-300 dark:bg-gray-600' : (in_array($order->status, ['preparing', 'ready', 'delivered']) ? 'bg-green-500' : 'bg-orange-500') }} flex items-center justify-center">
                                            @if(in_array($order->status, ['pending', 'confirmed']))
                                                <i class="fas fa-clock text-gray-500 dark:text-gray-400 text-xs"></i>
                                            @elseif(in_array($order->status, ['preparing', 'ready', 'delivered']))
                                                <i class="fas fa-check text-white text-xs"></i>
                                            @else
                                                <i class="fas fa-check-double text-white text-xs"></i>
                                            @endif
                                        </div>
                                        
                                        <!-- Step 5: Delivered -->
                                        <div class="w-3 h-3 rounded-full {{ $order->status === 'delivered' ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }} flex items-center justify-center">
                                            @if($order->status === 'delivered')
                                                <i class="fas fa-check text-white text-xs"></i>
                                            @else
                                                <i class="fas fa-truck text-gray-500 dark:text-gray-400 text-xs"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Progress Bar -->
                                <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1">
                                    <div class="h-1 bg-green-500 rounded-full transition-all duration-500" style="width: {{ 
                                        $order->status === 'pending' ? '20%' : 
                                        ($order->status === 'confirmed' ? '40%' : 
                                        ($order->status === 'preparing' ? '60%' : 
                                        ($order->status === 'ready' ? '80%' : 
                                        ($order->status === 'delivered' ? '100%' : '20%')))) 
                                    }}"></div>
                                </div>
                                
                                <!-- Countdown Timer for Active Orders -->
                                @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-xs text-gray-600 dark:text-gray-400">ETA:</span>
                                        <div class="countdown-timer text-xs font-medium text-orange-600 dark:text-orange-400" 
                                             data-order-time="{{ $order->created_at }}" 
                                             data-order-status="{{ $order->status }}">
                                            Calculating...
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                            @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                                <button onclick="event.stopPropagation(); trackOrder({{ $order->id }})" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 px-3 rounded-lg text-xs font-medium transition">
                                    <i class="fas fa-eye mr-1"></i>Track
                                </button>
                            @endif
                            <button onclick="event.stopPropagation(); viewOrderDetails({{ $order->id }})" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-3 rounded-lg text-xs font-medium transition">
                                <i class="fas fa-info-circle mr-1"></i>Details
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12 mb-24">
            <div class="w-24 h-24 bg-gradient-to-br from-orange-200 to-orange-400 dark:from-gray-700 dark:to-gray-900 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shopping-bag text-3xl text-white opacity-80"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No orders yet</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Start ordering delicious food from our menu!</p>
            <a href="{{ route('menu.index') }}" class="inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition">
                <i class="fas fa-utensils mr-2"></i>Browse Menu
            </a>
        </div>
    @endif
</div>

<!-- Bottom Navigation Bar -->
<nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-lg z-50 flex justify-around items-center py-1 px-2 max-w-md mx-auto w-full">
    <a href="/menu" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
        <!-- Home Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0h6" />
        </svg>
        <span class="text-xs mt-0.5">Home</span>
    </a>
    <a href="/cart" class="flex flex-col items-center text-gray-400 dark:text-gray-400 relative">
        <!-- Cart Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M1 1h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 8m12-8l2 8" />
        </svg>
        <span id="cartCount" class="absolute -top-1 -right-1 bg-yellow-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
        <span class="text-xs mt-0.5">Cart</span>
    </a>
    <a href="{{ route('user.orders') }}" class="flex flex-col items-center text-orange-500 dark:text-orange-300">
        <!-- Orders Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span class="text-xs mt-0.5">Orders</span>
    </a>
    <a href="https://wa.me/" target="_blank" class="flex flex-col items-center text-green-500 dark:text-green-400">
        <!-- WhatsApp Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.72 13.06a6.5 6.5 0 10-2.72 2.72l3.85 1.1a1 1 0 001.26-1.26l-1.1-3.85z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 11a3.5 3.5 0 005 0" />
        </svg>
        <span class="text-xs mt-0.5">WhatsApp</span>
    </a>
    <a href="{{ Auth::check() ? route('profile.edit') : route('login') }}" class="flex flex-col items-center text-orange-500 dark:text-orange-300">
        <!-- Login/Profile Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="8" r="4" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20v-1a4 4 0 014-4h8a4 4 0 014 4v1" />
        </svg>
        <span class="text-xs mt-0.5">{{ Auth::check() ? 'Profile' : 'Login' }}</span>
    </a>
</nav>

<style>
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

/* Order card hover effects */
.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.order-card:active {
    transform: scale(0.98);
}

/* Status filter button styles */
.status-btn.active {
    background-color: #f97316 !important;
    color: white !important;
    border-color: #f97316 !important;
}

.status-btn:hover {
    transform: scale(1.05);
}
</style>

<script>
// Light/Dark mode toggle
const themeToggle = document.getElementById('themeToggle');
const themeIcon = document.getElementById('themeIcon');

function setTheme(dark) {
    if (dark) {
        document.documentElement.classList.add('dark');
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
    } else {
        document.documentElement.classList.remove('dark');
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
    }
}

// Check local storage or system preference
const userPref = localStorage.getItem('theme');
const systemPref = window.matchMedia('(prefers-color-scheme: dark)').matches;
setTheme(userPref === 'dark' || (!userPref && systemPref));

themeToggle.addEventListener('click', () => {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    setTheme(isDark);
});

// Status filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const statusButtons = document.querySelectorAll('[data-status]');
    statusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const status = this.getAttribute('data-status');
            filterByStatus(status);
            
            // Update active state
            statusButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Update cart count
    updateCartCount();
    
    // Initialize countdown timers
    initializeOrderCountdowns();
    
    // Setup auto-refresh for active orders
    setupAutoRefresh();
});

function filterByStatus(status) {
    const orderCards = document.querySelectorAll('.order-card');
    
    orderCards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        
        if (status === 'all' || cardStatus === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function viewOrder(orderId) {
    window.location.href = `/orders/${orderId}`;
}

function trackOrder(orderId) {
    window.location.href = `/orders/${orderId}`;
}

function viewOrderDetails(orderId) {
    window.location.href = `/orders/${orderId}`;
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const cartCount = document.getElementById('cartCount');
    
    if (cartCount) {
        cartCount.textContent = totalItems;
        cartCount.classList.toggle('hidden', totalItems === 0);
    }
}

// Countdown Timer for Order Cards
function initializeOrderCountdowns() {
    const countdownElements = document.querySelectorAll('.countdown-timer');
    
    countdownElements.forEach(element => {
        const orderTime = new Date(element.getAttribute('data-order-time')).getTime();
        const orderStatus = element.getAttribute('data-order-status');
        
        function updateCountdown() {
            const now = new Date().getTime();
            const elapsedMinutes = Math.floor((now - orderTime) / (1000 * 60));
            
            // Calculate remaining time based on status
            let totalEstimatedMinutes = 45;
            let remainingMinutes = 0;
            
            switch(orderStatus) {
                case 'pending':
                    remainingMinutes = Math.max(0, totalEstimatedMinutes - elapsedMinutes);
                    break;
                case 'confirmed':
                    remainingMinutes = Math.max(0, totalEstimatedMinutes - elapsedMinutes - 10);
                    break;
                case 'preparing':
                    remainingMinutes = Math.max(0, totalEstimatedMinutes - elapsedMinutes - 25);
                    break;
                case 'ready':
                    remainingMinutes = Math.max(0, totalEstimatedMinutes - elapsedMinutes - 40);
                    break;
                default:
                    remainingMinutes = Math.max(0, totalEstimatedMinutes - elapsedMinutes);
            }
            
            if (remainingMinutes <= 0) {
                element.textContent = 'Ready!';
                element.classList.add('text-green-600', 'dark:text-green-400');
                element.classList.remove('text-orange-600', 'dark:text-orange-400');
                return;
            }
            
            const hours = Math.floor(remainingMinutes / 60);
            const minutes = remainingMinutes % 60;
            
            if (hours > 0) {
                element.textContent = `${hours}h ${minutes}m`;
            } else {
                element.textContent = `${minutes}m`;
            }
            
            // Change color when less than 10 minutes remaining
            if (remainingMinutes <= 10) {
                element.classList.add('text-red-600', 'dark:text-red-400');
                element.classList.remove('text-orange-600', 'dark:text-orange-400');
            }
        }
        
        // Initial update
        updateCountdown();
        
        // Update every minute
        setInterval(updateCountdown, 60000);
    });
}

// Auto-refresh page every 30 seconds for active orders
function setupAutoRefresh() {
    const hasActiveOrders = document.querySelectorAll('.countdown-timer').length > 0;
    if (hasActiveOrders) {
        setTimeout(() => {
            window.location.reload();
        }, 30000);
    }
}
</script>
@endsection 