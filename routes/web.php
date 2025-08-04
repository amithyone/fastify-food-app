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
use App\Http\Controllers\StoryController;
use App\Http\Controllers\AIMenuController;
use App\Http\Controllers\PayVibeController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\RestaurantStatusController;
use App\Http\Controllers\RestaurantDeliverySettingController;
use App\Http\Controllers\BankTransferPaymentController;
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

// Splash Screen Route
Route::get('/splash', function () {
    return view('splash');
})->name('splash');

// Serve storage images
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    
    if (!file_exists($filePath)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($filePath);
    
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
        'Access-Control-Allow-Origin' => '*'
    ]);
})->where('path', '.*')->name('storage.serve');

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
Route::get('/track-order', function () {
    return view('orders.track-form');
})->name('orders.track-form');
Route::post('/track-order', [OrderController::class, 'searchByTrackingCode'])->name('orders.track');

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
Route::get('/qr-image/{code}', [OrderController::class, 'qrImage'])->name('qr.image');

// Test route for storage
Route::get('/test-storage', function() {
    $testFile = 'test.txt';
    $content = 'Test file created at ' . now();
    
    try {
        Storage::disk('public')->put($testFile, $content);
        $url = Storage::disk('public')->url($testFile);
        $exists = Storage::disk('public')->exists($testFile);
        
        return response()->json([
            'success' => true,
            'file_created' => $exists,
            'url' => $url,
            'storage_path' => storage_path('app/public'),
            'public_path' => public_path('storage'),
            'link_exists' => file_exists(public_path('storage')),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Test route for image upload (no auth required)
Route::post('/test-image-upload', function(\Illuminate\Http\Request $request) {
    try {
        if (!$request->hasFile('image')) {
            return response()->json(['success' => false, 'message' => 'No image file provided']);
        }
        
        $file = $request->file('image');
        $path = $file->store('menu-items', 'public');
        
        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully',
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'file_mime' => $file->getMimeType()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Upload failed: ' . $e->getMessage()
        ]);
    }
});

// Test route for menu item creation (no auth required)
Route::post('/test-menu-item', function(\Illuminate\Http\Request $request) {
    try {
        // Auto-login as chef for testing
        $user = \App\Models\User::where('email', 'chef@tasteofabuja.com')->first();
        if ($user) {
            auth()->login($user);
        }
        
        $restaurant = \App\Models\Restaurant::where('slug', 'taste-of-abuja')->first();
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found']);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'is_available' => 'nullable',
            'is_featured' => 'nullable',
            'is_vegetarian' => 'nullable',
            'is_spicy' => 'nullable',
            'ingredients' => 'nullable|string|max:500',
            'allergens' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $validated['restaurant_id'] = $restaurant->id;
        
        // Set default category if none provided
        if (empty($validated['category_id'])) {
            $defaultCategory = \App\Models\Category::where('restaurant_id', $restaurant->id)->first();
            if ($defaultCategory) {
                $validated['category_id'] = $defaultCategory->id;
            }
        }
        
        // Set default values for boolean fields
        $validated['is_available'] = $request->has('is_available') || $request->input('is_available') === 'on';
        $validated['is_featured'] = $request->has('is_featured') || $request->input('is_featured') === 'on';
        $validated['is_vegetarian'] = $request->has('is_vegetarian') || $request->input('is_vegetarian') === 'on';
        $validated['is_spicy'] = $request->has('is_spicy') || $request->input('is_spicy') === 'on';
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }
        
        $menuItem = \App\Models\MenuItem::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Menu item created successfully!',
            'menu_item' => $menuItem
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create menu item: ' . $e->getMessage()
        ]);
    }
});

