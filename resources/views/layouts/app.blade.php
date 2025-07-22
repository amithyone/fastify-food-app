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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full">
    <div class="min-h-full bg-gray-50 dark:bg-gray-900">
        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- PWA Install Prompt -->
    <div id="pwaInstallPrompt" class="fixed bottom-4 left-4 right-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 border border-gray-200 dark:border-gray-700 z-50 hidden">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Install {{ \App\Helpers\PWAHelper::getRestaurantShortName() }}</h3>
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
            appName: "{{ \App\Helpers\PWAHelper::getRestaurantName() }}",
            shortName: "{{ \App\Helpers\PWAHelper::getRestaurantShortName() }}",
            themeColor: "{{ \App\Helpers\PWAHelper::getThemeColor() }}",
            features: {
                wallet: {{ \App\Helpers\PWAHelper::isFeatureEnabled('wallet') ? 'true' : 'false' }},
                rewards: {{ \App\Helpers\PWAHelper::isFeatureEnabled('rewards') ? 'true' : 'false' }},
                qrOrdering: {{ \App\Helpers\PWAHelper::isFeatureEnabled('qr_ordering') ? 'true' : 'false' }},
                pushNotifications: {{ \App\Helpers\PWAHelper::isFeatureEnabled('push_notifications') ? 'true' : 'false' }},
                offlineMode: {{ \App\Helpers\PWAHelper::isFeatureEnabled('offline_mode') ? 'true' : 'false' }},
                darkMode: {{ \App\Helpers\PWAHelper::isFeatureEnabled('dark_mode') ? 'true' : 'false' }},
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

        console.log('PWA Install Prompt elements:', {
            prompt: pwaInstallPrompt,
            installBtn: pwaInstallBtn,
            dismissBtn: pwaDismissBtn
        });

        // Check if app is already installed
        function isAppInstalled() {
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
                   window.navigator.standalone === true;
            console.log('Is app installed:', isStandalone);
            return isStandalone;
        }

        // Show install prompt
        function showInstallPrompt() {
            console.log('showInstallPrompt called');
            console.log('deferredPrompt:', deferredPrompt);
            console.log('isAppInstalled:', isAppInstalled());
            
            if (!isAppInstalled() && deferredPrompt) {
                console.log('Showing install prompt');
                pwaInstallPrompt.classList.remove('hidden');
                
                // Auto-hide after 15 seconds
                setTimeout(() => {
                    hideInstallPrompt();
                }, 15000);
            } else {
                console.log('Not showing prompt - app installed or no deferred prompt');
            }
        }

        // Hide install prompt
        function hideInstallPrompt() {
            console.log('hideInstallPrompt called');
            pwaInstallPrompt.classList.add('hidden');
            deferredPrompt = null;
        }

        // Listen for beforeinstallprompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('beforeinstallprompt event fired');
            e.preventDefault();
            deferredPrompt = e;
            
            // Show prompt after a delay
            setTimeout(showInstallPrompt, 2000);
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
                } else {
                    console.log('No deferred prompt available');
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
            // You can add specific behavior for installed app
        } else {
            console.log('App not installed - showing install prompt after delay');
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
    </script>

    @stack('scripts')
</body>
</html>
