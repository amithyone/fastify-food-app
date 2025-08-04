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
        'created_by',
        'session_id',
        'order_number',
        'customer_name',
        'phone_number',
        'delivery_address',
        'allergies',
        'delivery_time',
        'total_amount',
        'subtotal',
        'service_charge',
        'tax_amount',
        'delivery_fee',
        'discount_amount',
        'discount_code',
        'charge_breakdown',
        'status',
        'status_note',
        'status_updated_at',
        'status_updated_by',
        'notes',
        'tracking_code',
        'tracking_code_expires_at',
        'order_type',
        'pickup_code',
        'pickup_time',
        'pickup_name',
        'pickup_phone',
        'payment_method',
        'payment_reference',
        'gateway_reference',
        'payment_status',
        'paid_at'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'charge_breakdown' => 'array',
        'pickup_time' => 'datetime',
        'tracking_code_expires_at' => 'datetime',
        'status_updated_at' => 'datetime',
        'paid_at' => 'datetime',
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function guestSession()
    {
        return $this->belongsTo(GuestSession::class, 'session_id', 'session_id');
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
            'pending_payment' => 'bg-orange-100 text-orange-800',
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

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute()
    {
        return '₦' . number_format($this->subtotal ?? 0, 2);
    }

    /**
     * Get formatted service charge
     */
    public function getFormattedServiceChargeAttribute()
    {
        return '₦' . number_format($this->service_charge ?? 0, 2);
    }

    /**
     * Get formatted tax amount
     */
    public function getFormattedTaxAmountAttribute()
    {
        return '₦' . number_format($this->tax_amount ?? 0, 2);
    }

    /**
     * Get formatted delivery fee
     */
    public function getFormattedDeliveryFeeAttribute()
    {
        return '₦' . number_format($this->delivery_fee ?? 0, 2);
    }

    /**
     * Get formatted discount amount
     */
    public function getFormattedDiscountAmountAttribute()
    {
        return '₦' . number_format($this->discount_amount ?? 0, 2);
    }

    /**
     * Check if order has any charges
     */
    public function hasCharges()
    {
        return ($this->service_charge ?? 0) > 0 || 
               ($this->tax_amount ?? 0) > 0 || 
               ($this->delivery_fee ?? 0) > 0;
    }

    /**
     * Check if order has discount
     */
    public function hasDiscount()
    {
        return ($this->discount_amount ?? 0) > 0;
    }

    /**
     * Get charge breakdown for display
     */
    public function getChargeBreakdownDisplay()
    {
        $breakdown = [];
        
        if ($this->service_charge > 0) {
            $breakdown[] = [
                'name' => 'Service Charge',
                'amount' => $this->formatted_service_charge
            ];
        }
        
        if ($this->tax_amount > 0) {
            $breakdown[] = [
                'name' => 'Tax',
                'amount' => $this->formatted_tax_amount
            ];
        }
        
        if ($this->delivery_fee > 0) {
            $breakdown[] = [
                'name' => 'Delivery Fee',
                'amount' => $this->formatted_delivery_fee
            ];
        }
        
        if ($this->discount_amount > 0) {
            $breakdown[] = [
                'name' => 'Discount',
                'amount' => '-' . $this->formatted_discount_amount
            ];
        }
        
        return $breakdown;
    }

    /**
     * Generate pickup code
     */
    public function generatePickupCode()
    {
        return strtoupper(substr(md5($this->id . time()), 0, 6));
    }

    /**
     * Check if order is pickup type
     */
    public function isPickup()
    {
        return $this->order_type === 'pickup';
    }

    /**
     * Check if order is delivery type
     */
    public function isDelivery()
    {
        return $this->order_type === 'delivery';
    }

    /**
     * Check if order is in restaurant type
     */
    public function isInRestaurant()
    {
        return $this->order_type === 'in_restaurant';
    }

    /**
     * Get order type display name
     */
    public function getOrderTypeDisplayAttribute()
    {
        $types = [
            'delivery' => 'Delivery',
            'pickup' => 'Pickup',
            'in_restaurant' => 'In Restaurant'
        ];

        return $types[$this->order_type] ?? ucfirst($this->order_type);
    }

    /**
     * Get formatted pickup time
     */
    public function getFormattedPickupTimeAttribute()
    {
        if (!$this->pickup_time) {
            return 'ASAP';
        }
        
        return $this->pickup_time->format('M j, Y g:i A');
    }

    /**
     * Check if pickup time is in the future
     */
    public function isPickupTimeFuture()
    {
        return $this->pickup_time && $this->pickup_time->isFuture();
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
