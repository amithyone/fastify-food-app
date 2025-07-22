<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TableQR extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'table_number',
        'qr_code',
        'short_url',
        'is_active',
        'last_used_at',
        'usage_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function guestSessions()
    {
        return $this->hasMany(GuestSession::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function generateQrCode()
    {
        $this->qr_code = 'QR_' . Str::random(16);
        $this->short_url = Str::random(8);
        $this->save();
    }

    public function getFullUrl()
    {
        return url("/qr/{$this->restaurant->slug}/table/{$this->table_number}");
    }

    public function getShortUrl()
    {
        return url("/s/{$this->short_url}");
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    public function isRecentlyUsed($minutes = 30)
    {
        return $this->last_used_at && $this->last_used_at->diffInMinutes(now()) < $minutes;
    }
}
