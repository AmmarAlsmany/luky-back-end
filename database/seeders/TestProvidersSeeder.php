<?php

// database/seeders/TestProvidersSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ServiceProvider;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestProvidersSeeder extends Seeder
{
    public function run()
    {
        // Get service categories
        $categories = ServiceCategory::all();
        
        if ($categories->isEmpty()) {
            $this->command->error('Please run ServiceCategorySeeder first!');
            return;
        }

        // Create Test Providers
        $providers = [
            // Provider 1: Salon
            [
                'user' => [
                    'name' => 'نورة أحمد',
                    'phone' => '+966501111111',
                    'email' => 'noura.salon@test.com',
                    'user_type' => 'provider',
                    'city_id' => 1, // Riyadh
                    'date_of_birth' => '1985-05-15',
                    'gender' => 'female',
                ],
                'provider' => [
                    'business_name' => 'صالون نورة للتجميل',
                    'business_type' => 'salon',
                    'description' => 'صالون متخصص في العناية بالشعر والبشرة باستخدام منتجات عضوية أصلية. خبرة أكثر من 10 سنوات في مجال التجميل.',
                    'address' => 'حي العليا، شارع التحلية، الرياض',
                    'latitude' => 24.7136,
                    'longitude' => 46.6753,
                    'verification_status' => 'approved',
                    'is_active' => true,
                    'is_featured' => true,
                    'max_concurrent_bookings' => 5,
                    'commission_rate' => 15.00,
                    'average_rating' => 4.8,
                    'total_reviews' => 127,
                    'verified_at' => now(),
                    'working_hours' => [
                        'saturday' => ['start' => '09:00', 'end' => '21:00'],
                        'sunday' => ['start' => '09:00', 'end' => '21:00'],
                        'monday' => ['start' => '09:00', 'end' => '21:00'],
                        'tuesday' => ['start' => '09:00', 'end' => '21:00'],
                        'wednesday' => ['start' => '09:00', 'end' => '21:00'],
                        'thursday' => ['start' => '09:00', 'end' => '21:00'],
                    ],
                    'off_days' => ['friday'],
                ],
                'services' => [
                    [
                        'category_id' => 1, // Salons category
                        'name' => 'قص شعر نسائي',
                        'description' => 'قص شعر احترافي حسب أحدث صيحات الموضة العالمية',
                        'price' => 150.00,
                        'available_at_home' => true,
                        'home_service_price' => 200.00,
                        'duration_minutes' => 60,
                    ],
                    [
                        'category_id' => 1,
                        'name' => 'صبغة شعر كاملة',
                        'description' => 'صبغة شعر كاملة بألوان عصرية ومنتجات أصلية',
                        'price' => 300.00,
                        'available_at_home' => false,
                        'home_service_price' => null,
                        'duration_minutes' => 120,
                    ],
                    [
                        'category_id' => 1,
                        'name' => 'معالجة الشعر بالبروتين',
                        'description' => 'علاج مكثف للشعر التالف بالبروتين البرازيلي الأصلي',
                        'price' => 500.00,
                        'available_at_home' => false,
                        'home_service_price' => null,
                        'duration_minutes' => 180,
                    ],
                    [
                        'category_id' => 1,
                        'name' => 'فرد الشعر بالكيراتين',
                        'description' => 'فرد شعر طويل الأمد بالكيراتين الأصلي',
                        'price' => 600.00,
                        'available_at_home' => false,
                        'home_service_price' => null,
                        'duration_minutes' => 240,
                    ],
                    [
                        'category_id' => 1,
                        'name' => 'تسريحة مناسبات',
                        'description' => 'تسريحة شعر فاخرة للمناسبات والأعراس',
                        'price' => 250.00,
                        'available_at_home' => true,
                        'home_service_price' => 350.00,
                        'duration_minutes' => 90,
                    ],
                ],
            ],

            // Provider 2: Makeup Artist
            [
                'user' => [
                    'name' => 'سارة محمد',
                    'phone' => '+966502222222',
                    'email' => 'sarah.makeup@test.com',
                    'user_type' => 'provider',
                    'city_id' => 1,
                    'date_of_birth' => '1990-08-20',
                    'gender' => 'female',
                ],
                'provider' => [
                    'business_name' => 'سارة للمكياج',
                    'business_type' => 'makeup_artist',
                    'description' => 'فنانة مكياج معتمدة متخصصة في مكياج العرائس والمناسبات الخاصة',
                    'address' => 'حي النسيم، الرياض',
                    'latitude' => 24.6748,
                    'longitude' => 46.7345,
                    'verification_status' => 'approved',
                    'is_active' => true,
                    'is_featured' => true,
                    'max_concurrent_bookings' => 1,
                    'commission_rate' => 15.00,
                    'average_rating' => 4.9,
                    'total_reviews' => 89,
                    'verified_at' => now(),
                    'working_hours' => [
                        'saturday' => ['start' => '10:00', 'end' => '20:00'],
                        'sunday' => ['start' => '10:00', 'end' => '20:00'],
                        'monday' => ['start' => '10:00', 'end' => '20:00'],
                        'tuesday' => ['start' => '10:00', 'end' => '20:00'],
                        'wednesday' => ['start' => '10:00', 'end' => '20:00'],
                        'thursday' => ['start' => '10:00', 'end' => '20:00'],
                    ],
                    'off_days' => ['friday'],
                ],
                'services' => [
                    [
                        'category_id' => 3, // Makeup Artists
                        'name' => 'مكياج عروس',
                        'description' => 'مكياج عروس فاخر بأحدث التقنيات ومنتجات عالمية',
                        'price' => 800.00,
                        'available_at_home' => true,
                        'home_service_price' => 1000.00,
                        'duration_minutes' => 120,
                    ],
                    [
                        'category_id' => 3,
                        'name' => 'مكياج سهرة',
                        'description' => 'مكياج سهرة راقي للمناسبات الخاصة',
                        'price' => 400.00,
                        'available_at_home' => true,
                        'home_service_price' => 550.00,
                        'duration_minutes' => 60,
                    ],
                    [
                        'category_id' => 3,
                        'name' => 'مكياج ناعم يومي',
                        'description' => 'مكياج طبيعي وناعم للاستخدام اليومي',
                        'price' => 200.00,
                        'available_at_home' => true,
                        'home_service_price' => 280.00,
                        'duration_minutes' => 45,
                    ],
                ],
            ],

            // Provider 3: Beauty Clinic
            [
                'user' => [
                    'name' => 'د. لمى العتيبي',
                    'phone' => '+966503333333',
                    'email' => 'dr.lama@test.com',
                    'user_type' => 'provider',
                    'city_id' => 2, // Jeddah
                    'date_of_birth' => '1982-03-10',
                    'gender' => 'female',
                ],
                'provider' => [
                    'business_name' => 'عيادة د. لمى للتجميل',
                    'business_type' => 'clinic',
                    'description' => 'عيادة تجميل متخصصة في العناية بالبشرة والليزر وإزالة الشعر',
                    'address' => 'حي الروضة، جدة',
                    'latitude' => 21.5810,
                    'longitude' => 39.1653,
                    'verification_status' => 'approved',
                    'is_active' => true,
                    'is_featured' => false,
                    'max_concurrent_bookings' => 3,
                    'commission_rate' => 15.00,
                    'average_rating' => 4.7,
                    'total_reviews' => 156,
                    'verified_at' => now(),
                    'working_hours' => [
                        'saturday' => ['start' => '09:00', 'end' => '17:00'],
                        'sunday' => ['start' => '09:00', 'end' => '17:00'],
                        'monday' => ['start' => '09:00', 'end' => '17:00'],
                        'tuesday' => ['start' => '09:00', 'end' => '17:00'],
                        'wednesday' => ['start' => '09:00', 'end' => '17:00'],
                        'thursday' => ['start' => '09:00', 'end' => '17:00'],
                    ],
                    'off_days' => ['friday'],
                ],
                'services' => [
                    [
                        'category_id' => 2, // Clinics
                        'name' => 'إزالة شعر بالليزر - وجه كامل',
                        'description' => 'جلسة إزالة شعر بالليزر للوجه الكامل بأحدث أجهزة الليزر',
                        'price' => 300.00,
                        'available_at_home' => false,
                        'home_service_price' => null,
                        'duration_minutes' => 30,
                    ],
                    [
                        'category_id' => 2,
                        'name' => 'تنظيف بشرة عميق',
                        'description' => 'تنظيف عميق للبشرة مع بخار وماسك مغذي',
                        'price' => 250.00,
                        'available_at_home' => false,
                        'home_service_price' => null,
                        'duration_minutes' => 60,
                    ],
                    [
                        'category_id' => 2,
                        'name' => 'حقن فيلر للشفايف',
                        'description' => 'حقن فيلر طبيعي للشفايف بمنتجات أصلية معتمدة',
                        'price' => 1200.00,
                        'available_at_home' => false,
                        'home_service_price' => null,
                        'duration_minutes' => 45,
                    ],
                    [
                        'category_id' => 2,
                        'name' => 'حقن بوتكس',
                        'description' => 'حقن بوتكس للوجه بمنتجات أمريكية أصلية',
                        'price' => 1500.00,
                        'available_at_home' => false,
                        'home_service_price' => null,
                        'duration_minutes' => 30,
                    ],
                ],
            ],

            // Provider 4: Hair Stylist
            [
                'user' => [
                    'name' => 'ريم الغامدي',
                    'phone' => '+966504444444',
                    'email' => 'reem.hair@test.com',
                    'user_type' => 'provider',
                    'city_id' => 1,
                    'date_of_birth' => '1993-11-25',
                    'gender' => 'female',
                ],
                'provider' => [
                    'business_name' => 'ريم لتصفيف الشعر',
                    'business_type' => 'hair_stylist',
                    'description' => 'مصففة شعر محترفة متخصصة في التسريحات العصرية والكلاسيكية',
                    'address' => 'حي الملقا، الرياض',
                    'latitude' => 24.7734,
                    'longitude' => 46.6478,
                    'verification_status' => 'approved',
                    'is_active' => true,
                    'is_featured' => false,
                    'max_concurrent_bookings' => 1,
                    'commission_rate' => 15.00,
                    'average_rating' => 4.6,
                    'total_reviews' => 67,
                    'verified_at' => now(),
                    'working_hours' => [
                        'saturday' => ['start' => '11:00', 'end' => '22:00'],
                        'sunday' => ['start' => '11:00', 'end' => '22:00'],
                        'monday' => ['start' => '11:00', 'end' => '22:00'],
                        'tuesday' => ['start' => '11:00', 'end' => '22:00'],
                        'wednesday' => ['start' => '11:00', 'end' => '22:00'],
                        'thursday' => ['start' => '11:00', 'end' => '22:00'],
                    ],
                    'off_days' => ['friday'],
                ],
                'services' => [
                    [
                        'category_id' => 4, // Hair Stylists
                        'name' => 'تسريحة عروس',
                        'description' => 'تسريحة عروس فاخرة مع اكسسوارات',
                        'price' => 500.00,
                        'available_at_home' => true,
                        'home_service_price' => 700.00,
                        'duration_minutes' => 120,
                    ],
                    [
                        'category_id' => 4,
                        'name' => 'تسريحة سهرة بسيطة',
                        'description' => 'تسريحة أنيقة للسهرات والحفلات',
                        'price' => 200.00,
                        'available_at_home' => true,
                        'home_service_price' => 300.00,
                        'duration_minutes' => 60,
                    ],
                    [
                        'category_id' => 4,
                        'name' => 'تسريحة ضفائر',
                        'description' => 'تسريحة ضفائر عصرية ومميزة',
                        'price' => 150.00,
                        'available_at_home' => true,
                        'home_service_price' => 220.00,
                        'duration_minutes' => 45,
                    ],
                ],
            ],
        ];

        // Create providers and services
        foreach ($providers as $providerData) {
            // Create user
            $user = User::create([
                ...$providerData['user'],
                'password' => Hash::make('password'),
                'phone_verified_at' => now(),
                'is_active' => true,
            ]);

            // Assign provider role
            $user->assignRole('provider');

            // Create provider profile
            $provider = ServiceProvider::create([
                ...$providerData['provider'],
                'user_id' => $user->id,
                'city_id' => $providerData['user']['city_id'], // Add city_id from user data
            ]);

            // Create services
            foreach ($providerData['services'] as $serviceData) {
                Service::create([
                    ...$serviceData,
                    'provider_id' => $provider->id,
                    'is_active' => true,
                ]);
            }

            $this->command->info("Created provider: {$provider->business_name} with " . count($providerData['services']) . " services");
        }

        $this->command->info('✅ Test providers and services created successfully!');
    }
}