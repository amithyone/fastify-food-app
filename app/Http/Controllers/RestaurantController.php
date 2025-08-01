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
        return view('restaurant.onboarding');
    }

    public function store(Request $request)
    {
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
            'country' => 'required|string|max:100',
            'currency' => 'required|string|max:10',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'theme_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
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
            if ($request->hasFile('banner_image')) {
                $bannerPath = $request->file('banner_image')->store('restaurants/banners', 'public');
            }

            // Create restaurant
            $restaurant = Restaurant::create([
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
                'country' => $request->country,
                'currency' => $request->currency,
                'logo' => $logoPath,
                'banner_image' => $bannerPath,
                'theme_color' => $request->theme_color,
                'secondary_color' => $request->secondary_color,
                'is_active' => true,
                'is_verified' => false, // Will be verified by admin
                'business_hours' => [
                    'monday' => ['open' => '09:00', 'close' => '22:00'],
                    'tuesday' => ['open' => '09:00', 'close' => '22:00'],
                    'wednesday' => ['open' => '09:00', 'close' => '22:00'],
                    'thursday' => ['open' => '09:00', 'close' => '22:00'],
                    'friday' => ['open' => '09:00', 'close' => '23:00'],
                    'saturday' => ['open' => '10:00', 'close' => '23:00'],
                    'sunday' => ['open' => '10:00', 'close' => '22:00'],
                ],
                'settings' => [
                    'delivery_enabled' => true,
                    'dine_in_enabled' => true,
                    'delivery_fee' => 500, // in kobo
                    'minimum_order' => 1000, // in kobo
                    'auto_accept_orders' => false,
                ],
            ]);

            // Assign restaurant to current user if authenticated
            if (Auth::check()) {
                $user = Auth::user();
                $user->update(['restaurant_id' => $restaurant->id]);
            }

            return redirect()->route('restaurant.dashboard', $restaurant->slug)
                ->with('success', 'Restaurant created successfully! You can now start adding your menu items.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create restaurant. Please try again.']);
        }
    }

    public function dashboard($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check if user has access to this restaurant
        if (Auth::check() && Auth::user()->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to restaurant dashboard.');
        }

        $stats = [
            'total_orders' => $restaurant->orders()->count(),
            'pending_orders' => $restaurant->orders()->where('status', 'pending')->count(),
            'total_menu_items' => $restaurant->menuItems()->count(),
            'total_categories' => $restaurant->categories()->count(),
        ];

        $recent_orders = $restaurant->orders()->with('items')->latest()->take(5)->get();

        return view('restaurant.dashboard', compact('restaurant', 'stats', 'recent_orders'));
    }

    public function edit($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check() && Auth::user()->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to restaurant dashboard.');
        }

        return view('restaurant.edit', compact('restaurant'));
    }

    public function update(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check() && Auth::user()->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to restaurant dashboard.');
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
            'country' => 'required|string|max:100',
            'currency' => 'required|string|max:10',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'theme_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'business_hours' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);

        try {
            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo
                if ($restaurant->logo) {
                    Storage::disk('public')->delete($restaurant->logo);
                }
                $logoPath = $request->file('logo')->store('restaurants/logos', 'public');
                $restaurant->logo = $logoPath;
            }

            // Handle banner upload
            if ($request->hasFile('banner_image')) {
                // Delete old banner
                if ($restaurant->banner_image) {
                    Storage::disk('public')->delete($restaurant->banner_image);
                }
                $bannerPath = $request->file('banner_image')->store('restaurants/banners', 'public');
                $restaurant->banner_image = $bannerPath;
            }

            // Update restaurant
            $restaurant->update([
                'name' => $request->name,
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
            ]);

            return redirect()->route('restaurant.dashboard', $restaurant->slug)
                ->with('success', 'Restaurant updated successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update restaurant. Please try again.']);
        }
    }

    public function qrCodes($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check() && Auth::user()->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to restaurant dashboard.');
        }

        $qrCodes = $restaurant->tableQrs()->get();

        return view('restaurant.qr-codes', compact('restaurant', 'qrCodes'));
    }

    public function generateQrCode(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (Auth::check() && Auth::user()->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to restaurant dashboard.');
        }

        $request->validate([
            'table_number' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        $qrCode = $restaurant->tableQrs()->create([
            'table_number' => $request->table_number,
            'description' => $request->description,
            'qr_code' => $restaurant->slug . '_table_' . $request->table_number,
            'is_active' => true,
        ]);

        return redirect()->route('restaurant.qr-codes', $restaurant->slug)
            ->with('success', 'QR Code generated successfully!');
    }
} 