<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PromoCode;
use Carbon\Carbon;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        $promoCodes = [
            [
                'code' => 'WELCOME20',
                'description' => 'Welcome offer - 20% off on your first booking',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'max_discount_amount' => 50,
                'min_booking_amount' => 100,
                'usage_limit' => null, // unlimited
                'usage_limit_per_user' => 1, // once per user
                'valid_from' => Carbon::now()->subDays(7),
                'valid_until' => Carbon::now()->addMonths(3),
                'is_active' => true,
                'applicable_to' => 'clients_only',
                'applicable_services' => null,
                'applicable_categories' => null,
            ],
            [
                'code' => 'SUMMER50',
                'description' => 'Summer special - 50 SAR flat discount',
                'discount_type' => 'fixed',
                'discount_value' => 50,
                'max_discount_amount' => null,
                'min_booking_amount' => 200,
                'usage_limit' => 100, // limited to 100 uses
                'usage_limit_per_user' => 2, // twice per user
                'valid_from' => Carbon::now()->subDays(3),
                'valid_until' => Carbon::now()->addMonths(2),
                'is_active' => true,
                'applicable_to' => 'clients_only',
                'applicable_services' => null,
                'applicable_categories' => null,
            ],
            [
                'code' => 'VIP30',
                'description' => 'VIP discount - 30% off all services',
                'discount_type' => 'percentage',
                'discount_value' => 30,
                'max_discount_amount' => 100,
                'min_booking_amount' => 150,
                'usage_limit' => 50,
                'usage_limit_per_user' => 3,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonth(),
                'is_active' => true,
                'applicable_to' => 'clients_only',
                'applicable_services' => null,
                'applicable_categories' => null,
            ],
        ];

        foreach ($promoCodes as $promoData) {
            PromoCode::create($promoData);
            $this->command->info("Created promo code '{$promoData['code']}'");
        }

        $this->command->info('Promo codes seeding completed!');
    }
}
