<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BankTransferPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'payment_reference',
        'amount',
        'amount_paid',
        'amount_remaining',
        'virtual_account_number',
        'bank_name',
        'account_name',
        'status',
        'reward_points_earned',
        'reward_points_rate',
        'reward_points_threshold',
        'service_charge_rate',
        'service_charge_amount',
        'payment_history',
        'expires_at',
        'paid_at',
        'payment_instructions',
        'notes',
        'payvibe_net_amount',
        'payvibe_bank_charge',
        'payvibe_platform_fee',
        'payvibe_settled_amount',
        'payvibe_platform_profit',
        'payvibe_transaction_amount',
        'payvibe_credited_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_remaining' => 'decimal:2',
        'reward_points_rate' => 'integer',
        'reward_points_threshold' => 'decimal:2',
        'service_charge_rate' => 'decimal:2',
        'service_charge_amount' => 'decimal:2',
        'payment_history' => 'array',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'payvibe_net_amount' => 'decimal:2',
        'payvibe_bank_charge' => 'decimal:2',
        'payvibe_platform_fee' => 'decimal:2',
        'payvibe_settled_amount' => 'decimal:2',
        'payvibe_platform_profit' => 'decimal:2',
        'payvibe_transaction_amount' => 'decimal:2',
        'payvibe_credited_at' => 'datetime'
    ];

    /**
     * Get the order that owns the payment
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user that owns the payment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique payment reference
     */
    public static function generatePaymentReference()
    {
        do {
            $reference = 'BTF-' . strtoupper(Str::random(8)) . '-' . time();
        } while (self::where('payment_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Calculate reward points based on amount paid
     */
    public function calculateRewardPoints($amount = null)
    {
        $amountToCalculate = $amount ?? $this->amount_paid;
        return floor($amountToCalculate / $this->reward_points_threshold) * $this->reward_points_rate;
    }

    /**
     * Calculate service charge
     */
    public function calculateServiceCharge($amount = null)
    {
        $amountToCalculate = $amount ?? $this->amount_remaining;
        return ($amountToCalculate * $this->service_charge_rate) / 100;
    }

    /**
     * Update payment with new amount
     */
    public function updatePayment($amountPaid)
    {
        $this->amount_paid += $amountPaid;
        $this->amount_remaining = $this->amount - $this->amount_paid;
        
        // Calculate new reward points
        $this->reward_points_earned = $this->calculateRewardPoints();
        
        // Update status
        if ($this->amount_remaining <= 0) {
            $this->status = 'completed';
            $this->paid_at = now();
        } else {
            $this->status = 'partial';
        }
        
        // Add to payment history
        $history = $this->payment_history ?? [];
        $history[] = [
            'amount' => $amountPaid,
            'timestamp' => now()->toISOString(),
            'status' => $this->status
        ];
        $this->payment_history = $history;
        
        $this->save();
        
        return $this;
    }

    /**
     * Check if payment is expired
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is partial
     */
    public function isPartial()
    {
        return $this->status === 'partial';
    }

    /**
     * Check if payment is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return '₦' . number_format($this->amount, 2);
    }

    /**
     * Get formatted amount paid
     */
    public function getFormattedAmountPaidAttribute()
    {
        return '₦' . number_format($this->amount_paid, 2);
    }

    /**
     * Get formatted amount remaining
     */
    public function getFormattedAmountRemainingAttribute()
    {
        return '₦' . number_format($this->amount_remaining, 2);
    }

    /**
     * Get formatted service charge
     */
    public function getFormattedServiceChargeAttribute()
    {
        return '₦' . number_format($this->service_charge_amount, 2);
    }

    /**
     * Get payment instructions
     */
    public function getPaymentInstructionsAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        return "1. Transfer exactly ₦{$this->amount} to the account number below\n" .
               "2. Use your payment reference: {$this->payment_reference}\n" .
               "3. Keep your transfer receipt for verification\n" .
               "4. Payment will be confirmed automatically within 5 minutes";
    }

    /**
     * Get time remaining until expiration
     */
    public function getTimeRemainingAttribute()
    {
        if ($this->isExpired()) {
            return 'Expired';
        }
        
        return $this->expires_at->diffForHumans();
    }

    /**
     * Scope to get pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get partial payments
     */
    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    /**
     * Scope to get completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get expired payments
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }
}
