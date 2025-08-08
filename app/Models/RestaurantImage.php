<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RestaurantImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'original_name',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'alt_text',
        'is_used'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_used' => 'boolean',
    ];

    /**
     * Get the restaurant that owns the image
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get the full URL for the image
     */
    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->file_path);
    }

    /**
     * Get the thumbnail URL for the image
     */
    public function getThumbnailUrlAttribute()
    {
        $pathInfo = pathinfo($this->file_path);
        $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];
        
        if (Storage::disk('public')->exists($thumbnailPath)) {
            return Storage::disk('public')->url($thumbnailPath);
        }
        
        return $this->url;
    }

    /**
     * Check if the image file exists
     */
    public function getExistsAttribute()
    {
        return Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope to get unused images
     */
    public function scopeUnused($query)
    {
        return $query->where('is_used', false);
    }

    /**
     * Scope to get used images
     */
    public function scopeUsed($query)
    {
        return $query->where('is_used', true);
    }

    /**
     * Mark image as used
     */
    public function markAsUsed()
    {
        $this->update(['is_used' => true]);
    }

    /**
     * Mark image as unused
     */
    public function markAsUnused()
    {
        $this->update(['is_used' => false]);
    }
}
