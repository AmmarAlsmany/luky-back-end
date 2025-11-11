<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'provider_id',
        'category_id',
        'name',
        'name_ar',
        'name_en',
        'description',
        'description_ar',
        'description_en',
        'price',
        'available_at_home',
        'home_service_price',
        'duration_minutes',
        'is_active',
        'is_featured',
        'average_rating',
        'total_bookings',
        'image_url',
        'gallery',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'home_service_price' => 'decimal:2',
        'available_at_home' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'duration_minutes' => 'integer',
        'average_rating' => 'decimal:2',
        'total_bookings' => 'integer',
        'gallery' => 'array',  // Cast JSON to array
    ];

    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function getPriceForLocation(string $location): float
    {
        if ($location === 'home' && $this->available_at_home) {
            return (float) $this->home_service_price;
        }
        return (float) $this->price;
    }
}
