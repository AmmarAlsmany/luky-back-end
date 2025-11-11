<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name_ar' => 'صالونات',
                'name_en' => 'Salons',
                'icon' => 'salon-icon',
                'color' => '#FF6B6B',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'name_ar' => 'عيادات تجميل',
                'name_en' => 'Beauty Clinics',
                'icon' => 'clinic-icon',
                'color' => '#4ECDC4',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'name_ar' => 'ميك أب آرتست',
                'name_en' => 'Makeup Artists',
                'icon' => 'makeup-icon',
                'color' => '#45B7D1',
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'name_ar' => 'مصففات الشعر',
                'name_en' => 'Hair Stylists',
                'icon' => 'hairstyle-icon',
                'color' => '#F9CA24',
                'sort_order' => 4,
                'is_active' => true
            ],
            [
                'name_ar' => 'العروض',
                'name_en' => 'Offers',
                'icon' => 'offers-icon',
                'color' => '#6C5CE7',
                'sort_order' => 5,
                'is_active' => true
            ]
        ];

        foreach ($categories as $category) {
            ServiceCategory::create($category);
        }
    }
}

