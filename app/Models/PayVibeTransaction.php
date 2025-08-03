<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayVibeTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'reference',
        'amount',
        'status',
        'authorization_url',
        'access_code',
        'gateway_reference',
        'amount_received',
        'metadata',
        'webhook_data'
    ];

    protected $casts = [
        'amount' => 'integer',
        'amount_received' => 'integer',
        'metadata' => 'array',
        'webhook_data' => 'array'
    ];

    // Relationships
    public function payment()
    {
        return $this->belongsTo(PromotionPayment::class, 'payment_id');
    }

    public function restaurant()
    {
        return $this->hasOneThrough(Restaurant::class, PromotionPayment::class, 'id', 'id', 'payment_id', 'restaurant_id');
    }

    public function user()
    {
        return $this->hasOneThrough(User::class, Restaurant::class, 'id', 'id', 'restaurant_id', 'user_id');
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

    // Methods
    public function getFormattedAmountAttribute()
    {
        return '₦' . number_format($this->amount / 100, 0);
    }

    public function getFormattedAmountReceivedAttribute()
    {
        return '₦' . number_format($this->amount_received / 100, 0);
    }

    public function getStatusBadgeAttribute()
    {
        $classes = [
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-200',
            'successful' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200',
            'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-200'
        ];

        return $classes[$this->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-200';
    }

    public function isSuccessful()
    {
        return $this->status === 'successful';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Mark transaction as successful
     */
    public function markAsSuccessful($gatewayReference = null, $amountReceived = null, $webhookData = null)
    {
        $this->update([
            'status' => 'successful',
            'gateway_reference' => $gatewayReference,
            'amount_received' => $amountReceived,
            'webhook_data' => $webhookData
        ]);
    }

    /**
     * Mark transaction as failed
     */
    public function markAsFailed($webhookData = null)
    {
        $this->update([
            'status' => 'failed',
            'webhook_data' => $webhookData
        ]);
    }
} 