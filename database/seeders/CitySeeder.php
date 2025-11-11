<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run()
    {
        $cities = [
            [
                'name_ar' => 'الرياض',
                'name_en' => 'Riyadh',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
                'is_active' => true
            ],
            [
                'name_ar' => 'جدة',
                'name_en' => 'Jeddah',
                'latitude' => 21.4858,
                'longitude' => 39.1925,
                'is_active' => true
            ],
            [
                'name_ar' => 'الدمام',
                'name_en' => 'Dammam',
                'latitude' => 26.4282,
                'longitude' => 50.0982,
                'is_active' => true
            ],
            [
                'name_ar' => 'مكة المكرمة',
                'name_en' => 'Mecca',
                'latitude' => 21.3891,
                'longitude' => 39.8579,
                'is_active' => true
            ],
            [
                'name_ar' => 'المدينة المنورة',
                'name_en' => 'Medina',
                'latitude' => 24.5247,
                'longitude' => 39.5692,
                'is_active' => true
            ],
            [
                'name_ar' => 'الطائف',
                'name_en' => 'Taif',
                'latitude' => 21.2703,
                'longitude' => 40.4158,
                'is_active' => true
            ],
            [
                'name_ar' => 'بريدة',
                'name_en' => 'Buraidah',
                'latitude' => 26.3260,
                'longitude' => 43.9750,
                'is_active' => true
            ],
            [
                'name_ar' => 'خميس مشيط',
                'name_en' => 'Khamis Mushait',
                'latitude' => 18.3031,
                'longitude' => 42.7281,
                'is_active' => true
            ],
            [
                'name_ar' => 'حائل',
                'name_en' => 'Hail',
                'latitude' => 27.5114,
                'longitude' => 41.6900,
                'is_active' => true
            ],
            [
                'name_ar' => 'الأحساء',
                'name_en' => 'Al Ahsa',
                'latitude' => 25.4295,
                'longitude' => 49.6200,
                'is_active' => true
            ],
            [
                'name_ar' => 'نجران',
                'name_en' => 'Najran',
                'latitude' => 17.4924,
                'longitude' => 44.1270,
                'is_active' => true
            ],
            [
                'name_ar' => 'جازان',
                'name_en' => 'Jazan',
                'latitude' => 16.9000,
                'longitude' => 42.5500,
                'is_active' => true
            ],
            [
                'name_ar' => 'تبوك',
                'name_en' => 'Tabuk',
                'latitude' => 28.3838,
                'longitude' => 36.5550,
                'is_active' => true
            ],
            [
                'name_ar' => 'القصيم',
                'name_en' => 'Al Qassim',
                'latitude' => 26.3000,
                'longitude' => 43.9700,
                'is_active' => true
            ],
            [
                'name_ar' => 'عسير',
                'name_en' => 'Asir',
                'latitude' => 18.2164,
                'longitude' => 42.5053,
                'is_active' => true
            ]
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
