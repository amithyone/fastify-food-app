<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PhoneVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number',
        'verification_code',
        'expires_at',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    /**
     * Generate a new verification code for a phone number
     */
    public static function generateCode(string $phoneNumber): self
    {
        // Delete any existing unverified codes for this phone number
        self::where('phone_number', $phoneNumber)
            ->where('is_verified', false)
            ->delete();

        // Generate a 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Set expiry to 10 minutes from now
        $expiresAt = Carbon::now()->addMinutes(10);

        return self::create([
            'phone_number' => $phoneNumber,
            'verification_code' => $code,
            'expires_at' => $expiresAt,
            'is_verified' => false,
        ]);
    }

    /**
     * Verify a code for a phone number
     */
    public static function verifyCode(string $phoneNumber, string $code): bool
    {
        $verification = self::where('phone_number', $phoneNumber)
            ->where('verification_code', $code)
            ->where('is_verified', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($verification) {
            $verification->update([
                'is_verified' => true,
                'verified_at' => Carbon::now(),
            ]);
            return true;
        }

        return false;
    }

    /**
     * Check if a phone number is verified
     */
    public static function isVerified(string $phoneNumber): bool
    {
        return self::where('phone_number', $phoneNumber)
            ->where('is_verified', true)
            ->exists();
    }

    /**
     * Get the latest verification for a phone number
     */
    public static function getLatest(string $phoneNumber): ?self
    {
        return self::where('phone_number', $phoneNumber)
            ->latest()
            ->first();
    }

    /**
     * Check if the verification code has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Get remaining time in minutes
     */
    public function getRemainingMinutes(): int
    {
        return max(0, Carbon::now()->diffInMinutes($this->expires_at, false));
    }
}
