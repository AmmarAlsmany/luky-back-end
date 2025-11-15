<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'icon',
        'color',
        'image_url',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get business type from category name
     * Maps category to business_type for backward compatibility
     */
    public function getBusinessType()
    {
        $mapping = [
            'salon' => 'salon',
            'clinic' => 'clinic',
            'makeup artist' => 'makeup_artist',
            'hair stylist' => 'hair_stylist',
        ];

        $categoryName = strtolower($this->name_en ?? '');

        foreach ($mapping as $key => $value) {
            if (str_contains($categoryName, $key)) {
                return $value;
            }
        }

        // Default to salon if no match
        return 'salon';
    }
}

