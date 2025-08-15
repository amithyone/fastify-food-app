<div>
    <div class="relative" x-data="{ open: @entangle('showNotifications') }">
        <!-- Notification Bell -->
        <button 
            wire:click="toggleNotifications"
            class="relative p-2 text-gray-600 hover:text-orange-500 transition-colors duration-200"
            x-on:click.away="open = false"
        >
            <i class="fas fa-bell text-xl"></i>
            
            <!-- Notification Badge -->
            @if($unreadCount > 0)
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </button>

            <!-- Notifications Dropdown -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50"
        style="display: none;"
    >
            <!-- Header -->
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Order Notifications
                    </h3>
                    @if($unreadCount > 0)
                        <button 
                            wire:click="markAllAsRead"
                            class="text-sm text-orange-500 hover:text-orange-600 transition-colors"
                        >
                            Mark all as read
                        </button>
                    @endif
                </div>
            </div>

            <!-- Notifications List -->
            <div class="max-h-96 overflow-y-auto">
                @if(count($newOrders) > 0)
                    @foreach($newOrders as $order)
                        <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border-b border-gray-100 dark:border-gray-600 last:border-b-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            #{{ $order['order_number'] }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($order['status'] === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @elseif($order['status'] === 'confirmed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($order['status'] === 'preparing') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                            @elseif($order['status'] === 'ready') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                            {{ ucfirst($order['status']) }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                                        {{ $order['customer_name'] }} • {{ $order['items_count'] }} items
                                    </p>
                                    
                                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <span>{{ $order['delivery_method'] }}</span>
                                        <span>{{ $order['created_at'] }}</span>
                                    </div>
                                    
                                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                                        ₦{{ number_format($order['total'], 2) }}
                                    </p>
                                </div>
                                
                                <div class="flex items-center space-x-2 ml-3">
                                    <button 
                                        wire:click="viewOrder({{ $order['id'] }})"
                                        class="text-orange-500 hover:text-orange-600 transition-colors"
                                        title="View Order"
                                    >
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    <button 
                                        wire:click="markAsRead({{ $order['id'] }})"
                                        class="text-gray-400 hover:text-gray-600 transition-colors"
                                        title="Mark as Read"
                                    >
                                        <i class="fas fa-check text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="px-4 py-8 text-center">
                        <i class="fas fa-bell-slash text-3xl text-gray-400 mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400">No new orders</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500">You're all caught up!</p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            @if(count($newOrders) > 0)
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    <a 
                        href="{{ route('restaurant.orders', $restaurant->slug ?? '') }}"
                        class="block text-center text-sm text-orange-500 hover:text-orange-600 transition-colors"
                    >
                        View all orders
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Notification Sound -->
    <audio id="notificationSound" preload="auto">
        <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
    </audio>

    <!-- JavaScript for Real-time Updates -->
    <script>
    document.addEventListener('livewire:init', () => {
        // Listen for new order notifications
        Livewire.on('newOrderNotification', (data) => {
            // Play notification sound
            const audio = document.getElementById('notificationSound');
            if (audio) {
                audio.play().catch(e => console.log('Audio play failed:', e));
            }
            
            // Show browser notification
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('New Order!', {
                    body: data.message,
                    icon: '/favicon.png',
                    badge: '/favicon.png'
                });
            }
            
            // Show toast notification
            showToast(data.message, 'success');
        });
        
        // Auto-refresh every 30 seconds
        setInterval(() => {
            @this.checkForNewOrders();
        }, 30000);
    });

    // Toast notification function
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full ${
            type === 'success' ? 'bg-green-500' : 'bg-blue-500'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        // Animate out and remove
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 5000);
    }
    </script>
</div>