// Quick login test route
Route::get('/quick-login', function() {
    $user = \App\Models\User::where('email', 'chef@tasteofabuja.com')->first();
    if ($user) {
        auth()->login($user);
        return response()->json([
            'success' => true,
            'message' => 'Logged in as ' . $user->name,
            'user_id' => $user->id,
            'user_name' => $user->name
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Chef user not found'
        ]);
    }
});
Route::post('/test-story-creation', function () {
    try {
        $restaurant = \App\Models\Restaurant::first();
        $story = \App\Models\Story::create([
            'restaurant_id' => $restaurant->id,
            'type' => 'test',
            'title' => 'Test Story',
            'content' => 'This is a test story',
            'is_active' => true,
            'sort_order' => 0
        ]);
        return response()->json(['success' => true, 'story_id' => $story->id]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
})->name('test.story.creation');

// Test AI recognition
Route::post('/test-ai-recognition', function (Request $request) {
    try {
        $aiService = app(\App\Services\AIFoodRecognitionService::class);
        $result = $aiService->recognizeFood($request->file('image'));
        return response()->json($result);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('test.ai.recognition');

// Test storage functionality
Route::get('/test-storage', function () {
    try {
        $restaurant = \App\Models\Restaurant::first();
        if (!$restaurant) {
            return response()->json(['error' => 'No restaurant found']);
        }
        
        $logoPath = $restaurant->logo;
        $logoExists = \Storage::disk('public')->exists($logoPath);
        $logoUrl = $logoExists ? \Storage::disk('public')->url($logoPath) : null;
        
        return response()->json([
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            'logo_path' => $logoPath,
            'logo_exists' => $logoExists,
            'logo_url' => $logoUrl,
            'storage_url' => \Storage::disk('public')->url(''),
            'app_url' => config('app.url'),
            'filesystem_disk' => config('filesystems.default'),
            'public_disk_url' => config('filesystems.disks.public.url'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('test.storage');

// Test image serving
Route::get('/test-image/{filename}', function ($filename) {
    try {
        $path = 'restaurants/logos/' . $filename;
        if (\Storage::disk('public')->exists($path)) {
            $url = \Storage::disk('public')->url($path);
            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => $url,
                'exists' => true,
                'size' => \Storage::disk('public')->size($path),
                'mime_type' => \Storage::disk('public')->mimeType($path)
            ]);
        }
        return response()->json(['success' => false, 'error' => 'File not found']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
})->name('test.image');

// Test logo display
Route::get('/test-logo', function () {
    $restaurant = \App\Models\Restaurant::first();
    if (!$restaurant) {
        return 'No restaurant found';
    }
    
    $logoUrl = $restaurant->logo_url;
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Logo Test</title>
    </head>
    <body>
        <h1>Logo Test</h1>
        <p>Logo URL: {$logoUrl}</p>
        <p>Logo Path: {$restaurant->logo}</p>
        <p>File Exists: " . (\Storage::disk('public')->exists($restaurant->logo) ? 'Yes' : 'No') . "</p>
        <img src='{$logoUrl}' alt='Restaurant Logo' style='width: 100px; height: 100px; border: 1px solid red;'>
        <br>
        <img src='{$logoUrl}' alt='Restaurant Logo' style='width: 200px; height: 200px; border: 1px solid blue;'>
    </body>
    </html>
    ";
    
    return $html;
})->name('test.logo');

// Test Google Vision API key
Route::get('/test-google-vision', function () {
    try {
        $apiKey = config('services.google_vision.api_key');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'Google Vision API key not found in configuration',
                'config_key' => 'services.google_vision.api_key',
                'env_key' => 'GOOGLE_VISION_API_KEY'
            ]);
        }
        
        // Test with a simple image (base64 encoded small test image)
        $testImageData = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='; // 1x1 pixel
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://vision.googleapis.com/v1/images:annotate?key={$apiKey}", [
            'requests' => [
                [
                    'image' => [
                        'content' => $testImageData
                    ],
                    'features' => [
                        [
                            'type' => 'LABEL_DETECTION',
                            'maxResults' => 1
                        ]
                    ]
                ]
            ]
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            return response()->json([
                'success' => true,
                'message' => 'Google Vision API key is working!',
                'api_key_length' => strlen($apiKey),
                'api_key_preview' => substr($apiKey, 0, 10) . '...',
                'response_status' => $response->status(),
                'response_data' => $data
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Google Vision API request failed',
                'status_code' => $response->status(),
                'response' => $response->json(),
                'api_key_length' => strlen($apiKey),
                'api_key_preview' => substr($apiKey, 0, 10) . '...'
            ]);
        }
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Exception occurred: ' . $e->getMessage(),
            'api_key_length' => $apiKey ? strlen($apiKey) : 0,
            'api_key_preview' => $apiKey ? (substr($apiKey, 0, 10) . '...') : 'Not set'
        ]);
    }
})->name('test.google.vision');

Route::post('/guest-session', [OrderController::class, 'createGuestSession'])->name('guest.session');

// Phone Verification Routes
Route::post('/verify-phone', [OrderController::class, 'verifyPhone'])->name('verify.phone');
Route::post('/verify-code', [OrderController::class, 'verifyCode'])->name('verify.code');

// Restaurant Onboarding Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/restaurant/onboarding', [RestaurantController::class, 'onboarding'])->name('restaurant.onboarding');
    Route::post('/restaurant/store', [RestaurantController::class, 'store'])->name('restaurant.store');
});

// Restaurant Management Routes (Protected by auth middleware)
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
    
    // Restaurant Category Management
    Route::post('/{slug}/categories', [MenuController::class, 'storeCategory'])->name('restaurant.categories.store');
    Route::put('/{slug}/categories/{category}', [MenuController::class, 'updateCategory'])->name('restaurant.categories.update');
    Route::delete('/{slug}/categories/{category}', [MenuController::class, 'destroyCategory'])->name('restaurant.categories.destroy');
    
    // Restaurant Order Management
    Route::get('/{slug}/orders', [OrderController::class, 'restaurantOrders'])->name('restaurant.orders');
    Route::get('/{slug}/orders/{order}', [OrderController::class, 'restaurantOrderShow'])->name('restaurant.orders.show');
    Route::put('/{slug}/orders/{order}/status', [OrderController::class, 'restaurantOrderStatus'])->name('restaurant.orders.status');
    
    // Restaurant Order Tracking
    Route::get('/{slug}/track', [OrderController::class, 'restaurantTrackForm'])->name('restaurant.track-form');
    Route::post('/{slug}/track', [OrderController::class, 'restaurantTrackOrder'])->name('restaurant.track');
    
    // Restaurant Stories Management
    Route::get('/{slug}/stories', [StoryController::class, 'restaurantIndex'])->name('restaurant.stories');
    Route::post('/{slug}/stories', [StoryController::class, 'restaurantStore'])->name('restaurant.stories.store');
    Route::get('/{slug}/stories/{story}/edit', [StoryController::class, 'restaurantEdit'])->name('restaurant.stories.edit');
    Route::put('/{slug}/stories/{story}', [StoryController::class, 'restaurantUpdate'])->name('restaurant.stories.update');
    Route::delete('/{slug}/stories/{story}', [StoryController::class, 'restaurantDestroy'])->name('restaurant.stories.destroy');
    Route::post('/{slug}/stories/{story}/toggle', [StoryController::class, 'restaurantToggleStatus'])->name('restaurant.stories.toggle');
    
    // AI Menu Management Routes
    Route::post('/{slug}/ai/recognize', [AIMenuController::class, 'recognizeFood'])->name('restaurant.ai.recognize');
    Route::post('/{slug}/ai/store', [AIMenuController::class, 'storeMenuItem'])->name('restaurant.ai.store');
    Route::get('/{slug}/ai/categories', [AIMenuController::class, 'getCategories'])->name('restaurant.ai.categories');
    Route::post('/{slug}/ai/correct', [AIMenuController::class, 'correctRecognition'])->name('restaurant.ai.correct');
    
    // Promotion Management Routes
    Route::get('/{slug}/promotions', [PromotionController::class, 'index'])->name('restaurant.promotions');
    Route::get('/{slug}/promotions/{planId}', [PromotionController::class, 'show'])->name('restaurant.promotions.show');
    Route::post('/{slug}/promotions/payment', [PromotionController::class, 'createPayment'])->name('restaurant.promotions.payment');
    Route::get('/{slug}/promotions/payment/{paymentId}', [PromotionController::class, 'payment'])->name('restaurant.promotions.payment.show');
    Route::get('/{slug}/promotions/payment/{paymentId}/payvibe', [PromotionController::class, 'payvibePayment'])->name('restaurant.promotions.payment.payvibe');
    Route::get('/{slug}/promotions/payment/{paymentId}/virtual-account', [PromotionController::class, 'virtualAccountPayment'])->name('restaurant.promotions.payment.virtual-account');
    Route::get('/{slug}/promotions/payment/{paymentId}/status', [PromotionController::class, 'checkPaymentStatus'])->name('restaurant.promotions.payment.status');
    
    // PayVibe Payment Routes
    Route::post('/{slug}/payvibe/initialize', [PayVibeController::class, 'initializePayment'])->name('restaurant.payvibe.initialize');
    Route::post('/{slug}/payvibe/virtual-account', [PayVibeController::class, 'generateVirtualAccount'])->name('restaurant.payvibe.virtual-account');
    Route::get('/payvibe/callback', [PayVibeController::class, 'callback'])->name('payvibe.callback');
    Route::get('/payvibe/verify/{reference}', [PayVibeController::class, 'verifyPayment'])->name('payvibe.verify');
    Route::get('/{slug}/promotions/analytics', [PromotionController::class, 'analytics'])->name('restaurant.promotions.analytics');
    
    // Restaurant Delivery Settings Routes
Route::get('/{slug}/delivery-settings', [RestaurantDeliverySettingController::class, 'index'])->name('restaurant.delivery-settings.index');
Route::put('/{slug}/delivery-settings', [RestaurantDeliverySettingController::class, 'update'])->name('restaurant.delivery-settings.update');
Route::post('/{slug}/delivery-settings/menu-items', [RestaurantDeliverySettingController::class, 'updateMenuItemDeliveryMethods'])->name('restaurant.delivery-settings.menu-items');

// API Routes for Delivery Settings
Route::get('/{slug}/api/delivery-settings', [RestaurantDeliverySettingController::class, 'apiIndex'])->name('restaurant.api.delivery-settings');
Route::get('/{slug}/api/menu-item-delivery-methods', [RestaurantDeliverySettingController::class, 'apiMenuItemDeliveryMethods'])->name('restaurant.api.menu-item-delivery-methods');
Route::get('/{slug}/api/menu-items/{menuItemId}/availability/{deliveryMethod}', [RestaurantDeliverySettingController::class, 'checkMenuItemAvailability'])->name('restaurant.api.menu-item-availability');

// Restaurant Status Management Routes
Route::get('/{slug}/status', [RestaurantStatusController::class, 'index'])->name('restaurant.status.index');
Route::post('/{slug}/status/toggle', [RestaurantStatusController::class, 'toggleStatus'])->name('restaurant.status.toggle');
Route::post('/{slug}/status/open', [RestaurantStatusController::class, 'open'])->name('restaurant.status.open');
Route::post('/{slug}/status/close', [RestaurantStatusController::class, 'close'])->name('restaurant.status.close');
Route::post('/{slug}/status/business-hours', [RestaurantStatusController::class, 'updateBusinessHours'])->name('restaurant.status.business-hours');
Route::get('/{slug}/status/get', [RestaurantStatusController::class, 'getStatus'])->name('restaurant.status.get');
});

// Bank Transfer Payment Routes (allow guest users)
Route::prefix('bank-transfer')->name('bank-transfer.')->group(function () {
    Route::post('/initialize', [BankTransferPaymentController::class, 'initialize'])->name('initialize');
    Route::get('/status/{paymentId}', [BankTransferPaymentController::class, 'status'])->name('status');
    Route::post('/generate-new-account/{paymentId}', [BankTransferPaymentController::class, 'generateNewAccount'])->name('generate-new-account');
    Route::get('/user-payments', [BankTransferPaymentController::class, 'userPayments'])->name('user-payments');
});

// Guest Session Routes (allow guest users)
Route::prefix('guest-session')->name('guest-session.')->group(function () {
    Route::get('/{sessionId}', [GuestSessionController::class, 'show'])->name('show');
    Route::get('/{sessionId}/orders', [GuestSessionController::class, 'orders'])->name('orders');
});

// WhatsApp Webhook Routes
Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
    Route::get('/webhook', [WhatsAppWebhookController::class, 'verify'])->name('verify');
    Route::post('/webhook', [WhatsAppWebhookController::class, 'webhook'])->name('webhook');
});

