<?php

namespace App\Http\Controllers;

use App\Models\PromotionPlan;
use App\Models\PromotionPayment;
use App\Models\FeaturedRestaurant;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PromotionController extends Controller
{
    /**
     * Show available promotion plans
     */
    public function index($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check if user can manage this restaurant
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $plans = PromotionPlan::active()->ordered()->get();
        $currentPromotion = FeaturedRestaurant::where('restaurant_id', $restaurant->id)
            ->currentlyFeatured()
            ->first();

        return view('promotions.index', compact('restaurant', 'plans', 'currentPromotion'));
    }

    /**
     * Show promotion plan details and create payment
     */
    public function show($slug, $planId)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $plan = PromotionPlan::findOrFail($planId);
        
        // Check if user can manage this restaurant
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        return view('promotions.show', compact('restaurant', 'plan'));
    }

    /**
     * Create a new promotion payment
     */
    public function createPayment(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $plan = PromotionPlan::findOrFail($request->plan_id);
        
        // Check if user can manage this restaurant
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        try {
            // Create featured restaurant record
            $featuredRestaurant = FeaturedRestaurant::create([
                'restaurant_id' => $restaurant->id,
                'title' => $request->title ?? $restaurant->name,
                'description' => $request->description ?? $restaurant->description,
                'badge_text' => $request->badge_text ?? 'New',
                'badge_color' => $request->badge_color ?? 'orange',
                'cta_text' => $request->cta_text ?? 'Order Now',
                'cta_link' => $request->cta_link,
                'ad_image' => $request->ad_image,
                'is_active' => false, // Will be activated after payment
                'sort_order' => 0
            ]);

            // Create payment record
            $payment = PromotionPayment::createPayment(
                $restaurant->id,
                $plan->id,
                $featuredRestaurant->id
            );

            Log::info('Promotion payment created', [
                'restaurant_id' => $restaurant->id,
                'plan_id' => $plan->id,
                'payment_id' => $payment->id,
                'amount' => $payment->amount
            ]);

            return response()->json([
                'success' => true,
                'payment' => $payment,
                'featured_restaurant' => $featuredRestaurant,
                'redirect_url' => route('promotions.payment', ['slug' => $slug, 'paymentId' => $payment->id])
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating promotion payment', [
                'restaurant_id' => $restaurant->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating payment. Please try again.'
            ], 500);
        }
    }

    /**
     * Show payment details and instructions
     */
    public function payment($slug, $paymentId)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $payment = PromotionPayment::with(['promotionPlan', 'featuredRestaurant'])
            ->where('id', $paymentId)
            ->where('restaurant_id', $restaurant->id)
            ->firstOrFail();
        
        // Check if user can manage this restaurant
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        return view('promotions.payment', compact('restaurant', 'payment'));
    }

    /**
     * Show PayVibe payment page
     */
    public function payvibePayment($slug, $paymentId)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $payment = PromotionPayment::with(['promotionPlan', 'featuredRestaurant'])
            ->where('id', $paymentId)
            ->where('restaurant_id', $restaurant->id)
            ->firstOrFail();
        
        // Check if user can manage this restaurant
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        return view('promotions.payvibe-payment', compact('restaurant', 'payment'));
    }

    /**
     * Show virtual account payment page
     */
    public function virtualAccountPayment($slug, $paymentId)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $payment = PromotionPayment::with(['promotionPlan', 'featuredRestaurant'])
            ->where('id', $paymentId)
            ->where('restaurant_id', $restaurant->id)
            ->firstOrFail();
        
        // Check if user can manage this restaurant
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        return view('promotions.virtual-account-payment', compact('restaurant', 'payment'));
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus($slug, $paymentId)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $payment = PromotionPayment::where('id', $paymentId)
            ->where('restaurant_id', $restaurant->id)
            ->firstOrFail();
        
        // Check if user can manage this restaurant
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        return response()->json([
            'status' => $payment->status,
            'is_paid' => $payment->isPaid(),
            'is_expired' => $payment->isExpired(),
            'expires_at' => $payment->expires_at?->format('Y-m-d H:i:s'),
            'amount' => $payment->formatted_amount
        ]);
    }

    /**
     * Mark payment as paid (admin function)
     */
    public function markAsPaid(Request $request, $paymentId)
    {
        if (!Auth::user()->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        $payment = PromotionPayment::findOrFail($paymentId);
        
        try {
            $payment->markAsPaid(
                $request->payment_method ?? 'bank_transfer',
                $request->payment_details
            );

            Log::info('Payment marked as paid', [
                'payment_id' => $payment->id,
                'restaurant_id' => $payment->restaurant_id,
                'amount' => $payment->amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment marked as paid successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking payment as paid', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error marking payment as paid'
            ], 500);
        }
    }

    /**
     * Get promotion analytics
     */
    public function analytics($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check if user can manage this restaurant
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $payments = PromotionPayment::with(['promotionPlan', 'featuredRestaurant'])
            ->where('restaurant_id', $restaurant->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $activePromotion = FeaturedRestaurant::where('restaurant_id', $restaurant->id)
            ->currentlyFeatured()
            ->first();

        return view('promotions.analytics', compact('restaurant', 'payments', 'activePromotion'));
    }
}
