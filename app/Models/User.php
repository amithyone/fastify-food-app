<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'restaurant_id',
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
        return $this->belongsTo(Restaurant::class);
    }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class, 'owner_id');
    }

    /**
     * Check if user is a restaurant owner.
     */
    public function isRestaurantOwner()
    {
        return !is_null($this->restaurant_id);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\EmailVerificationNotification);
    }
}
