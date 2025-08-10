<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RestaurantSubscriptionController extends Controller
{
    /**
     * Display subscription status
     */
    public function index($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $subscription = $restaurant->subscription;
        $plans = SubscriptionPlan::active()->ordered()->get();

        return view('restaurant.subscription.index', compact('restaurant', 'subscription', 'plans'));
    }

    /**
     * Show subscription expired page
     */
    public function expired($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $subscription = $restaurant->subscription;
        $plans = SubscriptionPlan::active()->ordered()->get();

        return view('restaurant.subscription.expired', compact('restaurant', 'subscription', 'plans'));
    }

    /**
     * Upgrade subscription
     */
    public function upgrade(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'plan_type' => 'required|in:small,normal,premium'
        ]);

        $plan = SubscriptionPlan::where('slug', $request->plan_type)->firstOrFail();
        
        // Create or update subscription
        $subscription = $restaurant->subscription;
        
        if (!$subscription) {
            $subscription = new RestaurantSubscription();
            $subscription->restaurant_id = $restaurant->id;
        }

        $subscription->plan_type = $request->plan_type;
        $subscription->status = 'active';
        $subscription->monthly_fee = $plan->monthly_price;
        $subscription->menu_item_limit = $plan->menu_item_limit;
        $subscription->custom_domain_enabled = $plan->custom_domain_enabled;
        $subscription->unlimited_menu_items = $plan->unlimited_menu_items;
        $subscription->priority_support = $plan->priority_support;
        $subscription->advanced_analytics = $plan->advanced_analytics;
        $subscription->video_packages_enabled = $plan->video_packages_enabled;
        $subscription->social_media_promotion_enabled = $plan->social_media_promotion_enabled;
        $subscription->features = $plan->features;
        
        // Set subscription end date (30 days from now)
        $subscription->subscription_ends_at = Carbon::now()->addDays(30);
        
        $subscription->save();

        return redirect()->route('restaurant.subscription.index', $restaurant->slug)
            ->with('success', 'Subscription upgraded successfully!');
    }

    /**
     * Cancel subscription
     */
    public function cancel($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $subscription = $restaurant->subscription;
        
        if ($subscription) {
            $subscription->status = 'cancelled';
            $subscription->save();
        }

        return redirect()->route('restaurant.subscription.index', $restaurant->slug)
            ->with('success', 'Subscription cancelled successfully.');
    }

    /**
     * Renew subscription
     */
    public function renew($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $subscription = $restaurant->subscription;
        
        if ($subscription) {
            $subscription->status = 'active';
            $subscription->subscription_ends_at = Carbon::now()->addDays(30);
            $subscription->save();
        }

        return redirect()->route('restaurant.subscription.index', $restaurant->slug)
            ->with('success', 'Subscription renewed successfully!');
    }

    /**
     * Create trial subscription for new restaurants
     */
    public static function createTrialSubscription(Restaurant $restaurant)
    {
        $subscription = new RestaurantSubscription();
        $subscription->restaurant_id = $restaurant->id;
        $subscription->plan_type = 'small';
        $subscription->status = 'trial';
        $subscription->trial_ends_at = Carbon::now()->addMonths(3); // 3 months trial
        $subscription->monthly_fee = 5000.00;
        $subscription->menu_item_limit = 5;
        $subscription->custom_domain_enabled = false;
        $subscription->unlimited_menu_items = false;
        $subscription->priority_support = false;
        $subscription->advanced_analytics = false;
        $subscription->video_packages_enabled = false;
        $subscription->social_media_promotion_enabled = false;
        $subscription->features = [
            'Basic menu management',
            'QR code generation',
            'Order tracking',
            'Basic analytics',
            'Email support',
            'Mobile responsive menu'
        ];
        
        $subscription->save();
        
        return $subscription;
    }
}
