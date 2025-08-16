<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GuestUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'phone',
        'session_token',
        'session_expires_at',
        'is_active',
        'email_verified_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'session_expires_at' => 'datetime',
    ];

    /**
     * Get orders for this guest user
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Generate a new session token for QR code tracking
     */
    public function generateSessionToken()
    {
        $this->session_token = Str::random(32);
        $this->session_expires_at = Carbon::now()->addDays(7); // 7 days validity
        $this->save();
        
        return $this->session_token;
    }

    /**
     * Check if session token is valid
     */
    public function isSessionValid()
    {
        return $this->session_token && 
               $this->session_expires_at && 
               $this->session_expires_at->isFuture();
    }

    /**
     * Invalidate session token
     */
    public function invalidateSession()
    {
        $this->session_token = null;
        $this->session_expires_at = null;
        $this->save();
    }

    /**
     * Generate magic link for email-based login
     */
    public function generateMagicLink()
    {
        $token = Str::random(64);
        $expiresAt = Carbon::now()->addHours(24);
        
        // Store in cache for security
        cache()->put("guest_magic_link_{$token}", $this->id, $expiresAt);
        
        return route('guest.login.magic', ['token' => $token]);
    }

    /**
     * Verify magic link token
     */
    public static function verifyMagicLink($token)
    {
        $guestUserId = cache()->get("guest_magic_link_{$token}");
        
        if ($guestUserId) {
            cache()->forget("guest_magic_link_{$token}"); // One-time use
            return self::find($guestUserId);
        }
        
        return null;
    }

    /**
     * Create or find guest user by email
     */
    public static function findOrCreateByEmail($email, $name = null, $phone = null)
    {
        $guestUser = self::where('email', $email)->first();
        
        if (!$guestUser) {
            $guestUser = self::create([
                'email' => $email,
                'name' => $name,
                'phone' => $phone,
                'email_verified_at' => Carbon::now(), // Auto-verify for simplicity
            ]);
        }
        
        return $guestUser;
    }

    /**
     * Get QR code data for session tracking
     */
    public function getQrCodeData()
    {
        return [
            'type' => 'guest_session',
            'token' => $this->session_token,
            'user_id' => $this->id,
            'expires_at' => $this->session_expires_at?->toISOString(),
        ];
    }
}
