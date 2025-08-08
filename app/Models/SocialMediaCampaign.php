<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'campaign_name',
        'description',
        'platform',
        'status',
        'budget',
        'start_date',
        'end_date',
        'target_audience',
        'content_plan',
        'hashtags',
        'call_to_action',
        'landing_page_url',
        'impressions',
        'clicks',
        'engagements',
        'roi'
    ];

    protected $casts = [
        'target_audience' => 'array',
        'content_plan' => 'array',
        'budget' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'roi' => 'decimal:2'
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getPlatformIconAttribute()
    {
        return match($this->platform) {
            'instagram' => 'fab fa-instagram',
            'facebook' => 'fab fa-facebook',
            'twitter' => 'fab fa-twitter',
            'tiktok' => 'fab fa-tiktok',
            'youtube' => 'fab fa-youtube',
            'all' => 'fas fa-share-alt',
            default => 'fas fa-share'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'pending' => 'yellow',
            'active' => 'green',
            'completed' => 'blue',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getFormattedBudgetAttribute()
    {
        return $this->budget ? '₦' . number_format($this->budget, 2) : 'Not set';
    }

    public function getEngagementRateAttribute()
    {
        if ($this->impressions === 0) return 0;
        return round(($this->engagements / $this->impressions) * 100, 2);
    }
}
