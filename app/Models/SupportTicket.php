<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_number',
        'user_id',
        'user_type',
        'category',
        'subject',
        'description',
        'priority',
        'status',
        'assigned_to',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
        });
    }

    /**
     * Generate unique ticket number
     */
    protected static function generateTicketNumber(): string
    {
        do {
            $number = 'T-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('ticket_number', $number)->exists());

        return $number;
    }

    /**
     * Get the user who created this ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the admin assigned to this ticket
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get all messages for this ticket
     */
    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id');
    }

    /**
     * Get all attachments for this ticket
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(SupportTicketAttachment::class, 'ticket_id');
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * Scope to filter by priority
     */
    public function scopeByPriority($query, $priority)
    {
        if ($priority) {
            return $query->where('priority', $priority);
        }
        return $query;
    }

    /**
     * Scope to search tickets
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', '%' . $search . '%')
                    ->orWhere('subject', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }
        return $query;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'open' => 'bg-primary-subtle text-primary',
            'in_progress' => 'bg-info-subtle text-info',
            'waiting_customer' => 'bg-warning-subtle text-warning',
            'resolved' => 'bg-success-subtle text-success',
            'closed' => 'bg-secondary-subtle text-secondary',
            default => 'bg-light text-dark',
        };
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeClass(): string
    {
        return match ($this->priority) {
            'low' => 'bg-info-subtle text-info',
            'medium' => 'bg-primary-subtle text-primary',
            'high' => 'bg-warning-subtle text-warning',
            'urgent' => 'bg-danger-subtle text-danger',
            default => 'bg-light text-dark',
        };
    }

    /**
     * Get category display name
     */
    public function getCategoryDisplayName(): string
    {
        return match ($this->category) {
            'technical' => 'Technical',
            'billing' => 'Billing',
            'booking' => 'Booking',
            'complaint' => 'Complaint',
            'general' => 'General',
            default => ucfirst($this->category),
        };
    }
}
