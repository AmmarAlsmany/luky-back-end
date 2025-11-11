<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'user_id',
        'user_type',
        'last_message_id',
        'last_message_at',
        'admin_unread_count',
        'user_unread_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * Get the admin user
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the user (client or provider)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the last message
     */
    public function lastMessage()
    {
        return $this->belongsTo(AdminMessage::class, 'last_message_id');
    }

    /**
     * Get all messages
     */
    public function messages()
    {
        return $this->hasMany(AdminMessage::class, 'conversation_id');
    }

    /**
     * Get the provider if user_type is provider
     */
    public function provider()
    {
        return $this->hasOne(ServiceProvider::class, 'user_id', 'user_id');
    }
}
