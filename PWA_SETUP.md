# 📱 PWA Setup Guide - Abuja Eat

## 🎯 Overview

This guide will help you set up your Abuja Eat application as a Progressive Web App (PWA) with full offline functionality, push notifications, and app installation capabilities.

## 🚀 Quick Setup

### 1. Generate PWA Icons

First, you need to create the app icons. You can use any image editor or online tools:

#### Using Online Tools:
- **Favicon.io**: https://favicon.io/
- **PWA Builder**: https://www.pwabuilder.com/imageGenerator
- **RealFaviconGenerator**: https://realfavicongenerator.net/

#### Required Icon Sizes:
```
public/icons/
├── icon-16x16.png
├── icon-32x32.png
├── icon-72x72.png
├── icon-96x96.png
├── icon-128x128.png
├── icon-144x144.png
├── icon-152x152.png
├── icon-192x192.png
├── icon-384x384.png
└── icon-512x512.png
```

#### Icon Specifications:
- **Format**: PNG with transparency
- **Style**: Square with rounded corners (optional)
- **Colors**: Use your brand colors (#f97316 for Abuja Eat)
- **Design**: Simple, recognizable logo

### 2. Create App Screenshots

Add screenshots for the PWA store listing:

```
public/screenshots/
├── mobile-menu.png (390x844)
├── mobile-cart.png (390x844)
└── mobile-orders.png (390x844)
```

## 🔧 Configuration

### 1. Update Manifest File

The `public/manifest.json` file is already configured with:
- App name and description
- Theme colors
- Icons and shortcuts
- Display mode (standalone)

### 2. Configure VAPID Keys for Push Notifications

#### Generate VAPID Keys:

```bash
# Install web-push globally
npm install -g web-push

# Generate VAPID keys
web-push generate-vapid-keys
```

#### Add to Environment Variables:

```env
# .env
VAPID_PUBLIC_KEY=your_public_key_here
VAPID_PRIVATE_KEY=your_private_key_here
```

#### Update Service Worker:

Replace `'YOUR_VAPID_PUBLIC_KEY'` in `public/sw.js` with your actual public key.

### 3. Configure Laravel for Push Notifications

#### Install Required Packages:

```bash
composer require minishlink/web-push
```

#### Create Push Notification Controller:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushNotificationController extends Controller
{
    public function subscribe(Request $request)
    {
        $subscription = $request->all();
        
        // Store subscription in database
        $user = auth()->user();
        $user->push_subscription = json_encode($subscription);
        $user->save();
        
        return response()->json(['success' => true]);
    }
    
    public function send(Request $request)
    {
        $subscription = Subscription::create([
            'endpoint' => $request->endpoint,
            'keys' => $request->keys
        ]);
        
        $webPush = new WebPush([
            'VAPID' => [
                'subject' => 'mailto:your-email@example.com',
                'publicKey' => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ]);
        
        $report = $webPush->sendOneNotification(
            $subscription,
            json_encode([
                'title' => 'Order Update',
                'body' => 'Your order status has been updated!',
                'icon' => '/icons/icon-192x192.png',
                'data' => ['url' => '/orders']
            ])
        );
        
        return response()->json(['success' => true]);
    }
}
```

#### Add Routes:

```php
// routes/web.php
Route::post('/api/push-subscription', [PushNotificationController::class, 'subscribe']);
Route::post('/api/push-send', [PushNotificationController::class, 'send']);
```

## 🧪 Testing PWA Features

### 1. Test Installation

1. **Open Chrome DevTools**
2. **Go to Application tab**
3. **Check Manifest section**
4. **Verify all icons are loading**
5. **Test install prompt**

### 2. Test Offline Functionality

1. **Open DevTools → Network tab**
2. **Check "Offline" checkbox**
3. **Refresh the page**
4. **Verify offline page loads**
5. **Test cached resources**

### 3. Test Service Worker

1. **Go to Application → Service Workers**
2. **Check registration status**
3. **Test cache storage**
4. **Verify background sync**

### 4. Test Push Notifications

```javascript
// Test in browser console
if ('serviceWorker' in navigator && 'PushManager' in window) {
    navigator.serviceWorker.ready.then(registration => {
        registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: 'YOUR_VAPID_PUBLIC_KEY'
        }).then(subscription => {
            console.log('Push subscription:', subscription);
        });
    });
}
```

## 📱 PWA Features Checklist

### ✅ Core PWA Features
- [ ] Web App Manifest
- [ ] Service Worker
- [ ] Offline functionality
- [ ] App installation prompt
- [ ] Responsive design
- [ ] Fast loading

### ✅ Advanced Features
- [ ] Push notifications
- [ ] Background sync
- [ ] Offline order storage
- [ ] Cache strategies
- [ ] App shortcuts
- [ ] Splash screen

### ✅ User Experience
- [ ] Smooth animations
- [ ] Touch-friendly interface
- [ ] Native app feel
- [ ] Fast navigation
- [ ] Offline indicators

## 🔍 PWA Audit Tools

### 1. Lighthouse Audit

```bash
# Install Lighthouse globally
npm install -g lighthouse

