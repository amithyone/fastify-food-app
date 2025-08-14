<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'description',
        'logo',
        'banner_image',
        'default_menu_image',
        'whatsapp_number',
        'phone_number',
        'email',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'currency',
        'business_hours',
        'is_active',
        'is_verified',
        'is_open',
        'opening_time',
        'closing_time',
        'weekly_schedule',
        'status_message',
        'auto_open_close',
        'theme_color',
        'secondary_color',
        'settings',
        'custom_domain',
        'custom_domain_verified',
        'custom_domain_verified_at',
        'custom_domain_status',
    ];

    protected $casts = [
        'business_hours' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'is_open' => 'boolean',
        'opening_time' => 'datetime',
        'closing_time' => 'datetime',
        'weekly_schedule' => 'array',
        'auto_open_close' => 'boolean',
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function managers()
    {
        return $this->hasMany(Manager::class);
    }

    public function getManagersAttribute()
    {
        return $this->managers()->with('user')->get();
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function stories()
    {
        return $this->hasMany(Story::class);
    }

    public function tableQrs()
    {
        return $this->hasMany(TableQR::class);
    }

    public function guestSessions()
    {
        return $this->hasMany(GuestSession::class);
    }

    public function userRewards()
    {
        return $this->hasMany(UserReward::class);
    }

    public function deliverySetting()
    {
        return $this->hasOne(RestaurantDeliverySetting::class);
    }

    public function images()
    {
        return $this->hasMany(RestaurantImage::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function ratings()
    {
        return $this->hasMany(RestaurantRating::class);
    }

    public function featuredRestaurants()
    {
        return $this->hasMany(FeaturedRestaurant::class);
    }

    public function socialMediaCampaigns()
    {
        return $this->hasMany(SocialMediaCampaign::class);
    }

    public function videoPackages()
    {
        return $this->hasMany(VideoPackage::class);
    }

    public function subscription()
    {
        return $this->hasOne(RestaurantSubscription::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(RestaurantSubscription::class);
    }

    public function getActiveSubscriptionAttribute()
    {
        return $this->subscription()->where(function($query) {
            $query->where('status', 'active')
                  ->orWhere(function($q) {
                      $q->where('status', 'trial')
                        ->where('trial_ends_at', '>', now());
                  });
        })->first();
    }

    public function hasActiveSubscription()
    {
        $subscription = $this->activeSubscription;
        return $subscription && ($subscription->isActive() || $subscription->isTrial());
    }

    public function canAddMenuItem()
    {
        $subscription = $this->activeSubscription;
        if (!$subscription) {
            return false;
        }
        return $subscription->canAddMenuItem();
    }

    public function canUseCustomDomain()
    {
        $subscription = $this->activeSubscription;
        return $subscription && $subscription->canUseCustomDomain();
    }

    public function canAccessVideoPackages()
    {
        $subscription = $this->activeSubscription;
        return $subscription && $subscription->canAccessVideoPackages();
    }

    public function canAccessSocialMediaPromotion()
    {
        $subscription = $this->activeSubscription;
        return $subscription && $subscription->canAccessSocialMediaPromotion();
    }

    // Default Image Methods
    public function hasCustomDefaultImage()
    {
        // Check if restaurant has premium subscription that allows custom placeholder images
        $subscription = $this->activeSubscription;
        if (!$subscription || $subscription->plan_type !== 'premium') {
            return false;
        }
        
        return !empty($this->default_menu_image) && \Storage::disk('public')->exists($this->default_menu_image);
    }

    public function getDefaultImageUrlAttribute()
    {
        if ($this->hasCustomDefaultImage()) {
            $url = \Storage::disk('public')->url($this->default_menu_image);
            return \App\Helpers\PWAHelper::fixStorageUrl($url);
        }
        
        // Fallback to system default placeholder
        $placeholderUrl = \Storage::disk('public')->url('restaurants/defaults/placeholder-menu-item.jpg');
        return \App\Helpers\PWAHelper::fixStorageUrl($placeholderUrl);
    }

    public function getDefaultImageThumbnailUrlAttribute()
    {
        if ($this->hasCustomDefaultImage()) {
            // Try to get thumbnail version
            $pathInfo = pathinfo($this->default_menu_image);
            $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];
            
            if (\Storage::disk('public')->exists($thumbnailPath)) {
                $url = \Storage::disk('public')->url($thumbnailPath);
                return \App\Helpers\PWAHelper::fixStorageUrl($url);
            }
            
            // If no thumbnail exists, return the main image
            return $this->default_image_url;
        }
        
        // Fallback to system default placeholder
        $placeholderUrl = \Storage::disk('public')->url('restaurants/defaults/placeholder-menu-item.jpg');
        return \App\Helpers\PWAHelper::fixStorageUrl($placeholderUrl);
    }

    public function getVisibleMenuItemsAttribute()
    {
        $subscription = $this->activeSubscription;
        if (!$subscription) {
            return $this->menuItems()->with('restaurant')->limit(5)->get();
        }
        
        // During trial, show all menu items
        if ($subscription->isTrial()) {
            return $this->menuItems()->with('restaurant')->get();
        }
        
        if ($subscription->unlimited_menu_items) {
            return $this->menuItems()->with('restaurant')->get();
        }
        
        return $this->menuItems()->with('restaurant')->limit($subscription->menu_item_limit)->get();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }

    public function scopeClosed($query)
    {
        return $query->where('is_open', false);
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);

        return implode(', ', $parts);
    }

    public function getDisplayNameAttribute()
    {
        return $this->name;
    }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating');
    }

    // Methods
    public function generateSlug()
    {
        $baseSlug = Str::slug($this->name);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function getQrCodeUrl($tableNumber)
    {
        return url("/qr/{$this->slug}/table/{$tableNumber}");
    }

    public function getDashboardUrl()
    {
        return route('restaurant.dashboard', $this->slug);
    }

    public function getCustomDomainUrl()
    {
        if ($this->custom_domain && $this->custom_domain_verified) {
            return 'https://' . $this->custom_domain;
        }
        return null;
    }

    public function getMenuUrl()
    {
        if ($this->custom_domain && $this->custom_domain_verified) {
            return 'https://' . $this->custom_domain;
        }
        return route('menu.restaurant', $this->slug);
    }

    /**
     * Get default menu image URL with fallback to global placeholder
     */
    public function getDefaultMenuImageUrlAttribute()
    {
        try {
            if ($this->default_menu_image && \Storage::disk('public')->exists($this->default_menu_image)) {
                $url = \Storage::disk('public')->url($this->default_menu_image);
                return \App\Helpers\PWAHelper::fixStorageUrl($url);
            }
        } catch (\Exception $e) {
            \Log::error('Error getting default menu image url', [
                'restaurant_id' => $this->id,
                'path' => $this->default_menu_image,
                'error' => $e->getMessage()
            ]);
        }
        return \App\Helpers\PWAHelper::getPlaceholderImage('square');
    }

    /**
     * Check if restaurant can set custom default image (Premium or Trial only)
     */
    public function canSetCustomDefaultImage()
    {
        $subscription = $this->activeSubscription;
        if (!$subscription) {
            return false;
        }
        
        // Allow if on trial or has premium subscription
        return $subscription->isTrial() || 
               ($subscription->plan_type === 'premium' && ($subscription->isActive() || $subscription->isTrial()));
    }

    public function hasCustomDomain()
    {
        return !empty($this->custom_domain);
    }

    public function verifyCustomDomain()
    {
        // Implementation for domain verification
        return true;
    }

    /**
     * Get the logo URL with fallback
     */
    public function getLogoUrlAttribute()
    {
        try {
            if ($this->logo && \Storage::disk('public')->exists($this->logo)) {
                $url = \Storage::disk('public')->url($this->logo);
                
                $url = \App\Helpers\PWAHelper::fixStorageUrl($url);
                
                \Log::info('Restaurant model logo_url generated', [
                    'restaurant_id' => $this->id,
                    'logo_path' => $this->logo,
                    'logo_url' => $url,
                    'exists' => \Storage::disk('public')->exists($this->logo)
                ]);
                return $url;
            }
            
            \Log::warning('Restaurant model logo_url - file not found, using placeholder', [
                'restaurant_id' => $this->id,
                'logo_path' => $this->logo,
                'exists' => $this->logo ? \Storage::disk('public')->exists($this->logo) : false
            ]);
            
            // Return placeholder logo URL
            $placeholderUrl = \Storage::disk('public')->url('restaurants/logos/placeholder-logo.svg');
            $placeholderUrl = \App\Helpers\PWAHelper::fixStorageUrl($placeholderUrl);
            
            return $placeholderUrl;
        } catch (\Exception $e) {
            \Log::error('Error getting restaurant logo URL from model', [
                'restaurant_id' => $this->id,
                'logo_path' => $this->logo,
                'error' => $e->getMessage()
            ]);
            
            // Return placeholder logo URL as fallback
            $placeholderUrl = \Storage::disk('public')->url('restaurants/logos/placeholder-logo.svg');
            $placeholderUrl = \App\Helpers\PWAHelper::fixStorageUrl($placeholderUrl);
            
            return $placeholderUrl;
        }
    }

    /**
     * Get the banner URL with fallback
     */
    public function getBannerUrlAttribute()
    {
        try {
            if ($this->banner_image && \Storage::disk('public')->exists($this->banner_image)) {
                $url = \Storage::disk('public')->url($this->banner_image);
                $url = \App\Helpers\PWAHelper::fixStorageUrl($url);
                
                \Log::info('Restaurant model banner_url generated', [
                    'restaurant_id' => $this->id,
                    'banner_path' => $this->banner_image,
                    'banner_url' => $url,
                    'exists' => \Storage::disk('public')->exists($this->banner_image)
                ]);
                return $url;
            }
            
            \Log::warning('Restaurant model banner_url - file not found, using placeholder', [
                'restaurant_id' => $this->id,
                'banner_path' => $this->banner_image,
                'exists' => $this->banner_image ? \Storage::disk('public')->exists($this->banner_image) : false
            ]);
            
            // Return placeholder banner URL
            $placeholderUrl = \Storage::disk('public')->url('restaurants/banners/placeholder-banner.jpg');
            $placeholderUrl = \App\Helpers\PWAHelper::fixStorageUrl($placeholderUrl);
            
            return $placeholderUrl;
        } catch (\Exception $e) {
            \Log::error('Error getting restaurant banner URL', [
                'restaurant_id' => $this->id,
                'banner_path' => $this->banner_image,
                'error' => $e->getMessage()
            ]);
            
            // Return placeholder banner URL as fallback
            $placeholderUrl = \Storage::disk('public')->url('restaurants/banners/placeholder-banner.jpg');
            $placeholderUrl = \App\Helpers\PWAHelper::fixStorageUrl($placeholderUrl);
            
            return $placeholderUrl;
        }
    }

    /**
     * Get today's earnings (confirmed orders from today)
     */
    public function getTodayEarningsAttribute()
    {
        return $this->orders()
            ->where('status', 'confirmed')
            ->whereDate('created_at', today())
            ->sum('total_amount');
    }

    /**
     * Get today's pay-on-delivery earnings
     */
    public function getTodayPayOnDeliveryEarningsAttribute()
    {
        return $this->orders()
            ->where('payment_method', 'cash')
            ->where('status', 'confirmed')
            ->whereDate('created_at', today())
            ->sum('total_amount');
    }

    /**
     * Get today's online payment earnings
     */
    public function getTodayOnlinePaymentEarningsAttribute()
    {
        return $this->orders()
            ->whereIn('payment_method', ['card', 'transfer', 'wallet'])
            ->where('status', 'confirmed')
            ->whereDate('created_at', today())
            ->sum('total_amount');
    }

    /**
     * Check if order affects today's earnings
     */
    public function orderAffectsTodayEarnings($order)
    {
        // Only confirmed orders from today affect earnings
        return $order->status === 'confirmed' && 
               $order->created_at->isToday() &&
               $order->restaurant_id === $this->id;
    }

    /**
     * Handle order status change impact on earnings
     */
    public function handleOrderStatusChange($order, $oldStatus, $newStatus)
    {
        $isPayOnDelivery = $order->payment_method === 'cash';
        $amount = $order->total_amount;
        
        \Log::info('Order status change earnings impact', [
            'restaurant_id' => $this->id,
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'payment_method' => $order->payment_method,
            'is_pay_on_delivery' => $isPayOnDelivery,
            'amount' => $amount,
            'created_today' => $order->created_at->isToday()
        ]);

        // Only handle orders created today
        if (!$order->created_at->isToday()) {
            return;
        }

        // Pay-on-delivery orders: Add to earnings when confirmed, remove when cancelled
        if ($isPayOnDelivery) {
            if ($oldStatus !== 'confirmed' && $newStatus === 'confirmed') {
                \Log::info('Pay-on-delivery order confirmed - adding to today\'s earnings', [
                    'order_id' => $order->id,
                    'amount' => $amount
                ]);
            } elseif ($oldStatus === 'confirmed' && $newStatus === 'cancelled') {
                \Log::info('Pay-on-delivery order cancelled - removing from today\'s earnings', [
                    'order_id' => $order->id,
                    'amount' => $amount
                ]);
            }
        }
        
        // Online payment orders: Already paid, so they always count when confirmed
        // (no special handling needed as they're already paid)
    }

    /**
     * Check if restaurant is currently open
     */
    public function isCurrentlyOpen()
    {
        if (!$this->is_open) {
            return false;
        }

        if ($this->auto_open_close && $this->opening_time && $this->closing_time) {
            $now = now();
            $openingTime = \Carbon\Carbon::parse($this->opening_time);
            $closingTime = \Carbon\Carbon::parse($this->closing_time);
            
            // Handle overnight hours (e.g., 22:00 - 06:00)
            if ($closingTime < $openingTime) {
                return $now->gte($openingTime) || $now->lte($closingTime);
            }
            
            return $now->gte($openingTime) && $now->lte($closingTime);
        }

        return $this->is_open;
    }

    /**
     * Get open/close status display
     */
    public function getStatusDisplayAttribute()
    {
        if ($this->isCurrentlyOpen()) {
            return [
                'status' => 'open',
                'text' => 'Open',
                'color' => 'green',
                'icon' => 'fas fa-circle'
            ];
        } else {
            return [
                'status' => 'closed',
                'text' => 'Closed',
                'color' => 'red',
                'icon' => 'fas fa-circle'
            ];
        }
    }

    /**
     * Get formatted business hours
     */
    public function getFormattedBusinessHoursAttribute()
    {
        if ($this->opening_time && $this->closing_time) {
            return $this->opening_time->format('g:i A') . ' - ' . $this->closing_time->format('g:i A');
        }
        return 'Hours not set';
    }

    /**
     * Get status message
     */
    public function getStatusMessageAttribute()
    {
        if ($this->attributes['status_message'] ?? null) {
            return $this->attributes['status_message'];
        }
        
        if (!$this->isCurrentlyOpen()) {
            if ($this->opening_time && $this->closing_time) {
                return "We're closed. Open today from " . $this->opening_time->format('g:i A') . " to " . $this->closing_time->format('g:i A');
            }
            return "We're currently closed";
        }
        
        return "We're open now";
    }

    /**
     * Toggle open/close status
     */
    public function toggleStatus()
    {
        $this->update(['is_open' => !$this->is_open]);
        return $this->is_open;
    }

    /**
     * Set restaurant as open
     */
    public function open()
    {
        $this->update(['is_open' => true]);
    }

    /**
     * Set restaurant as closed
     */
    public function close()
    {
        $this->update(['is_open' => false]);
    }

    /**
     * Boot method to automatically create free subscription for new restaurants
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($restaurant) {
            // Create free subscription for new restaurants
            $freePlan = \App\Models\SubscriptionPlan::where('slug', 'free')->first();
            
            if ($freePlan) {
                \App\Models\RestaurantSubscription::create([
                    'restaurant_id' => $restaurant->id,
                    'plan_type' => 'free',
                    'status' => 'active',
                    'menu_item_limit' => $freePlan->menu_item_limit,
                    'custom_domain_enabled' => $freePlan->custom_domain_enabled,
                    'unlimited_menu_items' => $freePlan->unlimited_menu_items,
                    'priority_support' => $freePlan->priority_support,
                    'advanced_analytics' => $freePlan->advanced_analytics,
                    'video_packages_enabled' => $freePlan->video_packages_enabled,
                    'social_media_promotion_enabled' => $freePlan->social_media_promotion_enabled,
                    'features' => $freePlan->features,
                    'monthly_fee' => $freePlan->monthly_price,
                ]);

                \Log::info('Free subscription created for new restaurant', [
                    'restaurant_id' => $restaurant->id,
                    'restaurant_name' => $restaurant->name,
                    'plan_type' => 'free'
                ]);
            }
        });
    }
}
