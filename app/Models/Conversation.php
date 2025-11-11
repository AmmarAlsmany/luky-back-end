<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'client_id',
        'provider_id',
        'booking_id',
        'last_message_id',
        'last_message_at',
        'client_unread_count',
        'provider_unread_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'client_unread_count' => 'integer',
        'provider_unread_count' => 'integer',
    ];

    /**
     * Get the client (user) in this conversation
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Alias for client (for admin panel compatibility)
     */
    public function user(): BelongsTo
    {
        return $this->client();
    }

    /**
     * Get the provider in this conversation
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id');
    }

    /**
     * Get the booking associated with this conversation
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get all messages in this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the last message in this conversation
     */
    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    /**
     * Scope to get conversations for a specific user (client or provider)
     */
    public function scopeForUser($query, int $userId, string $userType)
    {
        if ($userType === 'client') {
            return $query->where('client_id', $userId);
        } elseif ($userType === 'provider') {
            return $query->where('provider_id', $userId);
        }
        return $query;
    }

    /**
     * Increment unread count for a specific user type
     */
    public function incrementUnreadCount(string $recipientType): void
    {
        if ($recipientType === 'client') {
            $this->increment('client_unread_count');
        } elseif ($recipientType === 'provider') {
            $this->increment('provider_unread_count');
        }
    }

    /**
     * Reset unread count for a specific user type
     */
    public function resetUnreadCount(string $userType): void
    {
        if ($userType === 'client') {
            $this->update(['client_unread_count' => 0]);
        } elseif ($userType === 'provider') {
            $this->update(['provider_unread_count' => 0]);
        }
    }
}
