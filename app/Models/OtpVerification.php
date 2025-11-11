<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'otp_code',
        'type',
        'is_verified',
        'expires_at',
        'verified_at',
        'attempts'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'attempts' => 'integer',
    ];

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
                    ->where('is_verified', false);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
