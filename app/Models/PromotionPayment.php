<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PromotionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'promotion_plan_id',
        'featured_restaurant_id',
        'payment_reference',
        'account_number',
        'amount',
        'status',
        'payment_method',
        'paid_at',
        'expires_at',
        'payment_details',
        'notes'
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'payment_details' => 'array'
    ];

    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function promotionPlan()
    {
        return $this->belongsTo(PromotionPlan::class);
    }

    public function featuredRestaurant()
    {
        return $this->belongsTo(FeaturedRestaurant::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    // Methods
    public function getFormattedAmountAttribute()
    {
        return '₦' . number_format($this->amount / 100, 0);
    }

    public function getFormattedAmountKoboAttribute()
    {
        return '₦' . number_format($this->amount, 0);
    }

    public function getStatusBadgeAttribute()
    {
        $classes = [
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-200',
            'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200',
            'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-200',
            'expired' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-200'
        ];

        return $classes[$this->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-200';
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function markAsPaid($paymentMethod = null, $paymentDetails = null)
    {
        $this->update([
            'status' => 'paid',
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
            'payment_details' => $paymentDetails
        ]);

        // Activate the featured restaurant if it exists
        if ($this->featuredRestaurant) {
            $this->featuredRestaurant->update([
                'is_active' => true,
                'featured_from' => now(),
                'featured_until' => now()->addDays($this->promotionPlan->duration_days)
            ]);
        }
    }

    public function markAsExpired()
    {
        $this->update(['status' => 'expired']);
    }

    // Static methods for generating payment references and account numbers
    public static function generatePaymentReference()
    {
        return 'PROMO-' . strtoupper(Str::random(8));
    }

    public static function generateAccountNumber()
    {
        return 'ACC-' . strtoupper(Str::random(10));
    }

    public static function createPayment($restaurantId, $planId, $featuredRestaurantId = null)
    {
        $plan = PromotionPlan::findOrFail($planId);
        
        return self::create([
            'restaurant_id' => $restaurantId,
            'promotion_plan_id' => $planId,
            'featured_restaurant_id' => $featuredRestaurantId,
            'payment_reference' => self::generatePaymentReference(),
            'account_number' => self::generateAccountNumber(),
            'amount' => $plan->price,
            'status' => 'pending',
            'expires_at' => now()->addHours(24) // Payment expires in 24 hours
        ]);
    }
}
