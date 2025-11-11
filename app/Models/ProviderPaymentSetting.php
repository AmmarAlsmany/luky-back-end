<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderPaymentSetting extends Model
{
    protected $fillable = [
        'provider_id',
        'tax_rate',
        'commission_rate',
        'currency',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'commission_rate' => 'decimal:2',
    ];

    /**
     * Get the provider that owns the payment settings
     */
    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id');
    }
}
