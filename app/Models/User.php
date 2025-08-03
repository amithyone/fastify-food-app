<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Manager;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'phone_verified_at',
        'default_address',
        'city',
        'state',
        'postal_code',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Get the addresses for the user.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the default address for the user.
     */
    public function defaultAddress()
    {
        return $this->belongsTo(Address::class, 'default_address');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function rewards()
    {
        return $this->hasMany(UserReward::class);
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function getWalletOrCreate()
    {
        return $this->wallet ?? $this->wallet()->create([
            'balance' => 0,
            'points' => 0
        ]);
    }

    /**
     * Get the full address as a formatted string.
     */
    public function getFullAddressAttribute()
    {
        if ($this->default_address) {
            $parts = [
                $this->default_address,
                $this->city,
                $this->state,
                $this->postal_code,
            ];

            return implode(', ', array_filter($parts));
        }

        return null;
    }

    /**
     * Get the restaurant that the user manages.
     */
    public function restaurant()
    {
        return $this->hasOne(Restaurant::class, 'owner_id');
    }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class, 'owner_id');
    }

    public function managedRestaurants()
    {
        return $this->hasMany(Manager::class);
    }

    public function getRestaurantsAttribute()
    {
        return $this->managedRestaurants()->with('restaurant')->get()->pluck('restaurant');
    }

    /**
     * Check if user is a restaurant owner.
     */
    public function isRestaurantOwner()
    {
        return $this->managedRestaurants()->where('role', 'owner')->exists();
    }

    /**
     * Check if user is a restaurant manager.
     */
    public function isRestaurantManager()
    {
        return $this->managedRestaurants()->whereIn('role', ['owner', 'manager'])->exists();
    }

    /**
     * Get user's primary restaurant (first one they manage).
     */
    public function getPrimaryRestaurantAttribute()
    {
        return $this->managedRestaurants()->with('restaurant')->first()?->restaurant;
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Check if user can manage a specific restaurant
     */
    public function canManageRestaurant($restaurant)
    {
        // Admins can manage all restaurants
        if ($this->isAdmin()) {
            return true;
        }

        // Check if user is a manager of this restaurant
        return Manager::canAccessRestaurant($this->id, $restaurant->id, 'manager');
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\EmailVerificationNotification);
    }
}
