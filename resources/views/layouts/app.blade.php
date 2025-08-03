<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="{{ \App\Helpers\PWAHelper::getThemeColor() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ \App\Helpers\PWAHelper::getRestaurantShortName() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="{{ \App\Helpers\PWAHelper::getRestaurantShortName() }}">
    <meta name="msapplication-TileColor" content="{{ \App\Helpers\PWAHelper::getThemeColor() }}">
    <meta name="msapplication-tap-highlight" content="no">

    <!-- PWA Icons -->
    @php
        $icons = \App\Helpers\PWAHelper::getIconLinks();
    @endphp
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $icons['icon-32x32'] }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $icons['icon-16x16'] }}">
    <link rel="apple-touch-icon" href="{{ $icons['apple-touch-icon'] }}">
    <link rel="mask-icon" href="{{ $icons['mask-icon'] }}" color="{{ \App\Helpers\PWAHelper::getThemeColor() }}">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    <title>{{ \App\Helpers\PWAHelper::getAppTitle() }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom Animations -->
    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full">
    <!-- PWA Loading Screen -->
    <div id="pwaLoadingScreen" class="fixed inset-0 bg-white dark:bg-gray-900 flex items-center justify-center z-50 transition-opacity duration-500">
        <div class="text-center">
            <div class="w-24 h-24 mx-auto mb-6 relative">
                <img src="{{ asset('favicon.png') }}" alt="Fastify" class="w-full h-full object-contain animate-pulse" style="animation: spin 3s linear infinite;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="absolute inset-0 bg-orange-500 rounded-full opacity-20 animate-ping" style="display: none;">
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="text-white text-2xl font-bold">F</span>
                    </div>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Fastify</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Loading your food experience...</p>
            <div class="flex justify-center space-x-1">
                <div class="w-2 h-2 bg-orange-500 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>

    <div class="min-h-full bg-gray-50 dark:bg-gray-900">
        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Online Status Bar -->
        <div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
                <div class="flex justify-end">
                    <span id="onlineStatus" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        🌐 Online
                    </span>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- PWA Install Prompt -->
    <div id="pwaInstallPrompt" class="fixed bottom-4 left-4 right-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 border border-gray-200 dark:border-gray-700 z-50 hidden max-w-md mx-auto">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center overflow-hidden">
                    <img src="/favicon.png" alt="Fastify" class="w-full h-full object-cover">
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Install Fastify</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Add to home screen for quick access</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <button id="pwaInstallBtn" class="bg-orange-500 text-white px-3 py-1 rounded text-sm font-medium hover:bg-orange-600 transition-colors">
                    Install
                </button>
                <button id="pwaDismissBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- PWA Configuration -->
    <script>
        // PWA Configuration
        window.PWAConfig = {
            appName: "Fastify",
            shortName: "Fastify",
            themeColor: "#f97316",
            logo: "/favicon.png",
            features: {
                wallet: true,
                rewards: true,
                qrOrdering: true,
                pushNotifications: true,
                offlineMode: true,
                darkMode: true,
            }
        };
    </script>

    <!-- PWA Service Worker Registration -->
    <script>
        // Register service worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('Service Worker registered successfully:', registration);
                    })
                    .catch(error => {
                        console.error('Service Worker registration failed:', error);
                    });
            });
        }

        // PWA Install Prompt
        let deferredPrompt;
        const pwaInstallPrompt = document.getElementById('pwaInstallPrompt');
        const pwaInstallBtn = document.getElementById('pwaInstallBtn');
        const pwaDismissBtn = document.getElementById('pwaDismissBtn');

        // Check if app is already installed
        function isAppInstalled() {
            return window.matchMedia('(display-mode: standalone)').matches ||
                   window.navigator.standalone === true;
        }

        // Show install prompt
        function showInstallPrompt() {
            if (!isAppInstalled() && deferredPrompt && pwaInstallPrompt) {
                console.log('Showing PWA install prompt');
                pwaInstallPrompt.classList.remove('hidden');
                
                // Auto-hide after 10 seconds
                setTimeout(() => {
                    hideInstallPrompt();
                }, 10000);
            }
        }

        // Hide install prompt
        function hideInstallPrompt() {
            if (pwaInstallPrompt) {
                pwaInstallPrompt.classList.add('hidden');
            }
            deferredPrompt = null;
        }

        // Listen for beforeinstallprompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('beforeinstallprompt event fired');
            e.preventDefault();
            deferredPrompt = e;
            
            // Show prompt after a delay
            setTimeout(showInstallPrompt, 3000);
        });

        // Handle install button click
        if (pwaInstallBtn) {
            pwaInstallBtn.addEventListener('click', async () => {
                console.log('Install button clicked');
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log('User choice:', outcome);
                    hideInstallPrompt();
                }
            });
        }

        // Handle dismiss button click
        if (pwaDismissBtn) {
            pwaDismissBtn.addEventListener('click', hideInstallPrompt);
        }

        // Listen for app installed event
        window.addEventListener('appinstalled', () => {
            console.log('PWA was installed');
            hideInstallPrompt();
        });

        // Check if app is launched from installed PWA
        if (isAppInstalled()) {
            console.log('App launched from installed PWA');
        } else {
            console.log('App not installed');
            // Show install prompt after 5 seconds if not installed
            setTimeout(() => {
                if (!isAppInstalled() && deferredPrompt) {
                    showInstallPrompt();
                }
            }, 5000);
        }

        // Offline/Online detection
        window.addEventListener('online', () => {
            console.log('App is online');
            // You can show online status or sync data
        });

        window.addEventListener('offline', () => {
            console.log('App is offline');
            // You can show offline status or cached content
        });

        // Background sync for offline orders
        if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
            navigator.serviceWorker.ready.then(registration => {
                // Register background sync for offline orders
                registration.sync.register('background-sync-orders')
                    .then(() => {
                        console.log('Background sync registered');
                    })
                    .catch(error => {
                        console.error('Background sync registration failed:', error);
                    });
            });
        }

        // Push notifications
        if ('serviceWorker' in navigator && 'PushManager' in window && window.PWAConfig.features.pushNotifications) {
            // Request notification permission
            function requestNotificationPermission() {
                return new Promise((resolve, reject) => {
                    const permissionResult = Notification.requestPermission(result => {
                        resolve(result);
                    });
                    
                    if (permissionResult) {
                        permissionResult.then(resolve, reject);
                    }
                });
            }

            // Subscribe to push notifications
            async function subscribeToPushNotifications() {
                try {
                    const permission = await requestNotificationPermission();
                    if (permission === 'granted') {
                        const registration = await navigator.serviceWorker.ready;
                        const subscription = await registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: 'YOUR_VAPID_PUBLIC_KEY' // Replace with your VAPID key
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
                    }
                } catch (error) {
                    console.error('Push notification subscription failed:', error);
                }
            }

            // Auto-subscribe if user is logged in
            if (document.body.classList.contains('user-logged-in')) {
                subscribeToPushNotifications();
            }
        }

        // PWA Loading Screen Management
        document.addEventListener('DOMContentLoaded', function() {
            const loadingScreen = document.getElementById('pwaLoadingScreen');
            
            // Hide loading screen after page is fully loaded
            window.addEventListener('load', function() {
                setTimeout(() => {
                    if (loadingScreen) {
                        loadingScreen.style.opacity = '0';
                        setTimeout(() => {
                            loadingScreen.style.display = 'none';
                        }, 500);
                    }
                }, 1000); // Show loading screen for at least 1 second
            });

            // Hide loading screen immediately if page is already loaded
            if (document.readyState === 'complete') {
                setTimeout(() => {
                    if (loadingScreen) {
                        loadingScreen.style.opacity = '0';
                        setTimeout(() => {
                            loadingScreen.style.display = 'none';
                        }, 500);
                    }
                }, 1000);
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
