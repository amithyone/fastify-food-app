# 👨‍💻 Developer Guide - Fastify

## 🏗️ Architecture Overview

### Technology Stack
- **Backend**: Laravel 11 (PHP 8.1+)
- **Frontend**: Blade Templates + Tailwind CSS
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Breeze + Custom WhatsApp Verification
- **Payment**: Custom Wallet System + Bank Transfer Integration
- **Real-time**: AJAX + JavaScript (Vanilla)

### Project Structure
```
abujaeat-laravel/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   ├── Models/              # Eloquent models
│   ├── Providers/           # Service providers
│   └── Policies/            # Authorization policies
├── database/
│   ├── migrations/          # Database schema
│   ├── seeders/            # Sample data
│   └── factories/          # Model factories
├── resources/
│   ├── views/              # Blade templates
│   ├── css/               # Tailwind styles
│   └── js/                # JavaScript files
├── routes/
│   └── web.php            # Web routes
└── public/                # Public assets
```

## 🗄️ Database Models & Relationships

### Core Models

#### User Model
```php
class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'phone', 'password'
    ];

    // Relationships
    public function wallet() {
        return $this->hasOne(Wallet::class);
    }

    public function rewards() {
        return $this->hasMany(UserReward::class);
    }

    public function addresses() {
        return $this->hasMany(Address::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    // Helper methods
    public function getWalletOrCreate() {
        return $this->wallet ?? $this->wallet()->create([
            'balance' => 0,
            'points' => 0
        ]);
    }
}
```

#### Wallet Model
```php
class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance', 'points'];

    // Relationships
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function transactions() {
        return $this->hasMany(WalletTransaction::class);
    }

    // Accessors
    public function getFormattedBalanceAttribute() {
        return '₦' . number_format($this->balance, 2);
    }

    public function getPointsDisplayAttribute() {
        return $this->points . ' points';
    }

    // Methods
    public function credit($amount, $description, $metadata = []) {
        return DB::transaction(function () use ($amount, $description, $metadata) {
            $this->increment('balance', $amount);
            
            return $this->transactions()->create([
                'type' => 'credit',
                'amount' => $amount,
                'description' => $description,
                'metadata' => $metadata,
                'status' => 'completed'
            ]);
        });
    }

    public function debit($amount, $description, $metadata = []) {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        return DB::transaction(function () use ($amount, $description, $metadata) {
            $this->decrement('balance', $amount);
            
            return $this->transactions()->create([
                'type' => 'debit',
                'amount' => $amount,
                'description' => $description,
                'metadata' => $metadata,
                'status' => 'completed'
            ]);
        });
    }
}
```

#### Order Model
```php
class Order extends Model
{
    protected $fillable = [
        'user_id', 'total_amount', 'delivery_address',
        'payment_method', 'payment_status', 'order_status'
    ];

    // Relationships
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    // Accessors
    public function getDeliveryFeeAttribute() {
        return $this->isRestaurantOrder() ? 0 : 500;
    }

    public function getSubtotalAttribute() {
        return $this->total_amount - $this->delivery_fee;
    }

    // Methods
    public function isRestaurantOrder() {
        return str_contains($this->delivery_address, 'Table:');
    }

    public function calculatePoints() {
        if ($this->payment_method === 'transfer') {
            return floor($this->total_amount / 100);
        }
        return 0;
    }
}
```

## 🔌 API Endpoints

### Authentication Routes
```php
// Phone verification
POST /verify-phone
POST /verify-code
POST /login
POST /register
POST /logout

// Guest QR access
GET /qr/{code}
POST /guest-session
```

### Menu & Cart Routes
```php
// Menu
GET /menu
GET /menu/search
GET /menu/category/{id}

// Cart
GET /cart
POST /cart/add
POST /cart/update
POST /cart/remove
```

### Order Routes
```php
// Orders
GET /orders
POST /orders
GET /orders/{id}
GET /orders/{id}/status

// User orders
GET /user/orders
GET /user/orders/{id}
```

### Wallet Routes
```php
// Wallet management
GET /wallet
GET /wallet/transactions
GET /wallet/rewards
POST /wallet/add-funds
GET /wallet/info

// Wallet transactions
GET /wallet/transactions
GET /wallet/rewards
```

## 🎨 Frontend Components

