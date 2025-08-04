<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RestaurantPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view restaurants
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Restaurant $restaurant): bool
    {
        return true; // Anyone can view a restaurant
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create a restaurant
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Restaurant $restaurant): bool
    {
        // Restaurant owner can update
        if ($user->id === $restaurant->owner_id) {
            return true;
        }

        // Restaurant managers can update
        if ($restaurant->managers()->where('user_id', $user->id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Restaurant $restaurant): bool
    {
        // Only restaurant owner can delete
        return $user->id === $restaurant->owner_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Restaurant $restaurant): bool
    {
        // Only restaurant owner can restore
        return $user->id === $restaurant->owner_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Restaurant $restaurant): bool
    {
        // Only restaurant owner can permanently delete
        return $user->id === $restaurant->owner_id;
    }

    /**
     * Determine whether the user can manage the restaurant.
     */
    public function manage(User $user, Restaurant $restaurant): bool
    {
        // Restaurant owner can manage
        if ($user->id === $restaurant->owner_id) {
            return true;
        }

        // Restaurant managers can manage
        if ($restaurant->managers()->where('user_id', $user->id)->exists()) {
            return true;
        }

        return false;
    }
} 