<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Restaurant Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration allows you to customize the app for different
    | restaurant deployments. Change these values for each restaurant.
    |
    */

    // Basic Restaurant Information
    'name' => env('RESTAURANT_NAME', 'Fastify'),
    'display_name' => env('RESTAURANT_DISPLAY_NAME', 'Fastify - Food Ordering'),
    'short_name' => env('RESTAURANT_SHORT_NAME', 'Fastify'),
    'description' => env('RESTAURANT_DESCRIPTION', 'Order delicious food from your favorite restaurants'),
    
    // Contact Information
    'phone' => env('RESTAURANT_PHONE', '+234 XXX XXX XXXX'),
    'email' => env('RESTAURANT_EMAIL', 'support@fastify.com'),
    'address' => env('RESTAURANT_ADDRESS', 'Abuja, Nigeria'),
    
    // Branding
    'logo' => env('RESTAURANT_LOGO', '/images/logo.png'),
    'favicon' => env('RESTAURANT_FAVICON', '/favicon.svg'),
    'theme_color' => env('RESTAURANT_THEME_COLOR', '#f97316'),
    'accent_color' => env('RESTAURANT_ACCENT_COLOR', '#ea580c'),
    
    // PWA Configuration
    'pwa' => [
        'name' => env('PWA_NAME', 'Fastify - Food Ordering'),
        'short_name' => env('PWA_SHORT_NAME', 'Fastify'),
        'description' => env('PWA_DESCRIPTION', 'Order delicious food from your favorite restaurants'),
        'theme_color' => env('PWA_THEME_COLOR', '#f97316'),
        'background_color' => env('PWA_BACKGROUND_COLOR', '#ffffff'),
        'display' => env('PWA_DISPLAY', 'standalone'),
        'orientation' => env('PWA_ORIENTATION', 'portrait-primary'),
        'scope' => env('PWA_SCOPE', '/'),
        'start_url' => env('PWA_START_URL', '/'),
        'icons' => [
            '16x16' => '/icons/icon-16x16.png',
            '32x32' => '/icons/icon-32x32.png',
            '72x72' => '/icons/icon-72x72.png',
            '96x96' => '/icons/icon-96x96.png',
            '128x128' => '/icons/icon-128x128.png',
            '144x144' => '/icons/icon-144x144.png',
            '152x152' => '/icons/icon-152x152.png',
            '192x192' => '/icons/icon-192x192.png',
            '384x384' => '/icons/icon-384x384.png',
            '512x512' => '/icons/icon-512x512.png',
        ],
        'shortcuts' => [
            [
                'name' => 'Order Food',
                'short_name' => 'Order',
                'description' => 'Quick access to order food',
                'url' => '/menu',
                'icons' => [
                    [
                        'src' => '/icons/icon-96x96.png',
                        'sizes' => '96x96'
                    ]
                ]
            ],
            [
                'name' => 'My Orders',
                'short_name' => 'Orders',
                'description' => 'View your order history',
                'url' => '/orders',
                'icons' => [
                    [
                        'src' => '/icons/icon-96x96.png',
                        'sizes' => '96x96'
                    ]
                ]
            ],
            [
                'name' => 'Wallet',
                'short_name' => 'Wallet',
                'description' => 'Manage your wallet and rewards',
                'url' => '/wallet',
                'icons' => [
                    [
                        'src' => '/icons/icon-96x96.png',
                        'sizes' => '96x96'
                    ]
                ]
            ]
        ]
    ],
    
    // Business Settings
    'business' => [
        'currency' => env('RESTAURANT_CURRENCY', 'NGN'),
        'currency_symbol' => env('RESTAURANT_CURRENCY_SYMBOL', '₦'),
        'delivery_fee' => env('RESTAURANT_DELIVERY_FEE', 500),
        'minimum_order' => env('RESTAURANT_MINIMUM_ORDER', 1000),
        'opening_hours' => [
            'monday' => env('RESTAURANT_MONDAY_HOURS', '8:00 AM - 10:00 PM'),
            'tuesday' => env('RESTAURANT_TUESDAY_HOURS', '8:00 AM - 10:00 PM'),
            'wednesday' => env('RESTAURANT_WEDNESDAY_HOURS', '8:00 AM - 10:00 PM'),
            'thursday' => env('RESTAURANT_THURSDAY_HOURS', '8:00 AM - 10:00 PM'),
            'friday' => env('RESTAURANT_FRIDAY_HOURS', '8:00 AM - 11:00 PM'),
            'saturday' => env('RESTAURANT_SATURDAY_HOURS', '9:00 AM - 11:00 PM'),
            'sunday' => env('RESTAURANT_SUNDAY_HOURS', '10:00 AM - 9:00 PM'),
        ],
        'payment_methods' => [
            'cash' => true,
            'transfer' => true,
            'wallet' => true,
        ],
        'order_types' => [
            'delivery' => true,
            'pickup' => true,
            'dine_in' => true,
        ]
    ],
    
    // Social Media
    'social' => [
        'facebook' => env('RESTAURANT_FACEBOOK', ''),
        'instagram' => env('RESTAURANT_INSTAGRAM', ''),
        'twitter' => env('RESTAURANT_TWITTER', ''),
        'whatsapp' => env('RESTAURANT_WHATSAPP', ''),
    ],
    
    // Features
    'features' => [
        'wallet' => env('RESTAURANT_WALLET_ENABLED', true),
        'rewards' => env('RESTAURANT_REWARDS_ENABLED', true),
        'qr_ordering' => env('RESTAURANT_QR_ORDERING_ENABLED', true),
        'push_notifications' => env('RESTAURANT_PUSH_NOTIFICATIONS_ENABLED', true),
        'offline_mode' => env('RESTAURANT_OFFLINE_MODE_ENABLED', true),
        'dark_mode' => env('RESTAURANT_DARK_MODE_ENABLED', true),
        'multi_language' => env('RESTAURANT_MULTI_LANGUAGE_ENABLED', false),
    ],
    
    // WhatsApp Integration
    'whatsapp' => [
        'enabled' => env('WHATSAPP_ENABLED', true),
        'phone' => env('WHATSAPP_PHONE', '+234 XXX XXX XXXX'),
        'business_name' => env('WHATSAPP_BUSINESS_NAME', 'Fastify'),
        'welcome_message' => env('WHATSAPP_WELCOME_MESSAGE', 'Welcome to Fastify! How can we help you today?'),
    ],
    
    // Analytics
    'analytics' => [
        'google_analytics_id' => env('GOOGLE_ANALYTICS_ID', ''),
        'facebook_pixel_id' => env('FACEBOOK_PIXEL_ID', ''),
    ],
    
    // SEO
    'seo' => [
        'title' => env('SEO_TITLE', 'Fastify - Food Ordering'),
        'description' => env('SEO_DESCRIPTION', 'Order delicious food from your favorite restaurants'),
        'keywords' => env('SEO_KEYWORDS', 'food, delivery, restaurant, order online'),
        'og_image' => env('SEO_OG_IMAGE', '/images/og-image.jpg'),
    ],
]; 