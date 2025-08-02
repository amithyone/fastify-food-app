<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'order_number',
        'customer_name',
        'phone_number',
        'delivery_address',
        'allergies',
        'delivery_time',
        'total_amount',
        'status',
        'status_note',
        'status_updated_at',
        'status_updated_by',
        'notes',
        'tracking_code',
        'tracking_code_expires_at'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'tracking_code_expires_at' => 'datetime',
        'status_updated_at' => 'datetime',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusUpdater()
    {
        return $this->belongsTo(User::class, 'status_updated_by');
    }

    public function getFormattedTotalAttribute()
    {
        return '₦' . number_format($this->total_amount, 0);
    }

    public function getStatusBadgeAttribute()
    {
        $statuses = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'preparing' => 'bg-orange-100 text-orange-800',
            'ready' => 'bg-green-100 text-green-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];

        return $statuses[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getPaymentMethodDisplayAttribute()
    {
        $methods = [
            'cash' => 'Cash on Delivery',
            'card' => 'Card Payment',
            'transfer' => 'Bank Transfer',
        ];

        return $methods[$this->payment_method] ?? ucfirst($this->payment_method);
    }

    public function getPaymentStatusBadgeAttribute()
    {
        $statuses = [
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-200',
            'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200',
            'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-200',
        ];

        return $statuses[$this->payment_status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-200';
    }

    public function isRestaurantOrder()
    {
        return str_contains($this->delivery_address, 'Table:');
    }

    public function getDeliveryFeeAttribute()
    {
        return $this->isRestaurantOrder() ? 0 : 500;
    }

    public function getSubtotalAttribute()
    {
        return $this->total_amount - $this->delivery_fee;
    }

    public function generateTrackingCode()
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4));
        } while (static::where('tracking_code', $code)->exists());

        return $code;
    }

    public function isTrackingCodeActive()
    {
        return $this->tracking_code && 
               $this->tracking_code_expires_at && 
               $this->tracking_code_expires_at->isFuture();
    }

    public function scopeByTrackingCode($query, $code)
    {
        return $query->where('tracking_code', $code)
                    ->where('tracking_code_expires_at', '>', now());
    }

    public function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
        } while (static::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ABJ' . date('Ymd') . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
