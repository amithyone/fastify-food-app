const CACHE_NAME = 'fastify-v1.0.0';
const STATIC_CACHE = 'fastify-static-v1.0.0';
const DYNAMIC_CACHE = 'fastify-dynamic-v1.0.0';

// Files to cache immediately
const STATIC_FILES = [
    '/',
    '/manifest.json',
    '/favicon.png',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/offline.html'
];

// API routes to cache
const API_ROUTES = [
    '/api/menu',
    '/api/categories',
    '/api/orders',
    '/api/wallet'
];

// Install event - cache static files
self.addEventListener('install', (event) => {
    console.log('Service Worker: Installing...');
    
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => {
                console.log('Service Worker: Caching static files');
                return cache.addAll(STATIC_FILES);
            })
            .then(() => {
                console.log('Service Worker: Static files cached');
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('Service Worker: Error caching static files', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activating...');
    
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
                            console.log('Service Worker: Deleting old cache', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('Service Worker: Activated');
                return self.clients.claim();
            })
    );
});

// Fetch event - handle network requests
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // Handle API requests
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(handleApiRequest(request));
        return;
    }
    
    // Handle static assets
    if (isStaticAsset(request)) {
        event.respondWith(handleStaticAsset(request));
        return;
    }
    
    // Handle navigation requests
    if (request.mode === 'navigate') {
        event.respondWith(handleNavigation(request));
        return;
    }
    
    // Default: try network first, fallback to cache
    event.respondWith(
        (async () => {
            // Check if the request URL is cacheable
            const url = new URL(request.url);
            if (url.protocol === 'chrome-extension:' || url.protocol === 'moz-extension:' || url.protocol === 'safari-extension:') {
                // Skip caching for browser extensions
                return fetch(request);
            }
            
            try {
                const response = await fetch(request);
                // Cache successful responses
                if (response.status === 200) {
                    const responseClone = response.clone();
                    const cache = await caches.open(DYNAMIC_CACHE);
                    cache.put(request, responseClone);
                }
                return response;
            } catch (error) {
                // Fallback to cache
                return caches.match(request);
            }
        })()
    );
});

// Handle API requests with cache-first strategy
async function handleApiRequest(request) {
    // Check if the request URL is cacheable
    const url = new URL(request.url);
    if (url.protocol === 'chrome-extension:' || url.protocol === 'moz-extension:' || url.protocol === 'safari-extension:') {
        // Skip caching for browser extensions
        return fetch(request);
    }
    
    try {
        // Try cache first
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            // Return cached response but update in background
            fetchAndCache(request);
            return cachedResponse;
        }
        
        // If not in cache, fetch from network
        const response = await fetch(request);
        if (response.status === 200) {
            const responseClone = response.clone();
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, responseClone);
        }
        return response;
    } catch (error) {
        console.error('Service Worker: API request failed', error);
        // Return cached response if available
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Return offline response
        return new Response(
            JSON.stringify({ 
                error: 'You are offline. Please check your connection.',
                offline: true 
            }),
            {
                status: 503,
                statusText: 'Service Unavailable',
                headers: { 'Content-Type': 'application/json' }
            }
        );
    }
}

// Handle static assets with cache-first strategy
async function handleStaticAsset(request) {
    // Check if the request URL is cacheable
    const url = new URL(request.url);
    if (url.protocol === 'chrome-extension:' || url.protocol === 'moz-extension:' || url.protocol === 'safari-extension:') {
        // Skip caching for browser extensions
        return fetch(request);
    }
    
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }
    
    try {
        const response = await fetch(request);
        if (response.status === 200) {
            const responseClone = response.clone();
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, responseClone);
        }
        return response;
    } catch (error) {
        console.error('Service Worker: Static asset fetch failed', error);
        return new Response('Asset not available offline', { status: 404 });
    }
}

