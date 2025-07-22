<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'points'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'points' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function credit($amount, $points = 0, $description = '', $orderId = null, $metadata = [])
    {
        return DB::transaction(function () use ($amount, $points, $description, $orderId, $metadata) {
            // Update wallet balance and points
            $this->increment('balance', $amount);
            $this->increment('points', $points);

            // Create transaction record
            return $this->transactions()->create([
                'order_id' => $orderId,
                'type' => 'credit',
                'amount' => $amount,
                'points_earned' => $points,
                'description' => $description,
                'metadata' => $metadata
            ]);
        });
    }

    public function debit($amount, $description = '', $orderId = null, $metadata = [])
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        return DB::transaction(function () use ($amount, $description, $orderId, $metadata) {
            // Update wallet balance
            $this->decrement('balance', $amount);

            // Create transaction record
            return $this->transactions()->create([
                'order_id' => $orderId,
                'type' => 'debit',
                'amount' => $amount,
                'points_earned' => 0,
                'description' => $description,
                'metadata' => $metadata
            ]);
        });
    }

    public function getFormattedBalanceAttribute()
    {
        return '₦' . number_format($this->balance, 0);
    }

    public function getPointsDisplayAttribute()
    {
        return number_format($this->points) . ' points';
    }
}
