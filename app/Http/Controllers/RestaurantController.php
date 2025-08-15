<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RestaurantController extends Controller
{
    public function onboarding()
    {
        // Add debugging
        \Log::info('Restaurant onboarding accessed', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email ?? 'not authenticated',
            'url' => request()->url()
        ]);

        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please log in to create a restaurant.');
        }

        // Allow users to add multiple restaurants
        // No restriction on existing restaurants
        
        return view('restaurant.onboarding');
    }

    public function store(Request $request)
    {
        // Add debugging
        \Log::info('Restaurant store method called', [
            'method' => $request->method(),
            'url' => $request->url(),
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email ?? 'not authenticated',
            'request_data' => $request->all()
        ]);

        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please log in to create a restaurant.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'whatsapp_number' => 'required|string|max:20',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'currency' => 'required|string|max:10',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'business_days' => 'nullable|array',
            'business_days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        try {
            // Generate unique slug
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $counter = 1;
            
            while (Restaurant::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Handle logo upload
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('restaurants/logos', 'public');
            }

            // Handle banner upload
            $bannerPath = null;
            if ($request->hasFile('banner')) {
                $bannerPath = $request->file('banner')->store('restaurants/banners', 'public');
            }

            // Build business hours based on selected days
            $businessHours = [];
            $selectedDays = $request->business_days ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            
            foreach ($selectedDays as $day) {
                $businessHours[$day] = ['open' => '09:00', 'close' => '22:00'];
            }

            // Create restaurant with owner_id
            $restaurant = Restaurant::create([
                'owner_id' => Auth::id(), // Set owner_id during creation
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'whatsapp_number' => $request->whatsapp_number,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country ?? 'Nigeria',
                'currency' => $request->currency,
                'logo' => $logoPath,
                'banner_image' => $bannerPath,
                'theme_color' => '#f97316', // Default orange
                'secondary_color' => '#ea580c', // Default dark orange
                'is_active' => true,
                'is_verified' => false, // Will be verified by admin
                'business_hours' => $businessHours,
                'settings' => [
                    'delivery_enabled' => true,
                    'dine_in_enabled' => true,
                    'delivery_fee' => 500, // in kobo
                    'minimum_order' => 1000, // in kobo
                    'auto_accept_orders' => false,
                ],
            ]);

            // Create manager record for the restaurant owner
            \App\Models\Manager::create([
                'user_id' => Auth::id(),
                'restaurant_id' => $restaurant->id,
                'role' => 'owner',
                'is_active' => true,
                'permissions' => ['*'], // Owner has all permissions
            ]);

            return redirect()->route('restaurant.dashboard', $restaurant->slug)
                ->with('success', 'Restaurant created successfully! You can now start adding your menu items.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create restaurant. Please try again.']);
        }
    }

    public function dashboard($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Debug information
        \Log::info('Dashboard access attempt', [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name,
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            'is_authenticated' => Auth::check(),
            'is_admin' => Auth::user()?->isAdmin(),
        ]);
        
        // Check if user has access to this restaurant
        if (Auth::check()) {
            // Check if user is a manager of this restaurant
            $canAccess = \App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager');
            $isAdmin = Auth::user()->isAdmin();
            
            \Log::info('Authorization check', [
                'can_access' => $canAccess,
                'is_admin' => $isAdmin,
                'user_id' => Auth::id(),
                'restaurant_id' => $restaurant->id,
            ]);
            
            if (!$canAccess && !$isAdmin) {
                abort(403, 'Unauthorized access to restaurant dashboard. You need manager privileges.');
            }
            
            // Update last access time
            $manager = \App\Models\Manager::where('user_id', Auth::id())
                ->where('restaurant_id', $restaurant->id)
                ->first();
            if ($manager) {
                $manager->updateLastAccess();
            }
        } else {
            // Redirect to login if not authenticated
            return redirect()->route('login')->with('error', 'Please login to access the restaurant dashboard.');
        }

        // Calculate today's earnings (confirmed orders from today)
        $todayEarnings = $restaurant->orders()
            ->where('status', 'confirmed')
            ->whereDate('created_at', today())
            ->sum('total_amount');

        // Calculate today's earnings for pay-on-delivery orders
        $payOnDeliveryEarnings = $restaurant->orders()
            ->where('payment_method', 'cash')
            ->where('status', 'confirmed')
            ->whereDate('created_at', today())
            ->sum('total_amount');

        // Calculate today's earnings for non-pay-on-delivery orders (card, transfer, wallet)
        $nonPayOnDeliveryEarnings = $restaurant->orders()
            ->whereIn('payment_method', ['card', 'transfer', 'wallet'])
            ->where('status', 'confirmed')
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $stats = [
            'total_orders' => $restaurant->orders()->count(),
            'pending_orders' => $restaurant->orders()->where('status', 'pending')->count(),
            'total_menu_items' => $restaurant->menuItems()->count(),
            'today_earnings' => $todayEarnings,
            'pay_on_delivery_earnings' => $payOnDeliveryEarnings,
            'non_pay_on_delivery_earnings' => $nonPayOnDeliveryEarnings,
        ];

        $recent_orders = $restaurant->orders()->with('orderItems')->latest()->take(5)->get();

        return view('restaurant.dashboard', compact('restaurant', 'stats', 'recent_orders'));
    }

    public function edit($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        \Log::info('Restaurant edit access attempt', [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name,
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            'is_authenticated' => Auth::check(),
        ]);
        
        if (Auth::check()) {
            $canAccess = \App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager');
            $isAdmin = Auth::user()->isAdmin();
            
            \Log::info('Restaurant edit authorization check', [
                'can_access' => $canAccess,
                'is_admin' => $isAdmin,
                'user_id' => Auth::id(),
                'restaurant_id' => $restaurant->id,
            ]);
            
            if (!$canAccess && !$isAdmin) {
                abort(403, 'Unauthorized access to restaurant edit. You need manager privileges.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to access the restaurant edit page.');
        }

        return view('restaurant.edit', compact('restaurant'));
    }

    public function update(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check()) {
            // Check if user is a manager of this restaurant
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant dashboard. You need manager privileges.');
            }
        } else {
            // Redirect to login if not authenticated
            return redirect()->route('login')->with('error', 'Please login to access the restaurant dashboard.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|regex:/^[a-z0-9-]+$/|unique:restaurants,slug,' . $restaurant->id,
            'description' => 'nullable|string',
            'whatsapp_number' => 'required|string|max:20',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
            'currency' => 'required|string|max:10',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'theme_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'business_hours' => 'nullable|array',
            'settings' => 'nullable|array',
            'custom_domain' => 'nullable|string|max:255|unique:restaurants,custom_domain,' . $restaurant->id,
        ]);

        try {
            // Handle logo upload
            if ($request->hasFile('logo')) {
                \Log::info('Logo upload detected', [
                    'file_name' => $request->file('logo')->getClientOriginalName(),
                    'file_size' => $request->file('logo')->getSize(),
                    'file_mime' => $request->file('logo')->getMimeType(),
                ]);
                
                // Delete old logo
                if ($restaurant->logo) {
                    Storage::disk('public')->delete($restaurant->logo);
                    \Log::info('Old logo deleted', ['old_path' => $restaurant->logo]);
                }
                
                $logoPath = $request->file('logo')->store('restaurants/logos', 'public');
                $restaurant->logo = $logoPath;
                
                \Log::info('Logo uploaded successfully', [
                    'new_path' => $logoPath,
                    'full_url' => Storage::disk('public')->url($logoPath),
                    'file_exists' => Storage::disk('public')->exists($logoPath)
                ]);
            }

            // Handle banner upload
            if ($request->hasFile('banner_image')) {
                \Log::info('Banner upload detected', [
                    'file_name' => $request->file('banner_image')->getClientOriginalName(),
                    'file_size' => $request->file('banner_image')->getSize(),
                    'file_mime' => $request->file('banner_image')->getMimeType(),
                ]);
                
                // Delete old banner
                if ($restaurant->banner_image) {
                    Storage::disk('public')->delete($restaurant->banner_image);
                    \Log::info('Old banner deleted', ['old_path' => $restaurant->banner_image]);
                }
                
                $bannerPath = $request->file('banner_image')->store('restaurants/banners', 'public');
                $restaurant->banner_image = $bannerPath;
                
                \Log::info('Banner uploaded successfully', [
                    'new_path' => $bannerPath,
                    'full_url' => Storage::disk('public')->url($bannerPath),
                    'file_exists' => Storage::disk('public')->exists($bannerPath)
                ]);
            }

            // Check if slug is being changed
            $slugChanged = false;
            if ($request->slug !== $restaurant->slug) {
                $slugChanged = true;
                \Log::info('Restaurant slug being changed', [
                    'restaurant_id' => $restaurant->id,
                    'old_slug' => $restaurant->slug,
                    'new_slug' => $request->slug,
                    'user_id' => Auth::id()
                ]);
            }

            // Update restaurant
            $updateData = [
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'whatsapp_number' => $request->whatsapp_number,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'currency' => $request->currency,
                'theme_color' => $request->theme_color,
                'secondary_color' => $request->secondary_color,
                'business_hours' => $request->business_hours ?? $restaurant->business_hours,
                'settings' => $request->settings ?? $restaurant->settings,
            ];

            // Handle custom domain changes
            if ($request->has('custom_domain')) {
                $newDomain = $request->custom_domain ? trim($request->custom_domain) : null;
                
                // If domain changed, reset verification status
                if ($newDomain !== $restaurant->custom_domain) {
                    $updateData['custom_domain'] = $newDomain;
                    $updateData['custom_domain_verified'] = false;
                    $updateData['custom_domain_verified_at'] = null;
                    $updateData['custom_domain_status'] = $newDomain ? 'pending' : null;
                }
            }

            $restaurant->update($updateData);

            // Prepare success message
            $successMessage = 'Restaurant updated successfully!';
            if ($slugChanged) {
                $successMessage .= ' Your restaurant slug has changed. Please regenerate your QR codes as the old ones will no longer work.';
            }

            return redirect()->route('restaurant.dashboard', $restaurant->slug)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update restaurant. Please try again.']);
        }
    }

    public function qrCodes($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        \Log::info('QR codes access attempt', [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name,
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            'is_authenticated' => Auth::check(),
        ]);
        
        if (Auth::check()) {
            $canAccess = \App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager');
            $isAdmin = Auth::user()->isAdmin();
            
            \Log::info('QR codes authorization check', [
                'can_access' => $canAccess,
                'is_admin' => $isAdmin,
                'user_id' => Auth::id(),
                'restaurant_id' => $restaurant->id,
            ]);
            
            if (!$canAccess && !$isAdmin) {
                abort(403, 'Unauthorized access to QR codes. You need manager privileges.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to access the QR codes.');
        }

        $qrCodes = $restaurant->tableQrs()->get();

        return view('restaurant.qr-codes', compact('restaurant', 'qrCodes'));
    }

    public function generateQrCode(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        \Log::info('Generate QR code access attempt', [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name,
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            'is_authenticated' => Auth::check(),
        ]);
        
        if (Auth::check()) {
            $canAccess = \App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager');
            $isAdmin = Auth::user()->isAdmin();
            
            \Log::info('Generate QR code authorization check', [
                'can_access' => $canAccess,
                'is_admin' => $isAdmin,
                'user_id' => Auth::id(),
                'restaurant_id' => $restaurant->id,
            ]);
            
            if (!$canAccess && !$isAdmin) {
                abort(403, 'Unauthorized access to generate QR codes. You need manager privileges.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to generate QR codes.');
        }

        $request->validate([
            'table_number' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        // Generate QR code data
        $qrCodeData = url('/qr/' . $restaurant->slug . '_table_' . $request->table_number);
        
        // Generate QR code image
        $qrCodeImage = \QrCode::format('png')
            ->size(300)
            ->margin(10)
            ->generate($qrCodeData);

        // Save QR code image to storage
        $imagePath = 'qr-codes/' . $restaurant->slug . '_table_' . $request->table_number . '.png';
        Storage::disk('public')->put($imagePath, $qrCodeImage);

        $qrCode = $restaurant->tableQrs()->create([
            'table_number' => $request->table_number,
            'description' => $request->description,
            'qr_code' => $restaurant->slug . '_table_' . $request->table_number,
            'short_url' => Str::random(8),
            'qr_image' => $imagePath,
            'is_active' => true,
        ]);

        return redirect()->route('restaurant.qr-codes', $restaurant->slug)
            ->with('success', 'QR Code generated successfully!');
    }

    public function wallet($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        \Log::info('Wallet access attempt', [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name,
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            'is_authenticated' => Auth::check(),
        ]);
        
        if (Auth::check()) {
            $canAccess = \App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager');
            $isAdmin = Auth::user()->isAdmin();
            
            \Log::info('Wallet authorization check', [
                'can_access' => $canAccess,
                'is_admin' => $isAdmin,
                'user_id' => Auth::id(),
                'restaurant_id' => $restaurant->id,
            ]);
            
            if (!$canAccess && !$isAdmin) {
                abort(403, 'Unauthorized access to restaurant wallet. You need manager privileges.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to access the restaurant wallet.');
        }

        // Get or create restaurant wallet
        $wallet = $restaurant->wallet;
        if (!$wallet) {
            $wallet = $restaurant->wallet()->create([
                'balance' => 0,
                'currency' => $restaurant->currency,
                'is_active' => true,
            ]);
        }

        // Get recent transactions
        $transactions = $wallet->transactions()->latest()->take(10)->get();

        // Calculate stats
        $totalEarnings = $wallet->transactions()->where('type', 'credit')->sum('amount');
        $totalWithdrawals = $wallet->transactions()->where('type', 'debit')->sum('amount');
        $pendingAmount = $wallet->transactions()->where('status', 'pending')->sum('amount');

        $stats = [
            'current_balance' => $wallet->balance,
            'total_earnings' => $totalEarnings,
            'total_withdrawals' => $totalWithdrawals,
            'pending_amount' => $pendingAmount,
            'total_transactions' => $wallet->transactions()->count(),
        ];

        return view('restaurant.wallet', compact('restaurant', 'wallet', 'transactions', 'stats'));
    }

    public function withdraw(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        \Log::info('Withdraw access attempt', [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name,
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            'is_authenticated' => Auth::check(),
        ]);
        
        if (Auth::check()) {
            $canAccess = \App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager');
            $isAdmin = Auth::user()->isAdmin();
            
            \Log::info('Withdraw authorization check', [
                'can_access' => $canAccess,
                'is_admin' => $isAdmin,
                'user_id' => Auth::id(),
                'restaurant_id' => $restaurant->id,
            ]);
            
            if (!$canAccess && !$isAdmin) {
                abort(403, 'Unauthorized access to withdraw funds. You need manager privileges.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to withdraw funds.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ]);

        $wallet = $restaurant->wallet;
        $amount = $request->amount * 100; // Convert to cents

        if ($amount > $wallet->balance) {
            return back()->withErrors(['amount' => 'Insufficient balance for withdrawal.']);
        }

        try {
            // Create withdrawal transaction
            $wallet->transactions()->create([
                'type' => 'debit',
                'amount' => $amount,
                'status' => 'pending',
                'description' => 'Withdrawal request to ' . $request->bank_name,
                'metadata' => [
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'account_name' => $request->account_name,
                ],
            ]);

            // Update wallet balance
            $wallet->update([
                'balance' => $wallet->balance - $amount
            ]);

            return redirect()->route('restaurant.wallet', $restaurant->slug)
                ->with('success', 'Withdrawal request submitted successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to process withdrawal request. Please try again.']);
        }
    }

    public function customDomain($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check()) {
            // Check if user is the owner of this restaurant
            if ($restaurant->owner_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant dashboard.');
            }
        } else {
            // Allow access for non-authenticated users (for demo purposes)
            // In production, you might want to restrict this
        }

        return view('restaurant.custom-domain', compact('restaurant'));
    }

    public function updateCustomDomain(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check()) {
            // Check if user is the owner of this restaurant
            if ($restaurant->owner_id !== Auth::id() && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant dashboard.');
            }
        } else {
            // Allow access for non-authenticated users (for demo purposes)
            // In production, you might want to restrict this
        }

        $request->validate([
            'custom_domain' => 'nullable|string|max:255|unique:restaurants,custom_domain,' . $restaurant->id,
        ]);

        try {
            $restaurant->update([
                'custom_domain' => $request->custom_domain,
                'custom_domain_verified' => false,
                'custom_domain_verified_at' => null,
                'custom_domain_status' => 'pending'
            ]);

            return redirect()->route('restaurant.custom-domain', $restaurant->slug)
                ->with('success', 'Custom domain updated successfully! Please configure your DNS settings.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update custom domain. Please try again.']);
        }
    }

    public function verifyCustomDomain($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Simulate domain verification
        $restaurant->verifyCustomDomain();
        
        return redirect()->back()->with('success', 'Domain verification completed successfully!');
    }

    public function allRestaurants()
    {
        $restaurants = Restaurant::where('is_active', true)
            ->with(['menuItems', 'categories', 'ratings'])
            ->orderBy('name')
            ->get();
            
        return view('restaurants.all', compact('restaurants'));
    }

    public function recentRestaurants()
    {
        $user = Auth::user();
        
        // Get recently visited restaurants based on user orders
        $recentRestaurants = $user->orders()
            ->with(['restaurant.ratings'])
            ->select('restaurant_id')
            ->groupBy('restaurant_id')
            ->orderByRaw('MAX(created_at) DESC')
            ->limit(10)
            ->get()
            ->pluck('restaurant')
            ->filter()
            ->unique('id');
            
        return view('restaurants.recent', compact('recentRestaurants'));
    }

    // Admin Methods
    public function adminIndex()
    {
        // Check if user is admin
        if (!Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $restaurants = Restaurant::with(['owner', 'menuItems', 'categories', 'ratings'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.restaurants.index', compact('restaurants'));
    }

    public function adminEdit(Restaurant $restaurant)
    {
        // Check if user is admin
        if (!Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        return view('admin.restaurants.edit', compact('restaurant'));
    }

    public function adminUpdate(Request $request, Restaurant $restaurant)
    {
        // Check if user is admin
        if (!Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'whatsapp_number' => 'required|string|max:20',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'currency' => 'required|string|max:10',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $data = $request->except(['logo', 'banner']);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                if ($restaurant->logo) {
                    Storage::disk('public')->delete($restaurant->logo);
                }
                $data['logo'] = $request->file('logo')->store('restaurants/logos', 'public');
            }

            // Handle banner upload
            if ($request->hasFile('banner')) {
                if ($restaurant->banner_image) {
                    Storage::disk('public')->delete($restaurant->banner_image);
                }
                $data['banner_image'] = $request->file('banner')->store('restaurants/banners', 'public');
            }

            $restaurant->update($data);

            return redirect()->route('admin.restaurants')
                ->with('success', 'Restaurant updated successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update restaurant. Please try again.']);
        }
    }

    public function adminDestroy(Restaurant $restaurant)
    {
        // Check if user is admin
        if (!Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        try {
            // Delete associated files
            if ($restaurant->logo) {
                Storage::disk('public')->delete($restaurant->logo);
            }
            if ($restaurant->banner_image) {
                Storage::disk('public')->delete($restaurant->banner_image);
            }

            $restaurant->delete();

            return redirect()->route('admin.restaurants')
                ->with('success', 'Restaurant deleted successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete restaurant. Please try again.']);
        }
    }
} 