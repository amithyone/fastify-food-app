<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantDeliverySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'delivery_mode',
        'delivery_enabled',
        'pickup_enabled',
        'in_restaurant_enabled',
        'delivery_radius',
        'minimum_delivery_amount',
        'delivery_fee',
        'delivery_time_minutes',
        'pickup_time_minutes',
        'delivery_notes',
        'pickup_notes'
    ];

    protected $casts = [
        'delivery_enabled' => 'boolean',
        'pickup_enabled' => 'boolean',
        'in_restaurant_enabled' => 'boolean',
        'delivery_radius' => 'array',
        'minimum_delivery_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'delivery_time_minutes' => 'integer',
        'pickup_time_minutes' => 'integer'
    ];

    /**
     * Get the restaurant that owns the delivery setting
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Check if flexible mode is enabled
     */
    public function isFlexibleMode()
    {
        return $this->delivery_mode === 'flexible';
    }

    /**
     * Check if fixed mode is enabled
     */
    public function isFixedMode()
    {
        return $this->delivery_mode === 'fixed';
    }

    /**
     * Get available delivery methods
     */
    public function getAvailableDeliveryMethods()
    {
        $methods = [];
        
        if ($this->delivery_enabled) {
            $methods[] = 'delivery';
        }
        
        if ($this->pickup_enabled) {
            $methods[] = 'pickup';
        }
        
        if ($this->in_restaurant_enabled) {
            $methods[] = 'in_restaurant';
        }
        
        return $methods;
    }

    /**
     * Check if a specific delivery method is enabled
     */
    public function isDeliveryMethodEnabled($method)
    {
        switch ($method) {
            case 'delivery':
                return $this->delivery_enabled;
            case 'pickup':
                return $this->pickup_enabled;
            case 'in_restaurant':
                return $this->in_restaurant_enabled;
            default:
                return false;
        }
    }

    /**
     * Get formatted delivery time
     */
    public function getFormattedDeliveryTimeAttribute()
    {
        return $this->delivery_time_minutes . ' minutes';
    }

    /**
     * Get formatted pickup time
     */
    public function getFormattedPickupTimeAttribute()
    {
        return $this->pickup_time_minutes . ' minutes';
    }

    /**
     * Get formatted delivery fee
     */
    public function getFormattedDeliveryFeeAttribute()
    {
        return '₦' . number_format($this->delivery_fee, 2);
    }

    /**
     * Get formatted minimum delivery amount
     */
    public function getFormattedMinimumDeliveryAmountAttribute()
    {
        return '₦' . number_format($this->minimum_delivery_amount, 2);
    }
}
