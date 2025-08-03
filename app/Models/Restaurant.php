<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'whatsapp_number',
        'phone_number',
        'email',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'currency',
        'business_hours',
        'is_active',
        'is_verified',
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

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function ratings()
    {
        return $this->hasMany(RestaurantRating::class);
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
            
            \Log::warning('Restaurant model logo_url - file not found', [
                'restaurant_id' => $this->id,
                'logo_path' => $this->logo,
                'exists' => $this->logo ? \Storage::disk('public')->exists($this->logo) : false
            ]);
            return null;
        } catch (\Exception $e) {
            \Log::error('Error getting restaurant logo URL from model', [
                'restaurant_id' => $this->id,
                'logo_path' => $this->logo,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get the banner URL with fallback
     */
    public function getBannerUrlAttribute()
    {
        try {
            if ($this->banner_image && \Storage::disk('public')->exists($this->banner_image)) {
                return \Storage::disk('public')->url($this->banner_image);
            }
            return null;
        } catch (\Exception $e) {
            \Log::error('Error getting restaurant banner URL', [
                'restaurant_id' => $this->id,
                'banner_path' => $this->banner_image,
                'error' => $e->getMessage()
            ]);
            return null;
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
}
