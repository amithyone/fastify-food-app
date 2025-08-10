<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Restaurant;

class CheckRestaurantSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature = null): Response
    {
        $restaurant = $request->route('restaurant') ?? $request->route('slug');
        
        if (is_string($restaurant)) {
            $restaurant = Restaurant::where('slug', $restaurant)->first();
        }
        
        if (!$restaurant) {
            abort(404, 'Restaurant not found');
        }

        // Check if restaurant has active subscription
        if (!$restaurant->hasActiveSubscription()) {
            return redirect()->route('restaurant.subscription.expired', $restaurant->slug)
                ->with('error', 'Your subscription has expired. Please renew to continue using our services.');
        }

        // Check specific feature access
        if ($feature) {
            switch ($feature) {
                case 'custom_domain':
                    if (!$restaurant->canUseCustomDomain()) {
                        return redirect()->route('restaurant.dashboard', $restaurant->slug)
                            ->with('error', 'Custom domain is not available in your current plan. Please upgrade to access this feature.');
                    }
                    break;
                    
                case 'video_packages':
                    if (!$restaurant->canAccessVideoPackages()) {
                        return redirect()->route('restaurant.dashboard', $restaurant->slug)
                            ->with('error', 'Video packages are not available in your current plan. Please upgrade to access this feature.');
                    }
                    break;
                    
                case 'social_media':
                    if (!$restaurant->canAccessSocialMediaPromotion()) {
                        return redirect()->route('restaurant.dashboard', $restaurant->slug)
                            ->with('error', 'Social media promotion is not available in your current plan. Please upgrade to access this feature.');
                    }
                    break;
                    
                case 'add_menu_item':
                    if (!$restaurant->canAddMenuItem()) {
                        return redirect()->route('restaurant.menu.index', $restaurant->slug)
                            ->with('error', 'You have reached your menu item limit. Please upgrade your plan to add more items.');
                    }
                    break;
            }
        }

        return $next($request);
    }
}