// Manager Registration Routes
Route::get('/register/manager', [App\Http\Controllers\Auth\ManagerRegistrationController::class, 'showRegistrationForm'])->name('manager.register');
Route::post('/register/manager', [App\Http\Controllers\Auth\ManagerRegistrationController::class, 'register']);

// User Upgrade Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/upgrade-to-manager', [App\Http\Controllers\Auth\UserUpgradeController::class, 'showUpgradeForm'])->name('user.upgrade.form');
    Route::post('/upgrade-to-manager', [App\Http\Controllers\Auth\UserUpgradeController::class, 'upgrade'])->name('user.upgrade');
});

// Manager Dashboard Routes
Route::middleware(['auth'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/verification-status', [ManagerDashboardController::class, 'verificationStatus'])->name('verification-status');
    Route::get('/create-restaurant', [ManagerDashboardController::class, 'createRestaurant'])->name('create-restaurant');
    Route::post('/create-restaurant', [ManagerDashboardController::class, 'storeRestaurant'])->name('store-restaurant');
});

// PayVibe Webhook Route (no auth required)
Route::post('/webhook/bank-transfer', [BankTransferPaymentController::class, 'webhook'])->name('webhook.bank-transfer');

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
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    Route::get('/restaurants', [RestaurantController::class, 'adminIndex'])->name('admin.restaurants');
    Route::get('/restaurants/{restaurant}/edit', [RestaurantController::class, 'adminEdit'])->name('admin.restaurants.edit');
    Route::put('/restaurants/{restaurant}', [RestaurantController::class, 'adminUpdate'])->name('admin.restaurants.update');
    Route::delete('/restaurants/{restaurant}', [RestaurantController::class, 'adminDestroy'])->name('admin.restaurants.destroy');
    
    Route::get('/orders', [OrderController::class, 'adminIndex'])->name('admin.orders');
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');
    
    Route::get('/menu', [MenuController::class, 'adminIndex'])->name('admin.menu');
    Route::post('/menu', [MenuController::class, 'store'])->name('admin.menu.store');
    Route::put('/menu/{item}', [MenuController::class, 'update'])->name('admin.menu.update');
    Route::delete('/menu/{item}', [MenuController::class, 'destroy'])->name('admin.menu.destroy');
    
    Route::get('/config', [PWAController::class, 'getConfig'])->name('admin.config');
    Route::put('/config', [PWAController::class, 'updateConfig'])->name('admin.config.update');
    
    // Admin Promotion Management
