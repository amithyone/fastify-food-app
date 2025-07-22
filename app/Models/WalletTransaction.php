<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'order_id',
        'type',
        'amount',
        'points_earned',
        'description',
        'status',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'points_earned' => 'integer',
        'metadata' => 'array'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getFormattedAmountAttribute()
    {
        $prefix = $this->type === 'credit' ? '+' : '-';
        return $prefix . '₦' . number_format($this->amount, 0);
    }

    public function getTypeBadgeAttribute()
    {
        $classes = [
            'credit' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200',
            'debit' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-200'
        ];

        return $classes[$this->type] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-200';
    }

    public function getStatusBadgeAttribute()
    {
        $classes = [
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-200',
            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200',
            'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-200'
        ];

        return $classes[$this->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-200';
    }
}
