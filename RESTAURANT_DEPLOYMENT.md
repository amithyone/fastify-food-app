# 🏪 Restaurant Deployment Guide - Fastify

## 🎯 Overview

This guide explains how to deploy the Fastify food ordering application for different restaurants with custom branding, names, and configurations.

## 🚀 Quick Setup for New Restaurant

### 1. Environment Configuration

Create a new `.env` file for each restaurant:

```env
# Restaurant Basic Information
RESTAURANT_NAME="Restaurant Name"
RESTAURANT_DISPLAY_NAME="Restaurant Name - Food Ordering"
RESTAURANT_SHORT_NAME="Restaurant"
RESTAURANT_DESCRIPTION="Order delicious food from Restaurant Name"

# Contact Information
RESTAURANT_PHONE="+234 XXX XXX XXXX"
RESTAURANT_EMAIL="contact@restaurant.com"
RESTAURANT_ADDRESS="Restaurant Address, City, State"

# Branding
RESTAURANT_LOGO="/images/restaurant-logo.png"
RESTAURANT_FAVICON="/icons/restaurant-favicon.png"
RESTAURANT_THEME_COLOR="#f97316"
RESTAURANT_ACCENT_COLOR="#ea580c"

# PWA Configuration
PWA_NAME="Restaurant Name - Food Ordering"
PWA_SHORT_NAME="Restaurant"
PWA_DESCRIPTION="Order delicious food from Restaurant Name"
PWA_THEME_COLOR="#f97316"
PWA_BACKGROUND_COLOR="#ffffff"

# Business Settings
RESTAURANT_CURRENCY="NGN"
RESTAURANT_CURRENCY_SYMBOL="₦"
RESTAURANT_DELIVERY_FEE=500
RESTAURANT_MINIMUM_ORDER=1000

# Opening Hours
RESTAURANT_MONDAY_HOURS="8:00 AM - 10:00 PM"
RESTAURANT_TUESDAY_HOURS="8:00 AM - 10:00 PM"
RESTAURANT_WEDNESDAY_HOURS="8:00 AM - 10:00 PM"
RESTAURANT_THURSDAY_HOURS="8:00 AM - 10:00 PM"
RESTAURANT_FRIDAY_HOURS="8:00 AM - 11:00 PM"
RESTAURANT_SATURDAY_HOURS="9:00 AM - 11:00 PM"
RESTAURANT_SUNDAY_HOURS="10:00 AM - 9:00 PM"

# Social Media
RESTAURANT_FACEBOOK="https://facebook.com/restaurant"
RESTAURANT_INSTAGRAM="https://instagram.com/restaurant"
RESTAURANT_TWITTER="https://twitter.com/restaurant"
RESTAURANT_WHATSAPP="+234 XXX XXX XXXX"

# WhatsApp Integration
WHATSAPP_ENABLED=true
WHATSAPP_PHONE="+234 XXX XXX XXXX"
WHATSAPP_BUSINESS_NAME="Restaurant Name"
WHATSAPP_WELCOME_MESSAGE="Welcome to Restaurant Name! How can we help you today?"

# Features
RESTAURANT_WALLET_ENABLED=true
RESTAURANT_REWARDS_ENABLED=true
RESTAURANT_QR_ORDERING_ENABLED=true
RESTAURANT_PUSH_NOTIFICATIONS_ENABLED=true
RESTAURANT_OFFLINE_MODE_ENABLED=true
RESTAURANT_DARK_MODE_ENABLED=true
RESTAURANT_MULTI_LANGUAGE_ENABLED=false

# SEO
SEO_TITLE="Restaurant Name - Food Ordering"
SEO_DESCRIPTION="Order delicious food from Restaurant Name"
SEO_KEYWORDS="food, delivery, restaurant, order online"
SEO_OG_IMAGE="/images/restaurant-og-image.jpg"
```

### 2. Custom Icons and Branding

#### Generate Restaurant Icons

Use the icon generation script with custom branding:

