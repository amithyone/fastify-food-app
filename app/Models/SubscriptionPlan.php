<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'menu_item_limit',
        'custom_domain_enabled',
        'unlimited_menu_items',
        'priority_support',
        'advanced_analytics',
        'video_packages_enabled',
        'social_media_promotion_enabled',
        'features',
        'limitations',
        'is_active',
        'sort_order',
        'color_scheme'
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'menu_item_limit' => 'integer',
        'custom_domain_enabled' => 'boolean',
        'unlimited_menu_items' => 'boolean',
        'priority_support' => 'boolean',
        'advanced_analytics' => 'boolean',
        'video_packages_enabled' => 'boolean',
        'social_media_promotion_enabled' => 'boolean',
        'features' => 'array',
        'limitations' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('monthly_price', 'asc');
    }

    // Methods
    public function getFormattedPriceAttribute()
    {
        return '₦' . number_format($this->monthly_price, 0);
    }

    public function hasFeature($feature)
    {
        return in_array($feature, $this->features ?? []);
    }

    public function hasLimitation($limitation)
    {
        return in_array($limitation, $this->limitations ?? []);
    }

    public function getColorClassesAttribute()
    {
        return match($this->color_scheme) {
            'orange' => 'text-orange-600',
            'purple' => 'text-purple-600',
            'green' => 'text-green-600',
            'blue' => 'text-blue-600',
            'red' => 'text-red-600',
            default => 'text-gray-600'
        };
    }

    public function getBgColorClassesAttribute()
    {
        return match($this->color_scheme) {
            'orange' => 'bg-orange-50 dark:bg-orange-900',
            'purple' => 'bg-purple-50 dark:bg-purple-900',
            'green' => 'bg-green-50 dark:bg-green-900',
            'blue' => 'bg-blue-50 dark:bg-blue-900',
            'red' => 'bg-red-50 dark:bg-red-900',
            default => 'bg-gray-50 dark:bg-gray-900'
        };
    }

    public function getMenuLimitTextAttribute()
    {
        if ($this->unlimited_menu_items) {
            return 'Unlimited menu items';
        }
        return $this->menu_item_limit . ' menu items';
    }
}
