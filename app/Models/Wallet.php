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
        'restaurant_id',
        'balance',
        'currency',
        'is_active'
    ];

    protected $casts = [
        'balance' => 'integer',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function credit($amount, $description = '', $orderId = null, $metadata = [])
    {
        return DB::transaction(function () use ($amount, $description, $orderId, $metadata) {
            // Update wallet balance
            $this->increment('balance', $amount);

            // Create transaction record
            return $this->transactions()->create([
                'order_id' => $orderId,
                'type' => 'credit',
                'amount' => $amount,
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
                'description' => $description,
                'metadata' => $metadata
            ]);
        });
    }

    public function getFormattedBalanceAttribute()
    {
        return $this->currency . number_format($this->balance / 100, 2);
    }
}
