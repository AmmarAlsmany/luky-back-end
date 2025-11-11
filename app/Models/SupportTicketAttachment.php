<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketAttachment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'message_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
        'uploaded_by_type',
        'created_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attachment) {
            $attachment->created_at = now();
        });
    }

    /**
     * Get the ticket this attachment belongs to
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Get the message this attachment belongs to
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(SupportTicketMessage::class, 'message_id');
    }

    /**
     * Get the uploader
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the file URL
     */
    public function getFileUrlAttribute(): string
    {
        return url('storage/' . $this->file_path);
    }
}
