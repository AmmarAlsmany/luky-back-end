<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketMessage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'sender_id',
        'sender_type',
        'message',
        'attachments',
        'is_internal_note',
        'created_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal_note' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($message) {
            $message->created_at = now();
        });
    }

    /**
     * Get the ticket this message belongs to
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Get the sender of this message
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
