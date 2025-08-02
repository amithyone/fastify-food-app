# 🍽️ Fastify - Food Ordering Application

A modern, mobile-first food ordering application built with Laravel, featuring a comprehensive wallet system, reward points, and restaurant management capabilities.

## 📱 Features Overview

### 🏠 **Core Features**
- **Mobile-First Design**: Optimized for mobile devices with responsive UI
- **Light/Dark Mode**: Toggle between light and dark themes with persistent preferences
- **Real-time Search**: Instant search functionality for menu items
- **Cart Management**: Add, remove, and update quantities with persistent storage
- **Order Tracking**: Real-time order progress with 5-step status tracking
- **User Authentication**: Phone number and email-based authentication with WhatsApp verification

### 💳 **Wallet & Rewards System**
- **Digital Wallet**: Store funds and make payments directly from wallet
- **Reward Points**: Earn 1 point per ₦100 spent on bank transfer payments
- **Transaction History**: Complete transaction and reward history tracking
- **Point Expiry**: Points expire after 6 months with automatic tracking
- **Wallet Payments**: Pay for orders using wallet balance

### 🏪 **Restaurant Management**
- **Multi-Restaurant Support**: Each restaurant has its own unique instance
- **QR Code System**: 50 unique QR codes per restaurant for table-based ordering
- **Guest Sessions**: Temporary sessions for QR code users (1-2 hours)
- **Restaurant Dashboard**: Upload statuses, manage dishes, set WhatsApp numbers
- **Order Notifications**: WhatsApp integration for order notifications

### 🛒 **Ordering System**
- **In-Restaurant Orders**: Special mode for dining-in with table numbers
- **Delivery Orders**: Full delivery with address management
- **Multiple Payment Methods**: Cash, Bank Transfer, Wallet Payment
- **Order Types**: Automatic detection of restaurant vs delivery orders
- **Payment Status**: Real-time payment status tracking

### 🎨 **User Interface**
- **Sticky Navigation**: Fixed top navigation bars for easy access
- **Bottom Navigation**: 5-icon navigation (Home, Cart, Orders, Wallet, Login)
- **Interactive Elements**: Visual feedback for clicks and notifications
- **Dish Details Modal**: Detailed information including ingredients and allergens
- **Custom Alerts**: Custom notification system instead of browser alerts

## 🚀 Quick Start

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL/MariaDB
- Node.js & NPM (for frontend assets)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd abujaeat-laravel
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install frontend dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=abueat
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

## 📊 Database Schema

### Core Tables

#### Users & Authentication
- `users` - User accounts and authentication
- `phone_verifications` - WhatsApp verification codes
- `addresses` - User delivery addresses

#### Menu & Categories
- `categories` - Food categories (e.g., "Fadded VIP 🔆 Main Course")
- `menu_items` - Individual dishes with ingredients and allergens
- `restaurants` - Restaurant information and settings

#### Orders & Transactions
- `orders` - Order details and status tracking
- `order_items` - Individual items in each order
- `wallets` - User wallet balances and points
- `wallet_transactions` - All wallet transactions
- `user_rewards` - Reward points earned by users

#### Restaurant Features
- `table_qrs` - QR codes for restaurant tables
- `guest_sessions` - Temporary sessions for QR users
- `stories` - Restaurant status updates and promotions

## 🎯 Key Features Documentation

### 1. Wallet System

#### Wallet Balance
```php
// Get user's wallet
$wallet = $user->getWalletOrCreate();

// Check balance
echo $wallet->formatted_balance; // "₦1,500.00"
echo $wallet->points_display;    // "15 points"
```

#### Making Transactions
```php
// Credit wallet
$wallet->credit(1000, 'Added funds', ['order_id' => $order->id]);

// Debit wallet
$wallet->debit(500, 'Order payment', ['order_id' => $order->id]);
```

#### Reward Points
```php
// Earn points (1 point per ₦100)
$points = floor($orderAmount / 100);
$reward = UserReward::create([
    'user_id' => $user->id,
    'order_id' => $order->id,
    'points_earned' => $points,
    'payment_method' => 'transfer',
    'status' => 'credited'
]);
```

### 2. Order Management

#### Order Types
```php
// Check if order is for restaurant dining
if ($order->isRestaurantOrder()) {
    // Skip delivery fee
    $deliveryFee = 0;
} else {
    // Apply delivery fee
    $deliveryFee = 500;
}
```

