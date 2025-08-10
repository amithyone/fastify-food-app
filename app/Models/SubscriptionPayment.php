<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'payment_reference',
        'plan_type',
        'amount',
        'amount_paid',
        'status',
        'virtual_account_number',
        'bank_name',
        'account_name',
        'gateway_reference',
        'paid_at',
        'expires_at',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array'
    ];

    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payVibeTransaction()
    {
        return $this->hasOne(PayVibeTransaction::class, 'payment_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'successful');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    // Methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isSuccessful()
    {
        return $this->status === 'successful';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isExpired()
    {
        return $this->status === 'expired' || 
               ($this->expires_at && $this->expires_at->isPast());
    }

    public function getFormattedAmountAttribute()
    {
        return '₦' . number_format($this->amount, 0);
    }

    public function getFormattedAmountPaidAttribute()
    {
        return '₦' . number_format($this->amount_paid, 0);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'successful' => 'green',
            'failed' => 'red',
            'expired' => 'gray',
            default => 'gray'
        };
    }

    public function getPlanTypeColorAttribute()
    {
        return match($this->plan_type) {
            'small' => 'blue',
            'normal' => 'purple',
            'premium' => 'orange',
            default => 'gray'
        };
    }

    public function getPlanNameAttribute()
    {
        return match($this->plan_type) {
            'small' => 'Small Restaurant',
            'normal' => 'Normal Restaurant',
            'premium' => 'Premium Restaurant',
            default => 'Unknown Plan'
        };
    }

    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->amount_paid;
    }

    public function getFormattedRemainingAmountAttribute()
    {
        return '₦' . number_format($this->remaining_amount, 0);
    }

    public function getPaymentInstructionsAttribute()
    {
        if (!$this->virtual_account_number) {
            return null;
        }

        return "Please transfer ₦{$this->amount} to:\n" .
               "Bank: {$this->bank_name}\n" .
               "Account Number: {$this->virtual_account_number}\n" .
               "Account Name: {$this->account_name}\n" .
               "Reference: {$this->payment_reference}";
    }

    // Static methods
    public static function generatePaymentReference()
    {
        return 'SUBREF' . time() . strtoupper(uniqid());
    }

    public static function createPayment($restaurant, $planType, $amount, $userId = null)
    {
        return self::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $userId,
            'payment_reference' => self::generatePaymentReference(),
            'plan_type' => $planType,
            'amount' => $amount,
            'amount_paid' => 0,
            'status' => 'pending',
            'expires_at' => now()->addHours(24),
            'metadata' => [
                'restaurant_name' => $restaurant->name,
                'plan_type' => $planType,
                'payment_type' => 'subscription_upgrade'
            ]
        ]);
    }
}
