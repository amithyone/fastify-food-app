/**
 * Order Notification System
 * Handles real-time order notifications with sound alerts
 */

class OrderNotificationSystem {
    constructor() {
        this.lastOrderCount = 0;
        this.lastOrderId = null;
        this.notificationSound = null;
        this.isEnabled = true;
        this.checkInterval = 10000; // Check every 10 seconds
        this.intervalId = null;
        this.restaurantId = null;
        this.notificationContainer = null;
        
        this.init();
    }

    init() {
        // Get restaurant ID from page
        this.restaurantId = this.getRestaurantId();
        
        // Initialize notification sound
        this.initNotificationSound();
        
        // Create notification container
        this.createNotificationContainer();
        
        // Start checking for new orders
        this.startOrderChecking();
        
        // Add notification toggle button
        this.addNotificationToggle();
        
        // Handle page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseOrderChecking();
            } else {
                this.resumeOrderChecking();
            }
        });
    }

    getRestaurantId() {
        // Try to get restaurant ID from various sources
        const restaurantId = document.querySelector('[data-restaurant-id]')?.dataset.restaurantId;
        if (restaurantId) return restaurantId;
        
        // Extract from URL if available
        const urlMatch = window.location.pathname.match(/\/restaurant\/([^\/]+)/);
        if (urlMatch) {
            // We'll need to make an API call to get the restaurant ID
            return null; // Will be set via API call
        }
        
        return null;
    }

    async fetchRestaurantId() {
        if (this.restaurantId) return this.restaurantId;
        
        try {
            const response = await fetch('/api/restaurant/current');
            const data = await response.json();
            if (data.restaurant_id) {
                this.restaurantId = data.restaurant_id;
                return this.restaurantId;
            }
        } catch (error) {
            console.log('Could not fetch restaurant ID:', error);
        }
        
        return null;
    }

    initNotificationSound() {
        try {
            // Create audio element for notification sound
            this.notificationSound = new Audio('/sounds/notification.mp3');
            this.notificationSound.preload = 'auto';
            
            // Set volume to a reasonable level
            this.notificationSound.volume = 0.7;
            
            // Handle audio loading errors
            this.notificationSound.addEventListener('error', (e) => {
                console.log('Notification sound failed to load:', e);
                // Fallback to browser notification sound
                this.notificationSound = null;
            });
            
        } catch (error) {
            console.log('Could not initialize notification sound:', error);
        }
    }

    createNotificationContainer() {
        // Create notification container if it doesn't exist
        if (!document.getElementById('order-notification-container')) {
            const container = document.createElement('div');
            container.id = 'order-notification-container';
            container.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(container);
            this.notificationContainer = container;
        } else {
            this.notificationContainer = document.getElementById('order-notification-container');
        }
    }

    addNotificationToggle() {
        // Add notification toggle button to the header
        const header = document.querySelector('.bg-white.dark\\:bg-gray-800.shadow-sm');
        if (header) {
            const toggleButton = document.createElement('button');
            toggleButton.id = 'notification-toggle';
            toggleButton.className = `inline-flex items-center px-3 py-2 border text-sm font-medium rounded-lg transition-all duration-200 ${
                this.isEnabled 
                    ? 'border-green-300 text-green-700 bg-green-50 dark:border-green-600 dark:text-green-300 dark:bg-green-900/20' 
                    : 'border-gray-300 text-gray-700 bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:bg-gray-700'
            }`;
            toggleButton.innerHTML = `
                <i class="fas ${this.isEnabled ? 'fa-bell' : 'fa-bell-slash'} mr-2"></i>
                ${this.isEnabled ? 'Notifications ON' : 'Notifications OFF'}
            `;
            
            toggleButton.addEventListener('click', () => {
                this.toggleNotifications();
            });
            
            // Insert before the "Back to Dashboard" button
            const backButton = header.querySelector('a[href*="dashboard"]');
            if (backButton) {
                backButton.parentNode.insertBefore(toggleButton, backButton);
            } else {
                header.appendChild(toggleButton);
            }
        }
    }

    toggleNotifications() {
        this.isEnabled = !this.isEnabled;
        
        // Update toggle button
        const toggleButton = document.getElementById('notification-toggle');
        if (toggleButton) {
            toggleButton.className = `inline-flex items-center px-3 py-2 border text-sm font-medium rounded-lg transition-all duration-200 ${
                this.isEnabled 
                    ? 'border-green-300 text-green-700 bg-green-50 dark:border-green-600 dark:text-green-300 dark:bg-green-900/20' 
                    : 'border-gray-300 text-gray-700 bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:bg-gray-700'
            }`;
            toggleButton.innerHTML = `
                <i class="fas ${this.isEnabled ? 'fa-bell' : 'fa-bell-slash'} mr-2"></i>
                ${this.isEnabled ? 'Notifications ON' : 'Notifications OFF'}
            `;
        }
        
        // Show status message
        this.showNotification(`Notifications ${this.isEnabled ? 'enabled' : 'disabled'}`, this.isEnabled ? 'success' : 'info');
        
        // Start/stop checking based on state
        if (this.isEnabled) {
            this.resumeOrderChecking();
        } else {
            this.pauseOrderChecking();
        }
    }

    startOrderChecking() {
        if (this.intervalId) return;
        
        // Initial check
        this.checkForNewOrders();
        
        // Set up interval
        this.intervalId = setInterval(() => {
            this.checkForNewOrders();
        }, this.checkInterval);
    }

    pauseOrderChecking() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
    }

    resumeOrderChecking() {
        if (!this.intervalId && this.isEnabled) {
            this.startOrderChecking();
        }
    }

    async checkForNewOrders() {
        if (!this.isEnabled) return;
        
        try {
            const restaurantId = await this.fetchRestaurantId();
            if (!restaurantId) return;
            
            // Fetch latest orders
            const response = await fetch(`/api/restaurant/${restaurantId}/orders/latest`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) return;
            
            const data = await response.json();
            
            if (data.orders && data.orders.length > 0) {
                const latestOrder = data.orders[0];
                
                // Check if this is a new order
                if (this.lastOrderId !== latestOrder.id) {
                    const newOrders = data.orders.filter(order => 
                        order.id !== this.lastOrderId && 
                        order.status === 'pending'
                    );
                    
                    if (newOrders.length > 0) {
                        this.handleNewOrders(newOrders);
                    }
                    
                    this.lastOrderId = latestOrder.id;
                }
                
                // Update order count
                const pendingCount = data.orders.filter(order => order.status === 'pending').length;
                if (pendingCount > this.lastOrderCount) {
                    this.updateOrderCount(pendingCount);
                }
                this.lastOrderCount = pendingCount;
            }
            
        } catch (error) {
            console.log('Error checking for new orders:', error);
        }
    }

    handleNewOrders(newOrders) {
        if (!this.isEnabled) return;
        
        // Play notification sound
        this.playNotificationSound();
        
        // Show browser notification if supported
        this.showBrowserNotification(newOrders);
        
        // Show in-page notification
        newOrders.forEach(order => {
            this.showOrderNotification(order);
        });
        
        // Update page content if on orders page
        this.updateOrdersPage(newOrders);
    }

    playNotificationSound() {
        if (this.notificationSound) {
            try {
                this.notificationSound.currentTime = 0;
                this.notificationSound.play().catch(error => {
                    console.log('Could not play notification sound:', error);
                });
            } catch (error) {
                console.log('Error playing notification sound:', error);
            }
        }
    }

    showBrowserNotification(newOrders) {
        if (!('Notification' in window) || Notification.permission !== 'granted') {
            return;
        }
        
        const order = newOrders[0]; // Show notification for the first new order
        const notification = new Notification('New Order Received!', {
            body: `Order #${order.id} - ${order.orderItems.length} items - ₦${order.total_amount}`,
            icon: '/favicon.ico',
            tag: 'new-order',
            requireInteraction: true
        });
        
        notification.onclick = () => {
            window.focus();
            notification.close();
        };
    }

    showOrderNotification(order) {
        const notification = document.createElement('div');
        notification.className = 'bg-green-500 text-white p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 translate-x-full';
        notification.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-bell text-white"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h4 class="text-sm font-medium">New Order Received!</h4>
                    <p class="text-sm mt-1">Order #${order.id}</p>
                    <p class="text-xs mt-1">${order.orderItems.length} items - ₦${order.total_amount}</p>
                    <p class="text-xs mt-1">Table: ${order.table_number || 'N/A'}</p>
                </div>
                <button class="ml-2 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        this.notificationContainer.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 left-1/2 transform -translate-x-1/2 z-50 px-4 py-2 rounded-lg shadow-lg text-white text-sm ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            'bg-blue-500'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    updateOrderCount(count) {
        // Update order count in stats cards
        const pendingCountElement = document.querySelector('[data-order-count="pending"]');
        if (pendingCountElement) {
            pendingCountElement.textContent = count;
        }
    }

    updateOrdersPage(newOrders) {
        // If we're on the orders page, refresh the orders list
        if (window.location.pathname.includes('/orders')) {
            // Add a subtle highlight to new orders
            newOrders.forEach(order => {
                const orderRow = document.querySelector(`[data-order-id="${order.id}"]`);
                if (orderRow) {
                    orderRow.classList.add('bg-green-50', 'dark:bg-green-900/20');
                    setTimeout(() => {
                        orderRow.classList.remove('bg-green-50', 'dark:bg-green-900/20');
                    }, 3000);
                }
            });
        }
    }
}

// Initialize the notification system when the page loads
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize on restaurant pages
    if (window.location.pathname.includes('/restaurant/')) {
        window.orderNotificationSystem = new OrderNotificationSystem();
    }
});

// Request notification permission when user interacts with the page
document.addEventListener('click', () => {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}, { once: true });
