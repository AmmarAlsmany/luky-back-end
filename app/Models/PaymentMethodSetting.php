<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethodSetting extends Model
{
    protected $fillable = [
        'method_id',
        'method_code',
        'method_name_en',
        'method_name_ar',
        'is_enabled',
        'is_default',
        'display_order',
        'image_url',
        'service_charge',
        'currency_iso',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_default' => 'boolean',
        'service_charge' => 'decimal:2',
    ];
}
