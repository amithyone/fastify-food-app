@php
    // Determine the current restaurant context
    $currentRestaurant = null;
    
    // Check if we're on a restaurant-specific page
    if (isset($restaurant)) {
        $currentRestaurant = $restaurant;
    } elseif (request()->routeIs('menu.index') && request()->segment(2)) {
        // We're on a restaurant-specific menu page
        $currentRestaurant = \App\Models\Restaurant::where('slug', request()->segment(2))->first();
    } elseif (session('qr_restaurant_id')) {
        // We're in a QR code context
        $currentRestaurant = \App\Models\Restaurant::find(session('qr_restaurant_id'));
    }
    
    // Determine menu URL
    if ($currentRestaurant) {
        $menuUrl = route('menu.restaurant', $currentRestaurant->slug);
    } else {
        $menuUrl = route('menu.index');
    }
@endphp

{{ $menuUrl }}
