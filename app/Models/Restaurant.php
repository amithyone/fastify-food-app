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
        return !empty($this->custom_domain) && $this->custom_domain_verified;
    }

    public function verifyCustomDomain()
    {
        // This would typically involve checking DNS records
        // For now, we'll simulate verification
        $this->update([
            'custom_domain_verified' => true,
            'custom_domain_verified_at' => now(),
            'custom_domain_status' => 'verified'
        ]);
    }
}
