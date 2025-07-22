<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
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
    ];

    protected $casts = [
        'business_hours' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
    ];

    // Relationships
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

    public function getMenuUrl()
    {
        return url("/menu/{$this->slug}");
    }

    public function getDashboardUrl()
    {
        return url("/restaurant/{$this->slug}/dashboard");
    }
}