// Handle navigation requests
async function handleNavigation(request) {
    try {
        const response = await fetch(request);
        if (response.status === 200) {
            const responseClone = response.clone();
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, responseClone);
        }
        return response;
    } catch (error) {
        console.error('Service Worker: Navigation failed', error);
        // Return offline page
        const offlineResponse = await caches.match('/offline.html');
        if (offlineResponse) {
            return offlineResponse;
        }
        
        // Fallback offline page
        return new Response(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Fastify - Offline</title>
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        text-align: center; 
                        padding: 50px 20px;
                        background: #f5f5f5;
                    }
                    .offline-container {
                        max-width: 400px;
                        margin: 0 auto;
                        background: white;
                        padding: 40px 20px;
                        border-radius: 10px;
                        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    }
                    .offline-icon {
                        font-size: 64px;
                        margin-bottom: 20px;
                    }
                    h1 { color: #f97316; margin-bottom: 10px; }
                    p { color: #666; line-height: 1.6; }
                    .retry-btn {
                        background: #f97316;
                        color: white;
                        border: none;
                        padding: 12px 24px;
                        border-radius: 6px;
                        cursor: pointer;
                        margin-top: 20px;
                    }
                </style>
            </head>
            <body>
                <div class="offline-container">
                    <div class="offline-icon">📱</div>
                    <h1>You're Offline</h1>
                    <p>Please check your internet connection and try again.</p>
                    <p>Some features may still work if you've used them before.</p>
                    <button class="retry-btn" onclick="window.location.reload()">
                        Try Again
                    </button>
                </div>
            </body>
            </html>
        `, {
            status: 200,
            statusText: 'OK',
            headers: { 'Content-Type': 'text/html' }
        });
    }
}

// Background fetch and cache
async function fetchAndCache(request) {
    // Check if the request URL is cacheable
    const url = new URL(request.url);
    if (url.protocol === 'chrome-extension:' || url.protocol === 'moz-extension:' || url.protocol === 'safari-extension:') {
        // Skip caching for browser extensions
        return;
    }
    
    try {
        const response = await fetch(request);
        if (response.status === 200) {
            const responseClone = response.clone();
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, responseClone);
        }
    } catch (error) {
        console.error('Service Worker: Background fetch failed', error);
    }
}

// Check if request is for static asset
function isStaticAsset(request) {
    const url = new URL(request.url);
    return (
        url.pathname.startsWith('/css/') ||
        url.pathname.startsWith('/js/') ||
        url.pathname.startsWith('/icons/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.jpeg') ||
        url.pathname.endsWith('.gif') ||
        url.pathname.endsWith('.svg') ||
        url.pathname.endsWith('.woff') ||
        url.pathname.endsWith('.woff2') ||
        url.pathname.endsWith('.ttf')
    );
}

// Background sync for offline orders
self.addEventListener('sync', (event) => {
    if (event.tag === 'background-sync-orders') {
        event.waitUntil(syncOrders());
    }
});

// Sync offline orders when connection is restored
async function syncOrders() {
    try {
        const offlineOrders = await getOfflineOrders();
        for (const order of offlineOrders) {
            await submitOrder(order);
            await removeOfflineOrder(order.id);
        }
    } catch (error) {
        console.error('Service Worker: Background sync failed', error);
    }
}

// Get offline orders from IndexedDB
async function getOfflineOrders() {
    // Implementation would depend on your IndexedDB setup
    return [];
}

// Submit order to server
async function submitOrder(order) {
    const response = await fetch('/orders', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': order.csrf_token
        },
        body: JSON.stringify(order)
    });
    return response.json();
}

// Remove offline order after successful sync
async function removeOfflineOrder(orderId) {
    // Implementation would depend on your IndexedDB setup
}

// Push notifications
self.addEventListener('push', (event) => {
    if (event.data) {
        const data = event.data.json();
        const options = {
            body: data.body,
            icon: '/icons/icon-192x192.png',
            badge: '/icons/icon-72x72.png',
            vibrate: [100, 50, 100],
            data: {
                url: data.url
            },
            actions: [
                {
                    action: 'view',
                    title: 'View Order',
                    icon: '/icons/icon-72x72.png'
                },
                {
                    action: 'close',
                    title: 'Close',
                    icon: '/icons/icon-72x72.png'
                }
            ]
        };
        
        event.waitUntil(
            self.registration.showNotification(data.title, options)
        );
    }
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    
    if (event.action === 'view') {
        event.waitUntil(
            clients.openWindow(event.notification.data.url)
        );
    }
});

console.log('Service Worker: Loaded successfully'); 