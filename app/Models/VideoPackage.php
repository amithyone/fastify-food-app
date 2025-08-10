<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'package_name',
        'description',
        'package_type',
        'status',
        'price',
        'video_duration',
        'number_of_videos',
        'video_requirements',
        'deliverables',
        'shoot_date',
        'shoot_time',
        'location_address',
        'contact_person',
        'contact_phone',
        'special_instructions',
        'dishes_to_film',
        'staff_contact',
        'filming_requirements',
        'video_file_path',
        'thumbnail_path',
        'social_media_links',
        'views',
        'shares',
        'engagements'
    ];

    protected $casts = [
        'video_requirements' => 'array',
        'deliverables' => 'array',
        'price' => 'decimal:2',
        'shoot_date' => 'date',
        'shoot_time' => 'datetime',
        'social_media_links' => 'array'
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProduction($query)
    {
        return $query->where('status', 'in_production');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function getPackageTypeColorAttribute()
    {
        return match($this->package_type) {
            'basic' => 'blue',
            'premium' => 'purple',
            'custom' => 'orange',
            default => 'gray'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'in_production' => 'blue',
            'completed' => 'green',
            'delivered' => 'purple',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getFormattedPriceAttribute()
    {
        return '₦' . number_format($this->price, 2);
    }

    public function getFormattedDurationAttribute()
    {
        $minutes = floor($this->video_duration / 60);
        $seconds = $this->video_duration % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getVideoUrlAttribute()
    {
        return $this->video_file_path ? asset('storage/' . $this->video_file_path) : null;
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail_path ? asset('storage/' . $this->thumbnail_path) : null;
    }

    public function getEngagementRateAttribute()
    {
        if ($this->views === 0) return 0;
        return round(($this->engagements / $this->views) * 100, 2);
    }
}
