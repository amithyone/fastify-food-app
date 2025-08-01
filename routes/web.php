<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\PWAController;
use App\Http\Controllers\Auth\PhoneAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Custom Domain Routes (for restaurant-specific domains)
Route::middleware(['custom.domain'])->group(function () {
    Route::get('/', function (Request $request) {
        $restaurant = $request->attributes->get('restaurant');
        return app(MenuController::class)->index($restaurant->slug);
    })->name('custom.domain.menu');
    
    Route::get('/menu', function (Request $request) {
        $restaurant = $request->attributes->get('restaurant');
        return app(MenuController::class)->index($restaurant->slug);
    })->name('custom.domain.menu.index');
    
    Route::get('/cart', function (Request $request) {
        $restaurant = $request->attributes->get('restaurant');
        return app(CartController::class)->index();
    })->name('custom.domain.cart');
    
    Route::post('/cart/add', function (Request $request) {
        return app(CartController::class)->add($request);
    })->name('custom.domain.cart.add');
    
    Route::get('/checkout', function (Request $request) {
        $restaurant = $request->attributes->get('restaurant');
        return app(OrderController::class)->checkout();
    })->name('custom.domain.checkout');
});

// Include Auth Routes
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        // User is authenticated
        if (Auth::user()->isRestaurantOwner() && Auth::user()->restaurant) {
            return redirect()->route('restaurant.dashboard', Auth::user()->restaurant->slug);
        }
        return redirect('/dashboard');
    }
    // User is not authenticated, redirect to dashboard (which is now public)
    return redirect('/dashboard');
});

// Dashboard Route (accessible without login)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Cart Routes (accessible without login for in-restaurant purchases)
Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/count', [App\Http\Controllers\CartController::class, 'count'])->name('cart.count');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Orders Routes
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders', [App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
    
    // User Profile Routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// PWA Routes
Route::get('/manifest.json', [PWAController::class, 'manifest']);
Route::get('/pwa/config', [PWAController::class, 'getConfig']);
Route::get('/pwa/offline-data', [PWAController::class, 'getOfflineData']);
Route::post('/api/push-subscription', [PWAController::class, 'subscribeToPush']);
Route::post('/api/push-send', [PWAController::class, 'sendPushNotification']);

// Menu Routes
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{slug}', [MenuController::class, 'index'])->name('menu.restaurant');
Route::get('/menu/search', [MenuController::class, 'search'])->name('menu.search');
Route::get('/menu/category/{category}', [MenuController::class, 'category'])->name('menu.category');
Route::get('/menu/items', [MenuController::class, 'items'])->name('menu.items');

// Checkout Route
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout.index');

// Order Routes
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/orders/{order}/status', [OrderController::class, 'status'])->name('orders.status');

// User Orders
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/user/orders', [OrderController::class, 'userOrders'])->name('user.orders');
    Route::get('/user/orders/{order}', [OrderController::class, 'userOrderShow'])->name('user.orders.show');
});

// Wallet Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
    Route::get('/wallet/rewards', [WalletController::class, 'rewards'])->name('wallet.rewards');
    Route::post('/wallet/add-funds', [WalletController::class, 'addFunds'])->name('wallet.add-funds');
    Route::get('/wallet/info', [WalletController::class, 'info'])->name('wallet.info');
});

// Phone Authentication API Routes
Route::post('/api/phone/send-code', [PhoneAuthController::class, 'sendVerificationCode']);
Route::post('/api/phone/verify', [PhoneAuthController::class, 'verifyCode']);
Route::post('/api/phone/resend', [PhoneAuthController::class, 'resendCode']);

// Phone Authentication Web Routes
Route::get('/phone/login', [PhoneAuthController::class, 'showLoginForm'])->name('phone.login');
Route::get('/phone/register', [PhoneAuthController::class, 'showRegisterForm'])->name('phone.register');

// WhatsApp Test Route
Route::get('/test-whatsapp', function () {
    return view('test-whatsapp');
})->name('test.whatsapp');

// Email Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard')->with('success', 'Email verified successfully!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Address Routes
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::get('/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');
});

// QR Code Routes
Route::get('/qr/{code}', [OrderController::class, 'qrAccess'])->name('qr.access');
Route::post('/guest-session', [OrderController::class, 'createGuestSession'])->name('guest.session');

// Phone Verification Routes
Route::post('/verify-phone', [OrderController::class, 'verifyPhone'])->name('verify.phone');
Route::post('/verify-code', [OrderController::class, 'verifyCode'])->name('verify.code');

// Restaurant Onboarding Routes
Route::get('/restaurant/onboarding', [RestaurantController::class, 'onboarding'])->name('restaurant.onboarding');
Route::post('/restaurant/store', [RestaurantController::class, 'store'])->name('restaurant.store');

