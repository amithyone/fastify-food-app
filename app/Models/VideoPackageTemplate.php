<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoPackageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'base_price',
        'video_duration',
        'number_of_videos',
        'features',
        'deliverables',
        'is_active',
        'sort_order',
        'color_scheme'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'video_duration' => 'integer',
        'number_of_videos' => 'integer',
        'features' => 'array',
        'deliverables' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('base_price', 'asc');
    }

    // Methods
    public function getFormattedPriceAttribute()
    {
        return '₦' . number_format($this->base_price, 0);
    }

    public function getFormattedDurationAttribute()
    {
        $minutes = floor($this->video_duration / 60);
        $seconds = $this->video_duration % 60;
        
        if ($minutes > 0) {
            return $seconds > 0 ? "{$minutes}m {$seconds}s" : "{$minutes}m";
        }
        return "{$seconds}s";
    }

    public function getDurationTextAttribute()
    {
        if ($this->video_duration < 60) {
            return $this->video_duration . ' seconds';
        } elseif ($this->video_duration < 120) {
            return '1 minute';
        } else {
            $minutes = floor($this->video_duration / 60);
            return $minutes . ' minutes';
        }
    }

    public function hasFeature($feature)
    {
        return in_array($feature, $this->features ?? []);
    }

    public function hasDeliverable($deliverable)
    {
        return in_array($deliverable, $this->deliverables ?? []);
    }

    public function getColorClassesAttribute()
    {
        return match($this->color_scheme) {
            'orange' => 'text-orange-600',
            'purple' => 'text-purple-600',
            'green' => 'text-green-600',
            'blue' => 'text-blue-600',
            'red' => 'text-red-600',
            default => 'text-gray-600'
        };
    }

    public function getBgColorClassesAttribute()
    {
        return match($this->color_scheme) {
            'orange' => 'bg-orange-50 dark:bg-orange-900',
            'purple' => 'bg-purple-50 dark:bg-purple-900',
            'green' => 'bg-green-50 dark:bg-green-900',
            'blue' => 'bg-blue-50 dark:bg-blue-900',
            'red' => 'bg-red-50 dark:bg-red-900',
            default => 'bg-gray-50 dark:bg-gray-900'
        };
    }
}
