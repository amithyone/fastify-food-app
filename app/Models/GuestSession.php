<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuestSession extends Model
{
    protected $fillable = [
        'restaurant_id',
        'table_qr_id',
        'session_id',
        'table_number',
        'cart_data',
        'customer_info',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'cart_data' => 'array',
        'customer_info' => 'array',
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Get the restaurant that owns the guest session
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get the table QR that owns the guest session
     */
    public function tableQR(): BelongsTo
    {
        return $this->belongsTo(TableQR::class);
    }

    /**
     * Get the orders for this guest session
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'session_id', 'session_id');
    }

    /**
     * Generate a unique session ID
     */
    public static function generateSessionId(): string
    {
        return 'GS-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }

    /**
     * Check if session is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Extend session expiry
     */
    public function extendSession(int $hours = 24): void
    {
        $this->update([
            'expires_at' => now()->addHours($hours)
        ]);
    }

    /**
     * Get active session by session ID
     */
    public static function getActiveSession(string $sessionId): ?self
    {
        return static::where('session_id', $sessionId)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();
    }
}
