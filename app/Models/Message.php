<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'message_type',
        'content',
        'image_path',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    protected $appends = [
        'image_url',
        'sender_name',
        'sender_avatar',
    ];

    /**
     * Get the conversation this message belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the sender (user, provider, or admin) of this message
     * This handles polymorphic sender types based on sender_type field
     */
    public function sender()
    {
        // Based on sender_type, return the appropriate relationship
        switch ($this->sender_type) {
            case 'provider':
                return $this->belongsTo(ServiceProvider::class, 'sender_id');
            case 'client':
            case 'admin':
            default:
                return $this->belongsTo(User::class, 'sender_id');
        }
    }

    /**
     * Get sender name regardless of type
     */
    public function getSenderNameAttribute(): string
    {
        if ($this->sender_type === 'provider') {
            $provider = ServiceProvider::find($this->sender_id);
            return $provider ? $provider->business_name : 'Unknown Provider';
        } else {
            $user = User::find($this->sender_id);
            return $user ? $user->name : 'Unknown User';
        }
    }

    /**
     * Get sender avatar regardless of type
     */
    public function getSenderAvatarAttribute(): ?string
    {
        if ($this->sender_type === 'provider') {
            $provider = ServiceProvider::find($this->sender_id);
            return $provider ? $provider->logo_url : null;
        } else {
            $user = User::find($this->sender_id);
            return $user ? $user->avatar_url : null;
        }
    }

    /**
     * Get the full image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        // If using local storage
        if (config('filesystems.default') === 'local') {
            return url('storage/' . $this->image_path);
        }

        // If using S3 or other cloud storage
        return \Storage::url($this->image_path);
    }

    /**
     * Scope to get unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get messages for a conversation
     */
    public function scopeForConversation($query, int $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}
