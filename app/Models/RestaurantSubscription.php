<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RestaurantSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'plan_type',
        'status',
        'trial_ends_at',
        'subscription_ends_at',
        'monthly_fee',
        'menu_item_limit',
        'custom_domain_enabled',
        'unlimited_menu_items',
        'priority_support',
        'advanced_analytics',
        'video_packages_enabled',
        'social_media_promotion_enabled',
        'features'
    ];

    protected $casts = [
        'trial_ends_at' => 'date',
        'subscription_ends_at' => 'date',
        'monthly_fee' => 'decimal:2',
        'menu_item_limit' => 'integer',
        'custom_domain_enabled' => 'boolean',
        'unlimited_menu_items' => 'boolean',
        'priority_support' => 'boolean',
        'advanced_analytics' => 'boolean',
        'video_packages_enabled' => 'boolean',
        'social_media_promotion_enabled' => 'boolean',
        'features' => 'array'
    ];

    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTrial($query)
    {
        return $query->where('status', 'trial');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    // Methods
    public function isActive()
    {
        if ($this->status === 'active') {
            return $this->subscription_ends_at === null || $this->subscription_ends_at->isFuture();
        }
        return false;
    }

    public function isTrial()
    {
        return $this->status === 'trial' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    public function isExpired()
    {
        if ($this->status === 'expired') {
            return true;
        }
        
        if ($this->status === 'trial' && $this->trial_ends_at) {
            return $this->trial_ends_at->isPast();
        }
        
        if ($this->status === 'active' && $this->subscription_ends_at) {
            return $this->subscription_ends_at->isPast();
        }
        
        return false;
    }

    public function getDaysRemainingAttribute()
    {
        if ($this->isTrial()) {
            return $this->trial_ends_at->diffInDays(Carbon::now());
        }
        
        if ($this->isActive() && $this->subscription_ends_at) {
            return $this->subscription_ends_at->diffInDays(Carbon::now());
        }
        
        return 0;
    }

    public function canAddMenuItem()
    {
        if ($this->unlimited_menu_items) {
            return true;
        }
        
        $currentCount = $this->restaurant->menuItems()->count();
        return $currentCount < $this->menu_item_limit;
    }

    public function getRemainingMenuSlotsAttribute()
    {
        if ($this->unlimited_menu_items) {
            return 'unlimited';
        }
        
        $currentCount = $this->restaurant->menuItems()->count();
        return max(0, $this->menu_item_limit - $currentCount);
    }

    public function canUseCustomDomain()
    {
        return $this->custom_domain_enabled && $this->isActive();
    }

    public function canAccessVideoPackages()
    {
        return $this->video_packages_enabled && $this->isActive();
    }

    public function canAccessSocialMediaPromotion()
    {
        return $this->social_media_promotion_enabled && $this->isActive();
    }

    public function hasPrioritySupport()
    {
        return $this->priority_support && $this->isActive();
    }

    public function hasAdvancedAnalytics()
    {
        return $this->advanced_analytics && $this->isActive();
    }

    public function getFormattedPriceAttribute()
    {
        return '₦' . number_format($this->monthly_fee, 0);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'trial' => 'blue',
            'expired' => 'red',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    public function getPlanTypeColorAttribute()
    {
        return match($this->plan_type) {
            'small' => 'blue',
            'normal' => 'purple',
            'premium' => 'orange',
            default => 'gray'
        };
    }
}
