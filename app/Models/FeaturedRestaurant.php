<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FeaturedRestaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'title',
        'description',
        'ad_image',
        'cta_text',
        'cta_link',
        'badge_text',
        'badge_color',
        'sort_order',
        'is_active',
        'featured_from',
        'featured_until',
        'click_count',
        'impression_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'featured_from' => 'datetime',
        'featured_until' => 'datetime',
        'click_count' => 'integer',
        'impression_count' => 'integer',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrentlyFeatured($query)
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
                    ->where(function($q) use ($now) {
                        $q->whereNull('featured_from')
                          ->orWhere('featured_from', '<=', $now);
                    })
                    ->where(function($q) use ($now) {
                        $q->whereNull('featured_until')
                          ->orWhere('featured_until', '>=', $now);
                    })
                    ->whereIn('id', function($subquery) use ($now) {
                        $subquery->selectRaw('MAX(id)')
                                ->from('featured_restaurants')
                                ->where('is_active', true)
                                ->where(function($q) use ($now) {
                                    $q->whereNull('featured_from')
                                      ->orWhere('featured_from', '<=', $now);
                                })
                                ->where(function($q) use ($now) {
                                    $q->whereNull('featured_until')
                                      ->orWhere('featured_until', '>=', $now);
                                })
                                ->groupBy('restaurant_id');
                    });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    // Accessors
    public function getAdImageUrlAttribute()
    {
        if ($this->ad_image) {
            return \Storage::disk('public')->url($this->ad_image);
        }
        return $this->restaurant->logo_url ?? null;
    }

    public function getDisplayTitleAttribute()
    {
        return $this->title ?? $this->restaurant->name;
    }

    public function getDisplayDescriptionAttribute()
    {
        return $this->description ?? $this->restaurant->description;
    }

    public function getCtaUrlAttribute()
    {
        return $this->cta_link ?? "/menu/{$this->restaurant->slug}";
    }

    // Methods
    public function incrementClick()
    {
        $this->increment('click_count');
    }

    public function incrementImpression()
    {
        $this->increment('impression_count');
    }

    public function isCurrentlyFeatured()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        
        if ($this->featured_from && $now->lt($this->featured_from)) {
            return false;
        }

        if ($this->featured_until && $now->gt($this->featured_until)) {
            return false;
        }

        return true;
    }
}
