@extends('layouts.app')

@section('title', 'Order Confirmation - Abuja Eat')

@section('content')
<div class="container mx-auto px-2 py-4 max-w-md bg-white dark:bg-gray-900 min-h-screen">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6 sticky top-0 z-40 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 py-2">
        <div class="flex items-center gap-3">
            <a href="/menu" class="text-gray-600 dark:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0h6" />
                </svg>
            </a>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Order Confirmation</h1>
        </div>
        <button id="darkModeToggle" class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
            <i class="fas fa-sun"></i>
        </button>
    </div>

    <!-- Success Message -->
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h2 class="text-2xl font-bold text-green-600 dark:text-white mb-2">Order Placed Successfully!</h2>
                <p class="text-lg mb-2 dark:text-white">Your order number is: {{ $order->order_number }}</p>
                @if(!Auth::check() && $order->tracking_code)
                    <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-900 dark:text-blue-200 mb-1">Your Tracking Code:</p>
                                <p class="text-2xl font-bold font-mono text-blue-600 dark:text-blue-400">{{ $order->tracking_code }}</p>
                                <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">Valid for 24 hours</p>
                            </div>
                            <button onclick="copyTrackingCode()" 
                                class="inline-flex items-center px-3 py-2 border border-blue-300 dark:border-blue-600 rounded-md shadow-sm text-sm font-medium text-blue-700 dark:text-blue-300 bg-white dark:bg-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                <i class="fas fa-copy mr-1"></i>
                                Copy
                            </button>
                        </div>
                        <div class="mt-3 pt-3 border-t border-blue-200 dark:border-blue-800">
                            <p class="text-xs text-blue-700 dark:text-blue-300 mb-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Save this code to track your order later
                            </p>
                            <a href="{{ route('orders.track-form') }}" 
                                class="inline-flex items-center px-3 py-2 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 dark:text-blue-200 dark:bg-blue-800 dark:hover:bg-blue-700 transition-colors">
                                <i class="fas fa-search mr-1"></i>
                                Track Order
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Video Status Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Live Kitchen Status</h2>
        
        <!-- Video Container -->
        <div class="relative bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden mb-4" style="aspect-ratio: 16/9;">
            <!-- YouTube Shorts Embed -->
            <div id="youtubeVideo" class="w-full h-full">
                <iframe 
                    class="w-full h-full"
                    src="https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=0&mute=1&controls=1&rel=0&modestbranding=1"
                    title="Abuja Eat Kitchen Live"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
            
            <!-- Local Video Fallback (Hidden by default) -->
            <video id="localVideo" class="w-full h-full hidden" controls muted loop>
                <source src="/videos/kitchen-status.mp4" type="video/mp4">
                <source src="/videos/kitchen-status.webm" type="video/webm">
                Your browser does not support the video tag.
            </video>
            
            <!-- Video Placeholder (shown when video fails to load) -->
            <div id="videoPlaceholder" class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-orange-100 to-yellow-100 dark:from-orange-900/20 dark:to-yellow-900/20">
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-utensils text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Kitchen Live</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Watch our chefs prepare your order</p>
                </div>
            </div>
            
            <!-- Video Controls -->
            <div class="absolute top-2 right-2 flex gap-2">
                <button id="toggleVideo" class="bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-colors">
                    <i class="fas fa-play"></i>
                </button>
                <button id="switchVideo" class="bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-colors">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
        
        <!-- Video Status Info -->
        <div class="flex items-center justify-between text-sm">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-gray-600 dark:text-gray-400">Live from kitchen</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-eye text-gray-500"></i>
                <span class="text-gray-600 dark:text-gray-400">Live</span>
            </div>
        </div>
    </div>

    <!-- Order Progress with Countdown -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Progress</h2>
        
        <!-- Countdown Timer -->
        <div class="bg-gradient-to-r from-orange-50 to-yellow-50 dark:from-orange-900/20 dark:to-yellow-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-orange-800 dark:text-orange-200">Estimated Delivery Time</h3>
                    <p class="text-xs text-orange-600 dark:text-orange-300">From order placement</p>
                </div>
                <div class="text-right">
                    <div id="countdown" class="text-2xl font-bold text-orange-600 dark:text-orange-400">--:--</div>
                    <div class="text-xs text-orange-600 dark:text-orange-300">remaining</div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <!-- Progress Steps -->
            <div class="relative">
                <div class="flex items-center justify-between">
                    <!-- Step 1: Order Placed -->
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-bold shadow-lg">
                            <i class="fas fa-check"></i>
                        </div>
                        <span class="text-xs mt-2 text-gray-600 dark:text-gray-400 text-center">Order Placed</span>
                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">0 min</span>
                    </div>
                    
                    <!-- Step 2: Order Confirmed -->
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full {{ $order->status === 'pending' ? 'bg-gray-300 dark:bg-gray-600' : 'bg-green-500 shadow-lg' }} flex items-center justify-center text-white text-sm font-bold transition-all duration-500">
                            @if($order->status === 'pending')
                                <i class="fas fa-clock"></i>
                            @else
                                <i class="fas fa-check"></i>
                            @endif
                        </div>
                        <span class="text-xs mt-2 text-gray-600 dark:text-gray-400 text-center">Confirmed</span>
                        <span class="text-xs {{ $order->status === 'pending' ? 'text-gray-500 dark:text-gray-400' : 'text-green-600 dark:text-green-400 font-medium' }}">5-10 min</span>
                    </div>
                    
                    <!-- Step 3: Preparing -->
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full {{ in_array($order->status, ['pending']) ? 'bg-gray-300 dark:bg-gray-600' : (in_array($order->status, ['confirmed', 'preparing', 'ready', 'delivered']) ? 'bg-green-500 shadow-lg' : 'bg-orange-500 shadow-lg') }} flex items-center justify-center text-white text-sm font-bold transition-all duration-500">
                            @if(in_array($order->status, ['pending']))
                                <i class="fas fa-clock"></i>
                            @elseif(in_array($order->status, ['confirmed', 'preparing', 'ready', 'delivered']))
                                <i class="fas fa-check"></i>
                            @else
                                <i class="fas fa-utensils"></i>
                            @endif
                        </div>
                        <span class="text-xs mt-2 text-gray-600 dark:text-gray-400 text-center">Preparing</span>
                        <span class="text-xs {{ in_array($order->status, ['pending']) ? 'text-gray-500 dark:text-gray-400' : 'text-green-600 dark:text-green-400 font-medium' }}">15-20 min</span>
                    </div>
                    
                    <!-- Step 4: Ready -->
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full {{ in_array($order->status, ['pending', 'confirmed']) ? 'bg-gray-300 dark:bg-gray-600' : (in_array($order->status, ['preparing', 'ready', 'delivered']) ? 'bg-green-500 shadow-lg' : 'bg-orange-500 shadow-lg') }} flex items-center justify-center text-white text-sm font-bold transition-all duration-500">
                            @if(in_array($order->status, ['pending', 'confirmed']))
                                <i class="fas fa-clock"></i>
                            @elseif(in_array($order->status, ['preparing', 'ready', 'delivered']))
                                <i class="fas fa-check"></i>
                            @else
                                <i class="fas fa-check-double"></i>
                            @endif
                        </div>
                        <span class="text-xs mt-2 text-gray-600 dark:text-gray-400 text-center">Ready</span>
                        <span class="text-xs {{ in_array($order->status, ['pending', 'confirmed']) ? 'text-gray-500 dark:text-gray-400' : 'text-green-600 dark:text-green-400 font-medium' }}">25-30 min</span>
                    </div>
                    
                    <!-- Step 5: Delivered -->
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full {{ $order->status === 'delivered' ? 'bg-green-500 shadow-lg' : 'bg-gray-300 dark:bg-gray-600' }} flex items-center justify-center text-white text-sm font-bold transition-all duration-500">
                            @if($order->status === 'delivered')
                                <i class="fas fa-check"></i>
                            @else
                                <i class="fas fa-truck"></i>
                    @endif
                        </div>
                        <span class="text-xs mt-2 text-gray-600 dark:text-gray-400 text-center">Delivered</span>
                        <span class="text-xs {{ $order->status === 'delivered' ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-500 dark:text-gray-400' }}">30-45 min</span>
                    </div>
                </div>
                
                <!-- Progress Line -->
                <div class="absolute top-6 left-6 right-6 h-1 bg-gray-300 dark:bg-gray-600 -z-10">
                    <div id="progressBar" class="h-full bg-green-500 transition-all duration-1000 ease-out" style="width: {{ 
                        $order->status === 'pending' ? '0%' : 
                        ($order->status === 'confirmed' ? '25%' : 
                        ($order->status === 'preparing' ? '50%' : 
                        ($order->status === 'ready' ? '75%' : 
                        ($order->status === 'delivered' ? '100%' : '0%')))) 
                    }}"></div>
                </div>
            </div>

            <!-- Current Status with Enhanced Info -->
            <div class="mt-6 p-4 rounded-lg {{ $order->status === 'pending' ? 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800' : ($order->status === 'confirmed' ? 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800' : ($order->status === 'preparing' ? 'bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800' : ($order->status === 'ready' ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : ($order->status === 'delivered' ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800')))) }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas {{ $order->status === 'pending' ? 'fa-clock text-yellow-500' : ($order->status === 'confirmed' ? 'fa-check-circle text-blue-500' : ($order->status === 'preparing' ? 'fa-utensils text-orange-500' : ($order->status === 'ready' ? 'fa-check-double text-green-500' : ($order->status === 'delivered' ? 'fa-truck text-green-500' : 'fa-times-circle text-red-500')))) }} mr-3 text-xl"></i>
                        <div>
                            <span class="font-semibold text-gray-900 dark:text-white text-lg">
                                {{ ucfirst($order->status) }}
                            </span>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                @if($order->status === 'pending')
                                    Order received • Confirming within 10 minutes
                                @elseif($order->status === 'confirmed')
                                    Order confirmed • Starting preparation
                                @elseif($order->status === 'preparing')
                                    Chefs are cooking • 15-20 minutes remaining
                                @elseif($order->status === 'ready')
                                    Food is ready • Out for delivery
                                @elseif($order->status === 'delivered')
                                    Order completed • Enjoy your meal!
                                @else
                                    Order cancelled
                                @endif
                        </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            @if($order->status === 'pending')
                                ~10 min
                            @elseif($order->status === 'confirmed')
                                ~25 min
                            @elseif($order->status === 'preparing')
                                ~15 min
                            @elseif($order->status === 'ready')
                                ~10 min
                            @elseif($order->status === 'delivered')
                                Complete
                            @else
                                --
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">remaining</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Details</h2>
        
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Order Number:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $order->order_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Order Date:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $order->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Status:</span>
                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-200
                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-200
                    @elseif($order->status === 'preparing') bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-200
                    @elseif($order->status === 'ready') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200
                    @elseif($order->status === 'delivered') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200
                    @else bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-200
                    @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Payment Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Information</h2>
        
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Payment Method:</span>
                <span class="font-semibold text-gray-900 dark:text-white">{{ $order->payment_method_display }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Payment Status:</span>
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->payment_status_badge }}">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </div>
            @if($order->payment_method === 'cash' && $order->payment_status === 'pending')
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-yellow-500 mr-2"></i>
                    <span class="text-sm text-yellow-700 dark:text-yellow-300">
                        Payment will be collected upon delivery
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Customer Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Customer Information</h2>
        
        <div class="space-y-3">
            <div>
                <span class="text-gray-600 dark:text-gray-400">Name:</span>
                <span class="font-semibold text-gray-900 dark:text-white ml-2">{{ $order->customer_name }}</span>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">Phone:</span>
                <span class="font-semibold text-gray-900 dark:text-white ml-2">{{ $order->phone_number }}</span>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">Delivery Address:</span>
                <p class="font-semibold text-gray-900 dark:text-white mt-1">{{ $order->delivery_address }}</p>
            </div>
            @if($order->allergies)
            <div>
                <span class="text-gray-600 dark:text-gray-400">Special Requests:</span>
                <p class="font-semibold text-gray-900 dark:text-white mt-1">{{ $order->allergies }}</p>
            </div>
            @endif
            @if($order->delivery_time)
            <div>
                <span class="text-gray-600 dark:text-gray-400">Delivery Time:</span>
                <span class="font-semibold text-gray-900 dark:text-white ml-2">{{ ucfirst(str_replace('_', ' ', $order->delivery_time)) }}</span>
        </div>
        @endif
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Items</h2>
        
        <div class="space-y-3">
            @foreach($order->orderItems as $item)
            <div class="flex justify-between items-center">
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $item->menuItem->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Qty: {{ $item->quantity }}</p>
                </div>
                <span class="font-semibold text-gray-900 dark:text-white">₦{{ number_format($item->total_price) }}</span>
            </div>
            @endforeach
        </div>
        
        <div class="border-t border-gray-200 dark:border-gray-700 mt-4 pt-4 space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                <span class="font-semibold text-gray-900 dark:text-white">₦{{ number_format($order->subtotal) }}</span>
            </div>
            @if(!$order->isRestaurantOrder())
            <div class="flex justify-between items-center">
                <span class="text-gray-600 dark:text-gray-400">Delivery Fee</span>
                <span class="font-semibold text-gray-900 dark:text-white">₦{{ number_format($order->delivery_fee) }}</span>
            </div>
            @endif
            <div class="border-t border-gray-200 dark:border-gray-700 pt-2">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-900 dark:text-white">Total</span>
                    <span class="text-lg font-bold text-orange-500 dark:text-orange-400">₦{{ number_format($order->total_amount) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Next Steps -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-3">What's Next?</h3>
        <div class="space-y-2 text-blue-700 dark:text-blue-300">
            <p class="dark:text-white">• We'll confirm your order within 10 minutes</p>
            <p class="dark:text-white">• You'll receive updates via WhatsApp</p>
            @if($order->isRestaurantOrder())
                <p class="dark:text-white">• Estimated preparation time: 15-25 minutes</p>
                <p class="dark:text-white">• Payment: {{ $order->payment_method_display }}</p>
            @else
                <p class="dark:text-white">• Estimated delivery time: 30-45 minutes</p>
                <p class="dark:text-white">• Payment: {{ $order->payment_method_display }}</p>
            @endif
            @if($order->payment_method === 'cash' && $order->payment_status === 'pending')
                <p class="dark:text-white">• Payment will be collected upon delivery</p>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="space-y-3 mb-20">
        <a href="https://wa.me/?text=Hi! I just placed an order ({{ $order->order_number }}). Can you confirm it?" 
           target="_blank"
           class="w-full bg-green-500 text-white py-3 rounded-lg font-semibold text-center hover:bg-green-600 transition flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
            </svg>
            Contact on WhatsApp
        </a>
        
        <a href="/menu" 
           class="w-full bg-orange-500 text-white py-3 rounded-lg font-semibold text-center hover:bg-orange-600 transition">
            Order More Food
        </a>
    </div>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-lg z-50 flex justify-around items-center py-1 px-2 max-w-md mx-auto w-full">
        <a href="/menu" class="flex flex-col items-center text-orange-500 dark:text-orange-300">
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
            <span id="cartCountBottom" class="absolute -top-1 -right-1 bg-yellow-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
            <span class="text-xs mt-0.5">Cart</span>
        </a>
        <a href="{{ route('user.orders') }}" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
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
        <a href="{{ route('login') }}" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
            <!-- Login Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 20v-1a4 4 0 014-4h8a4 4 0 014 4v1" />
            </svg>
            <span class="text-xs mt-0.5">Login</span>
        </a>
    </nav>

    <!-- Dark Mode Toggle Script -->
    <script>
        const darkModeToggle = document.getElementById('darkModeToggle');
        const html = document.documentElement;
        
        const currentTheme = localStorage.getItem('theme') || 'light';
        html.classList.toggle('dark', currentTheme === 'dark');
        updateToggleIcon();
        
        darkModeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            const theme = html.classList.contains('dark') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
            updateToggleIcon();
        });
        
        function updateToggleIcon() {
            const isDark = html.classList.contains('dark');
            darkModeToggle.innerHTML = isDark 
                ? '<i class="fas fa-moon text-yellow-400"></i>' 
                : '<i class="fas fa-sun text-gray-600"></i>';
        }

        // Update cart count (should be 0 after successful order)
        function updateCartCount() {
            const cartCount = document.getElementById('cartCountBottom');
            if (cartCount) {
                cartCount.classList.add('hidden');
            }
        }

        // Countdown Timer and Progress Bar
        function initializeCountdown() {
            const countdownElement = document.getElementById('countdown');
            const progressBar = document.getElementById('progressBar');
            
            if (!countdownElement) return;
            
            // Get order creation time from the page
            const orderCreatedAt = new Date('{{ $order->created_at }}').getTime();
            const now = new Date().getTime();
            
            // Calculate total estimated delivery time based on status
            let totalEstimatedMinutes = 45; // Default 45 minutes
            let elapsedMinutes = Math.floor((now - orderCreatedAt) / (1000 * 60));
            
            // Adjust based on current status
            const status = '{{ $order->status }}';
            let remainingMinutes = 0;
            
            switch(status) {
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
                case 'delivered':
                    remainingMinutes = 0;
                    break;
                default:
                    remainingMinutes = Math.max(0, totalEstimatedMinutes - elapsedMinutes);
            }
            
            // Update countdown display
            function updateCountdown() {
                if (remainingMinutes <= 0) {
                    countdownElement.textContent = '00:00';
                    countdownElement.classList.add('text-green-600', 'dark:text-green-400');
                    countdownElement.classList.remove('text-orange-600', 'dark:text-orange-400');
                    return;
                }
                
                const hours = Math.floor(remainingMinutes / 60);
                const minutes = remainingMinutes % 60;
                const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
                
                countdownElement.textContent = timeString;
                
                // Change color when less than 10 minutes remaining
                if (remainingMinutes <= 10) {
                    countdownElement.classList.add('text-red-600', 'dark:text-red-400');
                    countdownElement.classList.remove('text-orange-600', 'dark:text-orange-400');
                }
                
                remainingMinutes--;
            }
            
            // Initial update
            updateCountdown();
            
            // Update every minute
            setInterval(updateCountdown, 60000);
            
            // Animate progress bar
            if (progressBar) {
                setTimeout(() => {
                    progressBar.style.transition = 'width 2s ease-out';
                }, 500);
            }
        }

        // Auto-refresh order status every 30 seconds
        function setupAutoRefresh() {
            const status = '{{ $order->status }}';
            if (status !== 'delivered' && status !== 'cancelled') {
                setTimeout(() => {
                    window.location.reload();
                }, 30000);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            initializeCountdown();
            setupAutoRefresh();
            initializeVideoControls();
        });
        
        // Video Controls Functionality
        function initializeVideoControls() {
            const youtubeVideo = document.getElementById('youtubeVideo');
            const localVideo = document.getElementById('localVideo');
            const videoPlaceholder = document.getElementById('videoPlaceholder');
            const toggleBtn = document.getElementById('toggleVideo');
            const switchBtn = document.getElementById('switchVideo');
            
            if (!youtubeVideo || !localVideo || !videoPlaceholder || !toggleBtn || !switchBtn) {
                return; // Elements not found
            }
            
            let currentVideoType = 'youtube'; // 'youtube' or 'local'
            let isPlaying = false;
            
            // YouTube Shorts URLs (you can replace these with actual kitchen videos)
            const youtubeShorts = [
                'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=0&mute=1&controls=1&rel=0&modestbranding=1',
                'https://www.youtube.com/embed/jNQXAC9IVRw?autoplay=0&mute=1&controls=1&rel=0&modestbranding=1',
                'https://www.youtube.com/embed/kJQP7kiw5Fk?autoplay=0&mute=1&controls=1&rel=0&modestbranding=1'
            ];
            
            let currentYoutubeIndex = 0;
            
            // Toggle video play/pause
            toggleBtn.addEventListener('click', function() {
                if (currentVideoType === 'youtube') {
                    // For YouTube, we can't directly control play/pause via iframe
                    // So we'll show a message or switch to local video
                    showNotification('YouTube video controls are limited. Try switching to local video.');
                } else {
                    if (isPlaying) {
                        localVideo.pause();
                        toggleBtn.innerHTML = '<i class="fas fa-play"></i>';
                    } else {
                        localVideo.play();
                        toggleBtn.innerHTML = '<i class="fas fa-pause"></i>';
                    }
                    isPlaying = !isPlaying;
                }
            });
            
            // Switch between video types
            switchBtn.addEventListener('click', function() {
                if (currentVideoType === 'youtube') {
                    // Switch to local video
                    youtubeVideo.classList.add('hidden');
                    localVideo.classList.remove('hidden');
                    videoPlaceholder.classList.add('hidden');
                    currentVideoType = 'local';
                    switchBtn.innerHTML = '<i class="fas fa-youtube"></i>';
                    toggleBtn.innerHTML = '<i class="fas fa-play"></i>';
                } else {
                    // Switch to YouTube
                    localVideo.classList.add('hidden');
                    youtubeVideo.classList.remove('hidden');
                    videoPlaceholder.classList.add('hidden');
                    currentVideoType = 'youtube';
                    switchBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
                    toggleBtn.innerHTML = '<i class="fas fa-play"></i>';
                    
                    // Cycle through different YouTube videos
                    currentYoutubeIndex = (currentYoutubeIndex + 1) % youtubeShorts.length;
                    const iframe = youtubeVideo.querySelector('iframe');
                    iframe.src = youtubeShorts[currentYoutubeIndex];
                }
            });
            
            // Handle video loading errors
            localVideo.addEventListener('error', function() {
                localVideo.classList.add('hidden');
                videoPlaceholder.classList.remove('hidden');
                showNotification('Local video not available. Using placeholder.');
            });
            
            // Handle video play/pause events
            localVideo.addEventListener('play', function() {
                isPlaying = true;
                toggleBtn.innerHTML = '<i class="fas fa-pause"></i>';
            });
            
            localVideo.addEventListener('pause', function() {
                isPlaying = false;
                toggleBtn.innerHTML = '<i class="fas fa-play"></i>';
            });
            
            // Show notification
            function showNotification(message) {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg z-50 text-sm';
                notification.textContent = message;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
            
            // Initialize with YouTube video
            videoPlaceholder.classList.add('hidden');
        }

        // Copy tracking code function
        function copyTrackingCode() {
            const trackingCode = '{{ $order->tracking_code ?? "" }}';
            if (!trackingCode) return;
            
            navigator.clipboard.writeText(trackingCode).then(function() {
                // Show success message
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check mr-1"></i>Copied!';
                button.classList.add('bg-green-600', 'hover:bg-green-700', 'text-white');
                button.classList.remove('bg-white', 'hover:bg-blue-50', 'text-blue-700');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('bg-green-600', 'hover:bg-green-700', 'text-white');
                    button.classList.add('bg-white', 'hover:bg-blue-50', 'text-blue-700');
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
</div>
@endsection
