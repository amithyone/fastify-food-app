<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'ingredients',
        'allergens',
        'price',
        'category_id',
        'image',
        'is_available',
        'is_featured',
        'is_available_for_delivery',
        'is_vegetarian',
        'is_spicy',
        'restaurant_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_available_for_delivery' => 'boolean',
        'is_vegetarian' => 'boolean',
        'is_spicy' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function deliveryMethods()
    {
        return $this->hasMany(MenuItemDeliveryMethod::class);
    }

    public function getFormattedPriceAttribute()
    {
        return '₦' . number_format($this->price, 0);
    }

    /**
     * Get the image URL with fallback to restaurant's default image
     */
    public function getImageUrlAttribute()
    {
        // If menu item has its own image, use it
        if ($this->image && \Storage::disk('public')->exists($this->image)) {
            $url = \Storage::disk('public')->url($this->image);
            return \App\Helpers\PWAHelper::fixStorageUrl($url);
        }
        
        // If restaurant has a custom default image, use it
        if ($this->restaurant && $this->restaurant->hasCustomDefaultImage()) {
            return $this->restaurant->default_image_url;
        }
        
        // Fallback to system default placeholder
        $placeholderUrl = \Storage::disk('public')->url('restaurants/defaults/placeholder-menu-item.jpg');
        return \App\Helpers\PWAHelper::fixStorageUrl($placeholderUrl);
    }

    /**
     * Get the image thumbnail URL with fallback
     */
    public function getImageThumbnailUrlAttribute()
    {
        // If menu item has its own image, try to get thumbnail
        if ($this->image) {
            $pathInfo = pathinfo($this->image);
            $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];
            
            if (\Storage::disk('public')->exists($thumbnailPath)) {
                $url = \Storage::disk('public')->url($thumbnailPath);
                return \App\Helpers\PWAHelper::fixStorageUrl($url);
            }
        }
        
        // If restaurant has a custom default image, use its thumbnail
        if ($this->restaurant && $this->restaurant->hasCustomDefaultImage()) {
            return $this->restaurant->default_image_thumbnail_url;
        }
        
        // Fallback to system default placeholder
        $placeholderUrl = \Storage::disk('public')->url('restaurants/defaults/placeholder-menu-item.jpg');
        return \App\Helpers\PWAHelper::fixStorageUrl($placeholderUrl);
    }
}
