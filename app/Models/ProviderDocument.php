<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderDocument extends Model
{
    protected $fillable = [
        'provider_id',
        'document_type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'verification_status',
        'rejection_reason',
        'verified_at'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'verified_at' => 'datetime',
    ];

    // Relationships
    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('verification_status', 'approved');
    }
}