### Dark Mode Implementation
```javascript
// Dark mode toggle functionality
class DarkModeManager {
    constructor() {
        this.toggle = document.getElementById('darkModeToggle');
        this.init();
    }

    init() {
        const userPref = localStorage.getItem('theme');
        const systemPref = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const isDark = userPref === 'dark' || (!userPref && systemPref);
        
        this.setTheme(isDark);
        this.bindEvents();
    }

    setTheme(dark) {
        if (dark) {
            document.documentElement.classList.add('dark');
            this.toggle.innerHTML = '<i class="fas fa-moon text-yellow-400"></i>';
        } else {
            document.documentElement.classList.remove('dark');
            this.toggle.innerHTML = '<i class="fas fa-sun text-gray-600"></i>';
        }
    }

    bindEvents() {
        this.toggle.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            this.setTheme(isDark);
        });
    }
}

// Initialize
new DarkModeManager();
```

### Cart Management
```javascript
class CartManager {
    constructor() {
        this.cart = this.loadCart();
        this.updateCartCount();
    }

    loadCart() {
        return JSON.parse(localStorage.getItem('cart') || '[]');
    }

    saveCart() {
        localStorage.setItem('cart', JSON.stringify(this.cart));
        this.updateCartCount();
    }

    addItem(itemId, name, price, quantity = 1) {
        const existingItem = this.cart.find(item => item.id === itemId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.cart.push({ id: itemId, name, price, quantity });
        }
        
        this.saveCart();
        this.showNotification('Added to cart!', 'success');
    }

    removeItem(itemId) {
        this.cart = this.cart.filter(item => item.id !== itemId);
        this.saveCart();
        this.showNotification('Removed from cart!', 'info');
    }

    updateQuantity(itemId, quantity) {
        const item = this.cart.find(item => item.id === itemId);
        if (item) {
            item.quantity = quantity;
            if (quantity <= 0) {
                this.removeItem(itemId);
            } else {
                this.saveCart();
            }
        }
    }

    getTotal() {
        return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    updateCartCount() {
        const count = this.cart.reduce((total, item) => total + item.quantity, 0);
        const cartCount = document.getElementById('cartCount');
        if (cartCount) {
            cartCount.textContent = count;
            cartCount.classList.toggle('hidden', count === 0);
        }
    }

    showNotification(message, type = 'info') {
        // Custom notification implementation
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Initialize
const cartManager = new CartManager();
```

### Search Implementation
```javascript
class SearchManager {
    constructor() {
        this.searchInput = document.getElementById('searchInput');
        this.menuItems = document.querySelectorAll('.food-card');
        this.bindEvents();
    }

    bindEvents() {
        this.searchInput.addEventListener('input', (e) => {
            this.performSearch(e.target.value);
        });
    }

    performSearch(query) {
        const searchTerm = query.toLowerCase().trim();
        
        this.menuItems.forEach(item => {
            const name = item.dataset.name.toLowerCase();
            const category = item.dataset.category.toLowerCase();
            
            const matches = name.includes(searchTerm) || 
                           category.includes(searchTerm);
            
            item.style.display = matches ? 'flex' : 'none';
        });
    }
}

// Initialize
new SearchManager();
```

## 🔐 Security Implementation

### CSRF Protection
```php
// In forms
@csrf

// In AJAX requests
headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
}
```

### Input Validation
```php
// Order validation
public function store(Request $request)
{
    $validated = $request->validate([
        'items' => 'required|array|min:1',
        'items.*.id' => 'required|exists:menu_items,id',
        'items.*.quantity' => 'required|integer|min:1',
        'delivery_address' => 'required_if:order_type,delivery|string|max:500',
        'payment_method' => 'required|in:cash,transfer,wallet',
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'required|string|max:20',
    ]);

    // Process order...
}
```

### Authorization Policies
```php
// AddressPolicy
class AddressPolicy
{
    public function update(User $user, Address $address): bool
    {
        return $user->id === $address->user_id;
    }

    public function delete(User $user, Address $address): bool
    {
        return $user->id === $address->user_id;
    }
}
```

## 🧪 Testing Examples

