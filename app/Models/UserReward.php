<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'guest_session_id',
        'points',
        'total_spent',
        'orders_count',
        'tier',
        'rewards_earned',
        'rewards_redeemed',
        'last_order_at',
        'order_id',
        'points_earned',
        'order_amount',
        'payment_method',
        'status',
        'credited_at',
        'expires_at'
    ];

    protected $casts = [
        'points' => 'integer',
        'total_spent' => 'decimal:2',
        'orders_count' => 'integer',
        'rewards_earned' => 'integer',
        'rewards_redeemed' => 'integer',
        'last_order_at' => 'datetime',
        'points_earned' => 'integer',
        'order_amount' => 'decimal:2',
        'credited_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function calculatePoints($orderAmount)
    {
        // Reward rate: 1 point per ₦100 spent
        return (int) ($orderAmount / 100);
    }

    public function creditPoints()
    {
        if ($this->status === 'pending') {
            $this->update([
                'status' => 'credited',
                'credited_at' => now(),
                'expires_at' => now()->addMonths(6) // Points expire in 6 months
            ]);

            // Add points to user's wallet
            $wallet = $this->user->wallet;
            if ($wallet) {
                $wallet->credit(0, $this->points_earned, "Reward points from order #{$this->order_id}", $this->order_id);
            }
        }
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getStatusBadgeAttribute()
    {
        $classes = [
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-200',
            'credited' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200',
            'expired' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-200'
        ];

        return $classes[$this->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-200';
    }

    public function getFormattedOrderAmountAttribute()
    {
        return '₦' . number_format($this->order_amount, 0);
    }
}