# Run audit
lighthouse https://your-domain.com --view
```

### 2. Chrome DevTools

1. **Open DevTools**
2. **Go to Lighthouse tab**
3. **Select "Progressive Web App"**
4. **Run audit**

### 3. PWA Builder

Visit https://www.pwabuilder.com/ and enter your URL for a comprehensive audit.

## 🚀 Deployment Considerations

### 1. HTTPS Requirement

PWAs require HTTPS in production. Ensure your domain has a valid SSL certificate.

### 2. Service Worker Updates

```javascript
// Handle service worker updates
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.addEventListener('controllerchange', () => {
        window.location.reload();
    });
}
```

### 3. Cache Versioning

Update cache names when deploying new versions:

```javascript
const CACHE_VERSION = 'v1.0.1';
const STATIC_CACHE = `abuja-eat-static-${CACHE_VERSION}`;
```

### 4. Performance Optimization

- **Image optimization**: Use WebP format
- **Code splitting**: Load only necessary resources
- **Lazy loading**: Defer non-critical resources
- **Compression**: Enable gzip/brotli compression

## 📊 Monitoring PWA Performance

### 1. Analytics Integration

```javascript
// Track PWA installations
window.addEventListener('appinstalled', () => {
    gtag('event', 'pwa_install', {
        'event_category': 'engagement',
        'event_label': 'app_install'
    });
});
```

### 2. Error Tracking

```javascript
// Track service worker errors
navigator.serviceWorker.addEventListener('error', (error) => {
    console.error('Service Worker error:', error);
    // Send to error tracking service
});
```

### 3. Performance Metrics

```javascript
// Track Core Web Vitals
if ('PerformanceObserver' in window) {
    const observer = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
            console.log(`${entry.name}: ${entry.value}`);
        }
    });
    observer.observe({ entryTypes: ['largest-contentful-paint', 'first-input', 'layout-shift'] });
}
```

## 🐛 Common Issues & Solutions

### 1. Service Worker Not Registering

**Issue**: Service worker fails to register
**Solution**: Check file path and HTTPS requirement

### 2. Icons Not Loading

**Issue**: PWA icons not displaying
**Solution**: Verify file paths and image formats

### 3. Install Prompt Not Showing

**Issue**: Install prompt doesn't appear
**Solution**: Ensure all PWA criteria are met

### 4. Push Notifications Not Working

**Issue**: Push notifications fail
**Solution**: Check VAPID keys and subscription

### 5. Offline Mode Not Working

**Issue**: App doesn't work offline
**Solution**: Verify service worker caching strategy

## 📚 Additional Resources

- [PWA Documentation](https://web.dev/progressive-web-apps/)
- [Service Worker API](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)
- [Web Push Protocol](https://tools.ietf.org/html/rfc8030)
- [Lighthouse PWA Audit](https://developers.google.com/web/tools/lighthouse)

## 🎉 Success Metrics

Track these metrics to measure PWA success:

- **Installation Rate**: % of users who install the PWA
- **Engagement**: Time spent in app
- **Retention**: Return visits
- **Performance**: Core Web Vitals scores
- **Offline Usage**: % of offline sessions

---

**Your Abuja Eat PWA is now ready for production! 🚀** 