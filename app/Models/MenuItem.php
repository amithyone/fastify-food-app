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
        'restaurant_image_id',
        'is_available',
        'is_featured',
        'is_available_for_delivery',
        'is_available_for_pickup',
        'is_available_for_restaurant',
        'is_vegetarian',
        'is_spicy',
        'restaurant_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_available_for_delivery' => 'boolean',
        'is_available_for_pickup' => 'boolean',
        'is_available_for_restaurant' => 'boolean',
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

    public function restaurantImage()
    {
        return $this->belongsTo(RestaurantImage::class);
    }

    public function getFormattedPriceAttribute()
    {
        return '₦' . number_format($this->price, 0);
    }

    /**
     * Get the image URL with comprehensive fallback logic
     */
    public function getImageUrlAttribute()
    {
        // 1. If menu item has its own uploaded image, use it
        if ($this->image && \Storage::disk('public')->exists($this->image)) {
            $url = \Storage::disk('public')->url($this->image);
            return \App\Helpers\PWAHelper::fixStorageUrl($url);
        }
        
        // 2. If menu item references a restaurant image, use it
        if ($this->restaurant_image_id && $this->restaurantImage) {
            return $this->restaurantImage->url;
        }
        
        // 3. If restaurant is premium AND has custom placeholder image, use it
        if ($this->restaurant && $this->restaurant->hasCustomDefaultImage()) {
            return $this->restaurant->default_image_url;
        }
        
        // 4. Fallback to system default placeholder (for non-premium or premium without custom image)
        return asset('images/imageplaceholder.png');
    }

    /**
     * Get the image thumbnail URL with comprehensive fallback logic
     */
    public function getImageThumbnailUrlAttribute()
    {
        // 1. If menu item has its own image, try to get thumbnail
        if ($this->image) {
            $pathInfo = pathinfo($this->image);
            $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];
            
            if (\Storage::disk('public')->exists($thumbnailPath)) {
                $url = \Storage::disk('public')->url($thumbnailPath);
                return \App\Helpers\PWAHelper::fixStorageUrl($url);
            }
        }
        
        // 2. If menu item references a restaurant image, use its thumbnail
        if ($this->restaurant_image_id && $this->restaurantImage) {
            return $this->restaurantImage->thumbnail_url;
        }
        
        // 3. If restaurant is premium AND has custom placeholder image, use its thumbnail
        if ($this->restaurant && $this->restaurant->hasCustomDefaultImage()) {
            return $this->restaurant->default_image_thumbnail_url;
        }
        
        // 4. Fallback to system default placeholder (for non-premium or premium without custom image)
        return asset('images/imageplaceholder.png');
    }
}
