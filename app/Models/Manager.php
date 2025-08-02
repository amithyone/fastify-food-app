<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'role',
        'is_active',
        'permissions',
        'last_access_at',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
        'last_access_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Methods
    public function isOwner()
    {
        return $this->role === 'owner';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function hasPermission($permission)
    {
        if ($this->isOwner()) {
            return true; // Owners have all permissions
        }

        return in_array($permission, $this->permissions ?? []);
    }

    public function updateLastAccess()
    {
        $this->update(['last_access_at' => now()]);
    }

    // Static methods for authorization
    public static function canAccessRestaurant($userId, $restaurantId, $requiredRole = 'manager')
    {
        $manager = static::where('user_id', $userId)
            ->where('restaurant_id', $restaurantId)
            ->where('is_active', true)
            ->first();

        if (!$manager) {
            return false;
        }

        // Define role hierarchy
        $roleHierarchy = [
            'owner' => 3,
            'manager' => 2,
            'staff' => 1,
        ];

        $userRoleLevel = $roleHierarchy[$manager->role] ?? 0;
        $requiredRoleLevel = $roleHierarchy[$requiredRole] ?? 0;

        return $userRoleLevel >= $requiredRoleLevel;
    }

    public static function getUserRestaurants($userId)
    {
        return static::where('user_id', $userId)
            ->where('is_active', true)
            ->with('restaurant')
            ->get()
            ->pluck('restaurant');
    }

    public static function getRestaurantManagers($restaurantId)
    {
        return static::where('restaurant_id', $restaurantId)
            ->where('is_active', true)
            ->with('user')
            ->get();
    }
}