```bash
# Create custom icon generation script
php -r "
\$restaurantName = 'Restaurant Name';
\$themeColor = '#f97316';

// Generate icons with restaurant branding
\$sizes = [16, 32, 72, 96, 128, 144, 152, 192, 384, 512];

foreach (\$sizes as \$size) {
    \$image = imagecreatetruecolor(\$size, \$size);
    imagealphablending(\$image, true);
    imagesavealpha(\$image, true);
    
    \$transparent = imagecolorallocatealpha(\$image, 0, 0, 0, 127);
    imagefill(\$image, 0, 0, \$transparent);
    
    // Use restaurant theme color
    \$color = imagecolorallocate(\$image, 249, 115, 22);
    
    \$padding = \$size * 0.1;
    imagefilledrectangle(\$image, \$padding, \$padding, \$size - \$padding, \$size - \$padding, \$color);
    
    // Add restaurant initials
    if (\$size >= 72) {
        \$white = imagecolorallocate(\$image, 255, 255, 255);
        \$text = substr(\$restaurantName, 0, 2);
        \$fontSize = max(8, \$size / 8);
        
        \$textWidth = imagefontwidth(5) * strlen(\$text);
        \$textHeight = imagefontheight(5);
        \$x = (\$size - \$textWidth) / 2;
        \$y = (\$size - \$textHeight) / 2;
        imagestring(\$image, 5, \$x, \$y, \$text, \$white);
    }
    
    imagepng(\$image, \"public/icons/restaurant-icon-{\$size}x{\$size}.png\");
    imagedestroy(\$image);
}

echo 'Restaurant icons generated successfully!';
"
```

#### Update Icon Paths

Update the restaurant configuration to use custom icons:

```php
// config/restaurant.php
'pwa' => [
    'icons' => [
        '16x16' => '/icons/restaurant-icon-16x16.png',
        '32x32' => '/icons/restaurant-icon-32x32.png',
        '72x72' => '/icons/restaurant-icon-72x72.png',
        '96x96' => '/icons/restaurant-icon-96x96.png',
        '128x128' => '/icons/restaurant-icon-128x128.png',
        '144x144' => '/icons/restaurant-icon-144x144.png',
        '152x152' => '/icons/restaurant-icon-152x152.png',
        '192x192' => '/icons/restaurant-icon-192x192.png',
        '384x384' => '/icons/restaurant-icon-384x384.png',
        '512x512' => '/icons/restaurant-icon-512x512.png',
    ],
]
```

### 3. Database Setup

#### Run Migrations

```bash
php artisan migrate
```

#### Seed Restaurant Data

```bash
php artisan db:seed --class=RestaurantSeeder
```

#### Create Restaurant Admin

```bash
php artisan tinker
```

```php
$user = new \App\Models\User();
$user->name = 'Restaurant Admin';
$user->email = 'admin@restaurant.com';
$user->phone = '+234 XXX XXX XXXX';
$user->password = bcrypt('password');
$user->role = 'admin';
$user->save();
```

### 4. Custom Content

#### Update Welcome Page

```php
// resources/views/welcome.blade.php
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            Welcome to {{ \App\Helpers\PWAHelper::getRestaurantName() }}
        </h1>
        <p class="text-lg text-gray-600 mb-8">
            {{ \App\Helpers\PWAHelper::getRestaurantConfig()['description'] }}
        </p>
        <a href="/menu" class="bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors">
            Order Now
        </a>
    </div>
</div>
@endsection
```

#### Update Menu Categories

```php
// database/seeders/CategorySeeder.php
public function run()
{
    $categories = [
        ['name' => 'Fadded VIP 🔆 Main Course', 'description' => 'Delicious main dishes'],
        ['name' => 'Fadded VIP 🔆 Appetizers', 'description' => 'Start your meal right'],
        ['name' => 'Fadded VIP 🔆 Desserts', 'description' => 'Sweet endings'],
        ['name' => 'Fadded VIP 🔆 Beverages', 'description' => 'Refreshing drinks'],
    ];

    foreach ($categories as $category) {
        \App\Models\Category::create($category);
    }
}
```

## 🔧 Advanced Configuration

### 1. Multi-Restaurant Setup

For managing multiple restaurants, create a restaurant model:

```php
// app/Models/Restaurant.php
class Restaurant extends Model
{
    protected $fillable = [
        'name', 'display_name', 'short_name', 'description',
        'phone', 'email', 'address', 'logo', 'theme_color',
        'delivery_fee', 'minimum_order', 'opening_hours',
        'features', 'social_links', 'pwa_config'
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'features' => 'array',
        'social_links' => 'array',
        'pwa_config' => 'array',
    ];
}
```

### 2. Dynamic Configuration

Update the PWA helper to support multiple restaurants:

```php
// app/Helpers/PWAHelper.php
public static function getRestaurantConfig($restaurantId = null)
{
    if ($restaurantId) {
        $restaurant = \App\Models\Restaurant::find($restaurantId);
        return $restaurant ? $restaurant->toArray() : Config::get('restaurant');
    }
    
    return Config::get('restaurant');
}
```

### 3. Subdomain Setup

For different restaurants on subdomains:

```php
// app/Http/Middleware/SetRestaurant.php
class SetRestaurant
{
    public function handle($request, Closure $next)
    {
        $subdomain = explode('.', $request->getHost())[0];
        
        if ($subdomain !== 'www' && $subdomain !== 'localhost') {
            $restaurant = \App\Models\Restaurant::where('subdomain', $subdomain)->first();
            
            if ($restaurant) {
                config(['restaurant' => $restaurant->toArray()]);
            }
        }
        
        return $next($request);
    }
}
```

