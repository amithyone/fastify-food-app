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
        
        // Create subscription payment
        $payment = \App\Models\SubscriptionPayment::createPayment(
            $restaurant,
            $request->plan_type,
            $plan->monthly_price,
            Auth::id()
        );

        return redirect()->route('restaurant.subscription.payment', [$restaurant->slug, $payment->id])
            ->with('info', 'Please complete payment to upgrade your subscription.');
    }

    /**
     * Show subscription payment page
     */
    public function payment($slug, $paymentId)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $payment = \App\Models\SubscriptionPayment::where('id', $paymentId)
            ->where('restaurant_id', $restaurant->id)
            ->firstOrFail();

        return view('restaurant.subscription.payment', compact('restaurant', 'payment'));
    }

    /**
     * Generate virtual account for subscription payment
     */
    public function generateVirtualAccount(Request $request, $slug, $paymentId)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $payment = \App\Models\SubscriptionPayment::where('id', $paymentId)
            ->where('restaurant_id', $restaurant->id)
            ->firstOrFail();

        try {
            // Generate virtual account with PayVibe
            $payVibeService = new \App\Services\PayVibeService();
            $result = $payVibeService->generateVirtualAccount([
                'reference' => $payment->payment_reference
            ]);

            if ($result['success']) {
                // Update payment with virtual account details
                $payment->update([
                    'virtual_account_number' => $result['account_number'],
                    'bank_name' => $result['bank_name'],
                    'account_name' => $result['account_name']
                ]);

                // Create PayVibe transaction record
                \App\Models\PayVibeTransaction::create([
                    'subscription_payment_id' => $payment->id,
                    'payment_type' => 'subscription',
                    'reference' => $payment->payment_reference,
                    'amount' => $payment->amount * 100, // Convert to kobo
                    'status' => 'pending',
                    'authorization_url' => null,
                    'access_code' => null,
                    'metadata' => [
                        'restaurant_id' => $restaurant->id,
                        'user_id' => Auth::id(),
                        'payment_type' => 'subscription_payment',
                        'plan_type' => $payment->plan_type,
                        'virtual_account' => [
                            'account_number' => $result['account_number'],
                            'bank_name' => $result['bank_name'],
                            'account_name' => $result['account_name']
                        ]
                    ]
                ]);

                return response()->json([
                    'success' => true,
                    'account_number' => $result['account_number'],
                    'bank_name' => $result['bank_name'],
                    'account_name' => $result['account_name'],
                    'reference' => $payment->payment_reference,
                    'amount' => $payment->formatted_amount
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Virtual account generation failed'
                ], 400);
            }

        } catch (\Exception $e) {
            \Log::error('Subscription payment virtual account generation error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Virtual account service temporarily unavailable'
            ], 500);
        }
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
