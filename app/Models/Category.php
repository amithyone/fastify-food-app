<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'parent_id',
        'type',
        'sort_order',
        'restaurant_id',
        'restaurant_ids',
        'is_shared'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_shared' => 'boolean',
        'restaurant_ids' => 'array',
    ];

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function scopeMainCategories($query)
    {
        return $query->where('type', 'main')->whereNull('parent_id');
    }

    public function scopeSubCategories($query)
    {
        return $query->where('type', 'sub')->whereNotNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // New scopes for global categories
    public function scopeGlobal($query)
    {
        return $query->whereNull('restaurant_id');
    }

    public function scopeRestaurantSpecific($query)
    {
        return $query->whereNotNull('restaurant_id');
    }

    public function scopeGlobalMainCategories($query)
    {
        return $query->where('type', 'main')
                    ->whereNull('parent_id')
                    ->whereNull('restaurant_id');
    }

    public function scopeGlobalSubCategories($query)
    {
        return $query->where('type', 'sub')
                    ->whereNotNull('parent_id')
                    ->whereNull('restaurant_id');
    }

    public function scopeAvailableForRestaurant($query, $restaurantId)
    {
        return $query->where(function($q) use ($restaurantId) {
            $q->whereNull('restaurant_id') // Global categories
              ->orWhere('restaurant_id', $restaurantId) // Restaurant-specific categories
              ->orWhereRaw('JSON_CONTAINS(restaurant_ids, ?)', [json_encode($restaurantId)]); // Shared categories
        });
    }

    // Helper method to check if category is global
    public function isGlobal()
    {
        return is_null($this->restaurant_id);
    }

    // Helper method to check if category is restaurant-specific
    public function isRestaurantSpecific()
    {
        return !is_null($this->restaurant_id);
    }

    // Helper method to check if category is shared
    public function isShared()
    {
        return $this->is_shared && !empty($this->restaurant_ids);
    }

    // Helper method to check if a restaurant can use this category
    public function canBeUsedByRestaurant($restaurantId)
    {
        if ($this->isGlobal()) {
            return true;
        }
        
        if ($this->restaurant_id == $restaurantId) {
            return true;
        }
        
        if ($this->isShared() && in_array($restaurantId, $this->restaurant_ids ?? [])) {
            return true;
        }
        
        return false;
    }

    // Helper method to add a restaurant to shared category
    public function addRestaurant($restaurantId)
    {
        if (!$this->isShared()) {
            $this->is_shared = true;
            $this->restaurant_ids = [];
        }
        
        if (!in_array($restaurantId, $this->restaurant_ids ?? [])) {
            $this->restaurant_ids = array_merge($this->restaurant_ids ?? [], [$restaurantId]);
        }
        
        return $this->save();
    }

    // Helper method to remove a restaurant from shared category
    public function removeRestaurant($restaurantId)
    {
        if ($this->isShared() && !empty($this->restaurant_ids)) {
            $this->restaurant_ids = array_filter($this->restaurant_ids, function($id) use ($restaurantId) {
                return $id != $restaurantId;
            });
            
            // If no restaurants left, unshare the category
            if (empty($this->restaurant_ids)) {
                $this->is_shared = false;
            }
            
            return $this->save();
        }
        
        return false;
    }

    // Static method to find or create a shared category
    public static function findOrCreateShared($name, $restaurantId, $parentId = null, $type = 'main')
    {
        // First, try to find an existing category with the same name
        $existingCategory = self::where('name', $name)
                                ->where('type', $type)
                                ->where(function($query) use ($parentId) {
                                    if ($parentId) {
                                        $query->where('parent_id', $parentId);
                                    } else {
                                        $query->whereNull('parent_id');
                                    }
                                })
                                ->first();

        if ($existingCategory) {
            // If category exists, add this restaurant to it
            $existingCategory->addRestaurant($restaurantId);
            return $existingCategory;
        }

        // If no existing category found, create a new shared one
        $category = new self([
            'name' => $name,
            'type' => $type,
            'parent_id' => $parentId,
            'is_active' => true,
            'is_shared' => true,
            'restaurant_ids' => [$restaurantId],
            'sort_order' => 0,
        ]);

        // Generate unique slug
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;
        
        while (self::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        $category->slug = $slug;
        $category->save();

        return $category;
    }

    // Static method to find similar categories for matching
    public static function findSimilar($name, $parentId = null, $type = 'main')
    {
        $query = self::where('name', 'LIKE', '%' . $name . '%')
                    ->orWhere('name', 'LIKE', '%' . Str::slug($name) . '%')
                    ->where('type', $type);

        if ($parentId) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        return $query->get();
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
