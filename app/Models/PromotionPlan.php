<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration_days',
        'max_impressions',
        'features',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'integer',
        'duration_days' => 'integer',
        'max_impressions' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Relationships
    public function payments()
    {
        return $this->hasMany(PromotionPayment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('price', 'asc');
    }

    // Methods
    public function getFormattedPriceAttribute()
    {
        return '₦' . number_format($this->price / 100, 0);
    }

    public function getFormattedPriceKoboAttribute()
    {
        return '₦' . number_format($this->price, 0);
    }

    public function getDurationTextAttribute()
    {
        if ($this->duration_days == 1) {
            return '1 day';
        } elseif ($this->duration_days < 7) {
            return $this->duration_days . ' days';
        } elseif ($this->duration_days == 7) {
            return '1 week';
        } elseif ($this->duration_days < 30) {
            return floor($this->duration_days / 7) . ' weeks';
        } elseif ($this->duration_days == 30) {
            return '1 month';
        } else {
            return floor($this->duration_days / 30) . ' months';
        }
    }

    public function hasFeature($feature)
    {
        return in_array($feature, $this->features ?? []);
    }

    public function getFeaturesListAttribute()
    {
        return $this->features ?? [];
    }
}