#### Payment Methods
- **Cash**: Payment on delivery/collection
- **Bank Transfer**: Earn reward points
- **Wallet**: Pay from wallet balance

### 3. Restaurant QR System

#### QR Code Generation
```php
// Generate QR codes for restaurant tables
for ($i = 1; $i <= 50; $i++) {
    TableQR::create([
        'restaurant_id' => $restaurant->id,
        'table_number' => $i,
        'qr_code' => Str::random(32),
        'is_active' => true
    ]);
}
```

#### Guest Sessions
```php
// Create temporary session for QR users
$session = GuestSession::create([
    'qr_code' => $qrCode,
    'table_number' => $tableNumber,
    'expires_at' => now()->addHours(2)
]);
```

### 4. User Interface Features

#### Dark Mode Toggle
```javascript
// Toggle dark mode
function setTheme(dark) {
    if (dark) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

// Save preference
localStorage.setItem('theme', isDark ? 'dark' : 'light');
```

#### Cart Management
```javascript
// Add to cart
function addToCart(itemId, name, price) {
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    // Add item logic
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
}
```

## 🔧 Configuration

### Environment Variables

#### Database
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=abueat
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### WhatsApp Integration
```env
ENABLE_REAL_WHATSAPP=false
TWILIO_ACCOUNT_SID=your_twilio_sid
TWILIO_AUTH_TOKEN=your_twilio_token
TWILIO_PHONE_NUMBER=your_twilio_phone
```

#### App Settings
```env
APP_NAME="Fastify"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

### Tailwind CSS Configuration
```javascript
// tailwind.config.js
module.exports = {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                orange: {
                    500: '#f97316',
                    600: '#ea580c',
                }
            }
        }
    }
}
```

## 📱 Mobile-First Design

### Responsive Breakpoints
- **Mobile**: Default (320px+)
- **Tablet**: `md:` (768px+)
- **Desktop**: `lg:` (1024px+)

### Touch-Friendly Elements
- Minimum 44px touch targets
- Proper spacing between interactive elements
- Swipe gestures for navigation
- Pull-to-refresh functionality

## 🔒 Security Features

### Authentication
- CSRF protection on all forms
- Session-based authentication
- Phone number verification via WhatsApp
- Secure password hashing

### Data Protection
- Input validation and sanitization
- SQL injection prevention
- XSS protection
- Secure file uploads

## 🧪 Testing

### Run Tests
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=OrderTest

# Run with coverage
php artisan test --coverage
```

### Test Categories
- **Unit Tests**: Individual component testing
- **Feature Tests**: End-to-end functionality testing
- **Browser Tests**: User interface testing

## 📈 Performance Optimization

### Database Optimization
- Proper indexing on frequently queried columns
- Eager loading for relationships
- Query optimization for large datasets

### Frontend Optimization
- Minified CSS and JavaScript
- Image optimization
- Lazy loading for images
- Caching strategies

## 🚀 Deployment

### Production Setup
1. Set `APP_ENV=production`
2. Configure production database
3. Set up SSL certificate
4. Configure web server (Nginx/Apache)
5. Set up queue workers for background jobs

### Environment Variables for Production
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_HOST=production_db_host
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## 🔧 Maintenance

### Regular Tasks
- **Database Backups**: Daily automated backups
- **Log Rotation**: Weekly log cleanup
- **Cache Clearing**: Periodic cache refresh
- **Security Updates**: Regular dependency updates

### Monitoring
- Error logging and monitoring
- Performance metrics tracking
- User activity analytics
- Order completion rates

## 📞 Support & Contact

### Technical Support
- **Email**: support@abujaeat.com
- **WhatsApp**: +234 XXX XXX XXXX
- **Documentation**: [Link to full docs]

### Bug Reports
Please report bugs with:
- Detailed description of the issue
- Steps to reproduce
- Screenshots/videos if applicable
- Browser/device information

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new features
5. Submit a pull request

## 📝 Changelog

### Version 1.0.0 (Current)
- ✅ Complete wallet system implementation
- ✅ Reward points with expiry tracking
- ✅ Restaurant QR code system
- ✅ Mobile-first responsive design
- ✅ Dark/light mode toggle
- ✅ WhatsApp verification system
- ✅ Order tracking and management
- ✅ Multi-payment method support

---

**Built with ❤️ using Laravel, Tailwind CSS, and modern web technologies**
