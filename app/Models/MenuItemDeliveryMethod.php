<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItemDeliveryMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id',
        'delivery_method',
        'enabled',
        'additional_fee',
        'notes'
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'additional_fee' => 'decimal:2'
    ];

    /**
     * Get the menu item that owns the delivery method
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * Check if this delivery method is enabled
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get formatted additional fee
     */
    public function getFormattedAdditionalFeeAttribute()
    {
        return '₦' . number_format($this->additional_fee, 2);
    }

    /**
     * Get delivery method display name
     */
    public function getDeliveryMethodDisplayAttribute()
    {
        $methods = [
            'delivery' => 'Delivery',
            'pickup' => 'Pickup',
            'in_restaurant' => 'In Restaurant'
        ];

        return $methods[$this->delivery_method] ?? ucfirst($this->delivery_method);
    }

    /**
     * Scope to get enabled delivery methods
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope to get delivery methods by type
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('delivery_method', $method);
    }
}
