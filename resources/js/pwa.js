// PWA Utilities for Abuja Eat

class PWAUtils {
    constructor() {
        this.isOnline = navigator.onLine;
        this.offlineOrders = [];
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadOfflineOrders();
        this.checkInstallation();
    }

    setupEventListeners() {
        // Online/Offline detection
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.handleOnline();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.handleOffline();
        });

        // Service Worker events
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.addEventListener('message', (event) => {
                this.handleServiceWorkerMessage(event.data);
            });
        }
    }

    // Handle when app comes online
    handleOnline() {
        console.log('App is online - syncing data...');
        
        // Sync offline orders
        this.syncOfflineOrders();
        
        // Update UI
        this.updateOnlineStatus(true);
        
        // Show online notification
        this.showNotification('You are back online!', 'success');
    }

    // Handle when app goes offline
    handleOffline() {
        console.log('App is offline - enabling offline mode...');
        
        // Update UI
        this.updateOnlineStatus(false);
        
        // Show offline notification
        this.showNotification('You are offline. Some features may be limited.', 'warning');
    }

    // Update online status in UI
    updateOnlineStatus(isOnline) {
        const statusIndicator = document.getElementById('onlineStatus');
        if (statusIndicator) {
            statusIndicator.textContent = isOnline ? '🌐 Online' : '📡 Offline';
            statusIndicator.className = isOnline ? 'online' : 'offline';
        }
    }

    // Save order for offline sync
    saveOfflineOrder(orderData) {
        const order = {
            id: Date.now(),
            data: orderData,
            timestamp: new Date().toISOString(),
            synced: false
        };

        this.offlineOrders.push(order);
        this.saveOfflineOrders();
        
        console.log('Order saved for offline sync:', order);
    }

    // Sync offline orders when back online
    async syncOfflineOrders() {
        const unsyncedOrders = this.offlineOrders.filter(order => !order.synced);
        
        for (const order of unsyncedOrders) {
            try {
                await this.submitOrder(order.data);
                order.synced = true;
                console.log('Offline order synced:', order.id);
            } catch (error) {
                console.error('Failed to sync offline order:', order.id, error);
            }
        }

        this.saveOfflineOrders();
    }

    // Submit order to server
    async submitOrder(orderData) {
        const response = await fetch('/orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(orderData)
        });

        if (!response.ok) {
            throw new Error('Failed to submit order');
        }

        return response.json();
    }

    // Load offline orders from localStorage
    loadOfflineOrders() {
        try {
            const stored = localStorage.getItem('offlineOrders');
            this.offlineOrders = stored ? JSON.parse(stored) : [];
        } catch (error) {
            console.error('Failed to load offline orders:', error);
            this.offlineOrders = [];
        }
    }

    // Save offline orders to localStorage
    saveOfflineOrders() {
        try {
            localStorage.setItem('offlineOrders', JSON.stringify(this.offlineOrders));
        } catch (error) {
            console.error('Failed to save offline orders:', error);
        }
    }

    // Handle service worker messages
    handleServiceWorkerMessage(data) {
        switch (data.type) {
            case 'ORDER_UPDATE':
                this.handleOrderUpdate(data.order);
                break;
            case 'PUSH_NOTIFICATION':
                this.handlePushNotification(data.notification);
                break;
            case 'SYNC_COMPLETE':
                this.handleSyncComplete(data);
                break;
            default:
                console.log('Unknown service worker message:', data);
        }
    }

    // Handle order status updates
    handleOrderUpdate(order) {
        // Update order status in UI
        const orderElement = document.querySelector(`[data-order-id="${order.id}"]`);
        if (orderElement) {
            this.updateOrderStatus(orderElement, order.status);
        }

        // Show notification
        this.showNotification(`Order #${order.id} status: ${order.status}`, 'info');
    }

    // Handle push notifications
    handlePushNotification(notification) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(notification.title, {
                body: notification.body,
                icon: '/icons/icon-192x192.png',
                badge: '/icons/icon-72x72.png',
                data: notification.data
            });
        }
    }

    // Handle sync completion
    handleSyncComplete(data) {
        console.log('Background sync completed:', data);
        this.showNotification('Data synced successfully!', 'success');
    }

    // Update order status in UI
    updateOrderStatus(orderElement, status) {
        const statusElement = orderElement.querySelector('.order-status');
        if (statusElement) {
            statusElement.textContent = status;
            statusElement.className = `order-status status-${status.toLowerCase()}`;
        }
    }

    // Show custom notification
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <span class="text-sm font-medium">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    // Check if app is installed
    checkInstallation() {
        const isInstalled = window.matchMedia('(display-mode: standalone)').matches ||
                           window.navigator.standalone === true;
        
        if (isInstalled) {
            document.body.classList.add('pwa-installed');
            console.log('App is running in installed mode');
        }
    }

    // Request notification permission
    async requestNotificationPermission() {
        if (!('Notification' in window)) {
            console.log('This browser does not support notifications');
            return false;
        }

        if (Notification.permission === 'granted') {
            return true;
        }

        if (Notification.permission === 'denied') {
            console.log('Notification permission denied');
            return false;
        }

        const permission = await Notification.requestPermission();
        return permission === 'granted';
    }

    // Subscribe to push notifications
    async subscribeToPushNotifications() {
        try {
            const permission = await this.requestNotificationPermission();
            if (!permission) {
                return false;
            }

            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array('YOUR_VAPID_PUBLIC_KEY') // Replace with your VAPID key
            });

            // Send subscription to server
            await fetch('/api/push-subscription', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(subscription)
            });

            console.log('Push notification subscription successful');
            return true;
        } catch (error) {
            console.error('Push notification subscription failed:', error);
            return false;
        }
    }

    // Convert VAPID key to Uint8Array
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    // Cache API data for offline use
    async cacheApiData(endpoint, data) {
        try {
            const cache = await caches.open('api-cache-v1');
            const response = new Response(JSON.stringify(data), {
                headers: { 'Content-Type': 'application/json' }
            });
            await cache.put(endpoint, response);
            console.log('API data cached:', endpoint);
        } catch (error) {
            console.error('Failed to cache API data:', error);
        }
    }

    // Get cached API data
    async getCachedApiData(endpoint) {
        try {
            const cache = await caches.open('api-cache-v1');
            const response = await cache.match(endpoint);
            if (response) {
                return await response.json();
            }
        } catch (error) {
            console.error('Failed to get cached API data:', error);
        }
        return null;
    }

    // Clear old cache data
    async clearOldCache() {
        try {
            const cacheNames = await caches.keys();
            const oldCaches = cacheNames.filter(name => 
                name.startsWith('api-cache-') && name !== 'api-cache-v1'
            );
            
            await Promise.all(oldCaches.map(name => caches.delete(name)));
            console.log('Old cache cleared');
        } catch (error) {
            console.error('Failed to clear old cache:', error);
        }
    }
}

// Initialize PWA utilities
const pwaUtils = new PWAUtils();

// Export for use in other modules
window.PWAUtils = pwaUtils;

// Auto-subscribe to push notifications for logged-in users
if (document.body.classList.contains('user-logged-in')) {
    pwaUtils.subscribeToPushNotifications();
}

// Clear old cache on app load
pwaUtils.clearOldCache(); 