<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'booking_id',
        'client_id',
        'provider_id',
        'rating',
        'comment',
        'is_visible',
        'is_flagged',
        'flag_reason',
        'flagged_by',
        'flagged_at',
        'admin_response',
        'responded_by',
        'responded_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_visible' => 'boolean',
        'is_flagged' => 'boolean',
        'flagged_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the booking that was reviewed
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the client who wrote the review
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Alias for client (for consistency)
     */
    public function user(): BelongsTo
    {
        return $this->client();
    }

    /**
     * Get the provider being reviewed
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id');
    }

    /**
     * Get the service being reviewed (if applicable)
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Scope for visible reviews only
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope for a specific provider
     */
    public function scopeForProvider($query, int $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    /**
     * Scope for a specific rating
     */
    public function scopeWithRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }
}