### Feature Tests
```php
class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_order()
    {
        $user = User::factory()->create();
        $menuItem = MenuItem::factory()->create();

        $response = $this->actingAs($user)->post('/orders', [
            'items' => [
                ['id' => $menuItem->id, 'quantity' => 2]
            ],
            'delivery_address' => '123 Test Street',
            'payment_method' => 'cash',
            'customer_name' => 'John Doe',
            'customer_phone' => '1234567890'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_method' => 'cash'
        ]);
    }

    public function test_wallet_payment_debits_balance()
    {
        $user = User::factory()->create();
        $wallet = $user->wallet()->create(['balance' => 1000]);
        $menuItem = MenuItem::factory()->create(['price' => 500]);

        $response = $this->actingAs($user)->post('/orders', [
            'items' => [
                ['id' => $menuItem->id, 'quantity' => 1]
            ],
            'payment_method' => 'wallet',
            'customer_name' => 'John Doe',
            'customer_phone' => '1234567890'
        ]);

        $wallet->refresh();
        $this->assertEquals(500, $wallet->balance);
    }
}
```

### Unit Tests
```php
class WalletTest extends TestCase
{
    public function test_wallet_credit_increases_balance()
    {
        $wallet = Wallet::factory()->create(['balance' => 100]);
        
        $wallet->credit(50, 'Test credit');
        
        $this->assertEquals(150, $wallet->balance);
    }

    public function test_wallet_debit_throws_exception_on_insufficient_balance()
    {
        $wallet = Wallet::factory()->create(['balance' => 100]);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient balance');
        
        $wallet->debit(150, 'Test debit');
    }
}
```

## 🚀 Performance Optimization

### Database Queries
```php
// Eager loading to prevent N+1 queries
$orders = Order::with(['items.menuItem', 'user'])->get();

// Indexing for frequently queried columns
Schema::table('orders', function (Blueprint $table) {
    $table->index(['user_id', 'created_at']);
    $table->index('order_status');
});
```

### Caching Strategies
```php
// Cache menu items
public function getMenuItems()
{
    return Cache::remember('menu_items', 3600, function () {
        return MenuItem::with('category')->get();
    });
}

// Cache user wallet
public function getWalletBalance($userId)
{
    return Cache::remember("wallet_balance_{$userId}", 300, function () use ($userId) {
        return Wallet::where('user_id', $userId)->value('balance') ?? 0;
    });
}
```

## 🔧 Customization Guide

### Adding New Payment Methods
1. Update the `payment_method` validation in `OrderController`
2. Add payment processing logic in the `store` method
3. Update the checkout view to include the new option
4. Add any necessary database fields

### Customizing Reward System
```php
// Modify reward calculation
public function calculatePoints($orderAmount, $paymentMethod)
{
    if ($paymentMethod !== 'transfer') {
        return 0;
    }
    
    // Custom point calculation
    $basePoints = floor($orderAmount / 100);
    $bonusPoints = $orderAmount >= 5000 ? 50 : 0; // Bonus for large orders
    
    return $basePoints + $bonusPoints;
}
```

### Adding New Order Statuses
```php
// In Order model
const STATUS_PENDING = 'pending';
const STATUS_CONFIRMED = 'confirmed';
const STATUS_PREPARING = 'preparing';
const STATUS_READY = 'ready';
const STATUS_DELIVERED = 'delivered';
const STATUS_CANCELLED = 'cancelled';

// Add to fillable array
protected $fillable = [
    // ... existing fields
    'order_status'
];
```

## 📊 Monitoring & Logging

### Custom Logging
```php
// Log important events
Log::info('Order placed', [
    'order_id' => $order->id,
    'user_id' => $order->user_id,
    'amount' => $order->total_amount,
    'payment_method' => $order->payment_method
]);

// Log wallet transactions
Log::info('Wallet transaction', [
    'wallet_id' => $wallet->id,
    'type' => $transaction->type,
    'amount' => $transaction->amount,
    'balance_after' => $wallet->balance
]);
```

### Error Tracking
```php
// Custom exception handling
try {
    $wallet->debit($amount, $description);
} catch (\Exception $e) {
    Log::error('Wallet debit failed', [
        'wallet_id' => $wallet->id,
        'amount' => $amount,
        'error' => $e->getMessage()
    ]);
    
    return response()->json(['error' => 'Insufficient balance'], 400);
}
```

---

**This developer guide provides comprehensive technical documentation for the Fastify application. For additional support, refer to the main README.md file or contact the development team.** 