// Restaurant Management Routes
Route::middleware(['auth'])->prefix('restaurant')->group(function () {
    Route::get('/{slug}/dashboard', [RestaurantController::class, 'dashboard'])->name('restaurant.dashboard');
    Route::get('/{slug}/edit', [RestaurantController::class, 'edit'])->name('restaurant.edit');
    Route::put('/{slug}/update', [RestaurantController::class, 'update'])->name('restaurant.update');
    Route::get('/{slug}/qr-codes', [RestaurantController::class, 'qrCodes'])->name('restaurant.qr-codes');
    Route::post('/{slug}/generate-qr', [RestaurantController::class, 'generateQrCode'])->name('restaurant.generate-qr');
    Route::get('/{slug}/wallet', [RestaurantController::class, 'wallet'])->name('restaurant.wallet');
    Route::post('/{slug}/wallet/withdraw', [RestaurantController::class, 'withdraw'])->name('restaurant.wallet.withdraw');
    Route::get('/{slug}/custom-domain', [RestaurantController::class, 'customDomain'])->name('restaurant.custom-domain');
    Route::put('/{slug}/custom-domain', [RestaurantController::class, 'updateCustomDomain'])->name('restaurant.custom-domain.update');
    Route::post('/{slug}/custom-domain/verify', [RestaurantController::class, 'verifyCustomDomain'])->name('restaurant.custom-domain.verify');
    
    // Restaurant Menu Management
    Route::get('/{slug}/menu', [MenuController::class, 'restaurantIndex'])->name('restaurant.menu');
    Route::get('/{slug}/menu/create', [MenuController::class, 'restaurantCreate'])->name('restaurant.menu.create');
    Route::post('/{slug}/menu', [MenuController::class, 'restaurantStore'])->name('restaurant.menu.store');
    Route::get('/{slug}/menu/{item}/edit', [MenuController::class, 'restaurantEdit'])->name('restaurant.menu.edit');
    Route::put('/{slug}/menu/{item}', [MenuController::class, 'restaurantUpdate'])->name('restaurant.menu.update');
    Route::delete('/{slug}/menu/{item}', [MenuController::class, 'restaurantDestroy'])->name('restaurant.menu.destroy');
});

// Restaurant routes
Route::prefix('restaurant')->name('restaurant.')->group(function () {
    Route::get('/onboarding', [RestaurantController::class, 'onboarding'])->name('onboarding');
    Route::post('/store', [RestaurantController::class, 'store'])->name('store');
    Route::get('/{slug}/dashboard', [RestaurantController::class, 'dashboard'])->name('dashboard');
    Route::get('/{slug}/edit', [RestaurantController::class, 'edit'])->name('edit');
    Route::put('/{slug}/update', [RestaurantController::class, 'update'])->name('update');
    Route::get('/{slug}/qr-codes', [RestaurantController::class, 'qrCodes'])->name('qr-codes');
    Route::get('/{slug}/wallet', [RestaurantController::class, 'wallet'])->name('wallet');
    Route::post('/{slug}/wallet/withdraw', [RestaurantController::class, 'withdraw'])->name('wallet.withdraw');
    Route::get('/{slug}/custom-domain', [RestaurantController::class, 'customDomain'])->name('custom-domain');
    Route::put('/{slug}/custom-domain', [RestaurantController::class, 'updateCustomDomain'])->name('custom-domain.update');
    Route::post('/{slug}/custom-domain/verify', [RestaurantController::class, 'verifyCustomDomain'])->name('custom-domain.verify');
});

// Restaurant browsing routes
Route::prefix('restaurants')->name('restaurants.')->group(function () {
    Route::get('/all', [RestaurantController::class, 'allRestaurants'])->name('all');
    Route::get('/recent', [RestaurantController::class, 'recentRestaurants'])->name('recent');
});

// Rating Routes
Route::middleware(['auth'])->prefix('ratings')->name('ratings.')->group(function () {
    Route::post('/restaurant/{restaurantId}', [App\Http\Controllers\RatingController::class, 'store'])->name('store');
    Route::get('/restaurant/{restaurantId}/user', [App\Http\Controllers\RatingController::class, 'getUserRating'])->name('user');
    Route::get('/restaurant/{restaurantId}/all', [App\Http\Controllers\RatingController::class, 'getRestaurantRatings'])->name('all');
});

// Admin Routes (for restaurant management)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    Route::get('/orders', [OrderController::class, 'adminIndex'])->name('admin.orders');
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');
    
    Route::get('/menu', [MenuController::class, 'adminIndex'])->name('admin.menu');
    Route::post('/menu', [MenuController::class, 'store'])->name('admin.menu.store');
    Route::put('/menu/{item}', [MenuController::class, 'update'])->name('admin.menu.update');
    Route::delete('/menu/{item}', [MenuController::class, 'destroy'])->name('admin.menu.destroy');
    
    Route::get('/config', [PWAController::class, 'getConfig'])->name('admin.config');
    Route::put('/config', [PWAController::class, 'updateConfig'])->name('admin.config.update');
});