Route::get('/promotions', [PromotionController::class, 'adminIndex'])->name('admin.promotions');
Route::get('/promotions/payments', [PromotionController::class, 'adminPayments'])->name('admin.promotions.payments');
Route::post('/promotions/payments/{paymentId}/mark-paid', [PromotionController::class, 'markAsPaid'])->name('admin.promotions.payments.mark-paid');

// Admin Payment Settings Management
Route::get('/payment-settings', [App\Http\Controllers\Admin\PaymentSettingController::class, 'index'])->name('admin.payment-settings.index');
Route::get('/payment-settings/create', [App\Http\Controllers\Admin\PaymentSettingController::class, 'create'])->name('admin.payment-settings.create');
Route::post('/payment-settings', [App\Http\Controllers\Admin\PaymentSettingController::class, 'store'])->name('admin.payment-settings.store');
Route::get('/payment-settings/{paymentSetting}/edit', [App\Http\Controllers\Admin\PaymentSettingController::class, 'edit'])->name('admin.payment-settings.edit');
Route::put('/payment-settings/{paymentSetting}', [App\Http\Controllers\Admin\PaymentSettingController::class, 'update'])->name('admin.payment-settings.update');
Route::delete('/payment-settings/{paymentSetting}', [App\Http\Controllers\Admin\PaymentSettingController::class, 'destroy'])->name('admin.payment-settings.destroy');
Route::patch('/payment-settings/{paymentSetting}/toggle', [App\Http\Controllers\Admin\PaymentSettingController::class, 'toggleStatus'])->name('admin.payment-settings.toggle');
Route::post('/payment-settings/test-calculation', [App\Http\Controllers\Admin\PaymentSettingController::class, 'testCalculation'])->name('admin.payment-settings.test');
});
