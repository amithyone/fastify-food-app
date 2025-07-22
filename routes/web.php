<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\PWAController;
use App\Http\Controllers\Auth\PhoneAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    return redirect('/menu');
});

// PWA Routes
Route::get('/manifest.json', [PWAController::class, 'manifest']);
Route::get('/pwa/config', [PWAController::class, 'getConfig']);
Route::get('/pwa/offline-data', [PWAController::class, 'getOfflineData']);
Route::post('/api/push-subscription', [PWAController::class, 'subscribeToPush']);
Route::post('/api/push-send', [PWAController::class, 'sendPushNotification']);

// Menu Routes
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/search', [MenuController::class, 'search'])->name('menu.search');
Route::get('/menu/category/{category}', [MenuController::class, 'category'])->name('menu.category');
Route::get('/menu/items', [MenuController::class, 'items'])->name('menu.items');

// Cart Routes
Route::get('/cart', [OrderController::class, 'cart'])->name('cart.index');
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout.index');
Route::post('/cart/add', [OrderController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/update', [OrderController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove', [OrderController::class, 'removeFromCart'])->name('cart.remove');

// Order Routes
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/orders/{order}/status', [OrderController::class, 'status'])->name('orders.status');

// User Orders
Route::middleware(['auth'])->group(function () {
    Route::get('/user/orders', [OrderController::class, 'userOrders'])->name('user.orders');
    Route::get('/user/orders/{order}', [OrderController::class, 'userOrderShow'])->name('user.orders.show');
});

// Wallet Routes
Route::middleware(['auth'])->group(function () {
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

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Email Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/profile');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// Logout Route
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

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