## 📱 PWA Customization

### 1. Custom Splash Screen

Create restaurant-specific splash screen:

```html
<!-- public/splash.html -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Helpers\PWAHelper::getRestaurantName() }}</title>
    <style>
        body {
            background: {{ \App\Helpers\PWAHelper::getThemeColor() }};
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .splash {
            text-align: center;
            color: white;
        }
        .logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            border-radius: 20px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
        }
    </style>
</head>
<body>
    <div class="splash">
        <div class="logo">
            {{ substr(\App\Helpers\PWAHelper::getRestaurantName(), 0, 2) }}
        </div>
        <h1>{{ \App\Helpers\PWAHelper::getRestaurantName() }}</h1>
        <p>Loading...</p>
    </div>
</body>
</html>
```

### 2. Custom Offline Page

Update offline page for restaurant branding:

```html
<!-- public/offline.html -->
<!DOCTYPE html>
<html>
<head>
    <title>{{ \App\Helpers\PWAHelper::getRestaurantName() }} - Offline</title>
    <!-- ... rest of offline page with restaurant branding ... -->
</head>
<body>
    <div class="offline-container">
        <h1>You're Offline</h1>
        <p>Don't worry! {{ \App\Helpers\PWAHelper::getRestaurantName() }} works offline too.</p>
        <!-- ... rest of content ... -->
    </div>
</body>
</html>
```

## 🚀 Deployment Steps

### 1. Production Environment

```bash
# Set production environment
APP_ENV=production
APP_DEBUG=false
APP_URL=https://restaurant-domain.com

# Configure database
DB_HOST=production_db_host
DB_DATABASE=restaurant_db
DB_USERNAME=restaurant_user
DB_PASSWORD=secure_password

# Configure cache and sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 2. SSL Certificate

Ensure HTTPS is configured for PWA functionality:

```bash
# Install SSL certificate
sudo certbot --nginx -d restaurant-domain.com

# Redirect HTTP to HTTPS
# Add to nginx configuration
```

### 3. Build Assets

```bash
# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Database Migration

```bash
# Run migrations
php artisan migrate --force

# Seed data
php artisan db:seed --force
```

## 📊 Monitoring and Analytics

### 1. Google Analytics

Add restaurant-specific tracking:

```javascript
// resources/js/app.js
if (window.PWAConfig.analytics && window.PWAConfig.analytics.googleAnalyticsId) {
    gtag('config', window.PWAConfig.analytics.googleAnalyticsId, {
        'custom_map': {
            'dimension1': 'restaurant_name',
            'dimension2': 'restaurant_location'
        }
    });
    
    gtag('set', 'dimension1', window.PWAConfig.appName);
    gtag('set', 'dimension2', window.PWAConfig.location);
}
```

### 2. Performance Monitoring

```javascript
// Track PWA performance
if ('PerformanceObserver' in window) {
    const observer = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
            // Send to analytics
            gtag('event', 'performance', {
                'event_category': 'pwa',
                'event_label': entry.name,
                'value': Math.round(entry.value),
                'restaurant': window.PWAConfig.appName
            });
        }
    });
    
    observer.observe({ entryTypes: ['largest-contentful-paint', 'first-input', 'layout-shift'] });
}
```

## 🔄 Updates and Maintenance

### 1. Restaurant Configuration Updates

```bash
# Update restaurant configuration
php artisan config:cache

# Clear PWA cache
php artisan cache:clear
```

### 2. Icon Updates

```bash
# Regenerate icons with new branding
php generate-restaurant-icons.php

# Clear browser cache
# Users will need to reinstall PWA for new icons
```

### 3. Feature Toggles

Enable/disable features per restaurant:

```env
# Disable wallet for specific restaurant
RESTAURANT_WALLET_ENABLED=false

# Enable multi-language
RESTAURANT_MULTI_LANGUAGE_ENABLED=true
```

## 📋 Checklist for New Restaurant

- [ ] Environment variables configured
- [ ] Custom icons generated
- [ ] Database migrated and seeded
- [ ] Admin user created
- [ ] SSL certificate installed
- [ ] PWA manifest updated
- [ ] Offline page customized
- [ ] Analytics configured
- [ ] Performance tested
- [ ] User acceptance testing completed

## 🎉 Success Metrics

Track these metrics for each restaurant:

- **Installation Rate**: % of users who install the PWA
- **Order Conversion**: % of visitors who place orders
- **Average Order Value**: Revenue per order
- **User Retention**: Return customer rate
- **Performance Scores**: Lighthouse PWA audit scores

---

**Your restaurant is now ready to serve customers with a fully branded Fastify PWA! 🚀** 