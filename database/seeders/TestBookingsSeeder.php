<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\User;
use App\Models\ServiceProvider;
use App\Models\Service;
use Carbon\Carbon;

class TestBookingsSeeder extends Seeder
{
    public function run()
    {
        // Clear existing test bookings
        $this->command->info('Clearing existing bookings...');
        Booking::truncate();
        BookingItem::truncate();
        
        // Get test data
        $clients = User::where('user_type', 'client')->limit(3)->get();
        $providers = ServiceProvider::where('verification_status', 'approved')->limit(3)->get();
        $services = Service::where('is_active', true)->limit(5)->get();

        if ($clients->isEmpty() || $providers->isEmpty() || $services->isEmpty()) {
            $this->command->warn('Not enough test data. Please run other seeders first.');
            return;
        }

        $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        // Create 20 test bookings
        for ($i = 1; $i <= 20; $i++) {
            $client = $clients->random();
            $provider = $providers->random();
            $status = $statuses[array_rand($statuses)];
            $paymentStatus = $status === 'completed' ? 'paid' : $paymentStatuses[array_rand($paymentStatuses)];

            // Random date in the last 30 days or next 30 days
            $daysOffset = rand(-30, 30);
            $bookingDate = Carbon::now()->addDays($daysOffset);
            $startTime = $bookingDate->copy()->setTime(rand(8, 18), [0, 30][rand(0, 1)]);
            $endTime = $startTime->copy()->addHours(rand(1, 3));

            // Calculate amounts
            $subtotal = rand(100, 1000);
            $taxRate = 0.15; // 15% tax
            $taxAmount = $subtotal * $taxRate;
            $discountAmount = rand(0, 1) ? rand(10, 50) : 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;
            $commissionRate = 0.10; // 10% commission
            $commissionAmount = $subtotal * $commissionRate;

            $booking = Booking::create([
                'booking_number' => 'BK' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'client_id' => $client->id,
                'provider_id' => $provider->id,
                'booking_date' => $bookingDate->format('Y-m-d'),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => ['card', 'cash', 'wallet'][rand(0, 2)],
                'payment_reference' => $paymentStatus === 'paid' ? 'PAY' . rand(100000, 999999) : null,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'commission_amount' => $commissionAmount,
                'client_address' => $client->address ?? 'Test Address, Riyadh',
                'client_latitude' => $client->latitude ?? 24.7136,
                'client_longitude' => $client->longitude ?? 46.6753,
                'notes' => rand(0, 1) ? 'Please arrive on time' : null,
                'confirmed_at' => in_array($status, ['confirmed', 'completed']) ? $bookingDate->copy()->subDays(1) : null,
                'completed_at' => $status === 'completed' ? $bookingDate->copy()->addHours(2) : null,
                'cancelled_at' => $status === 'cancelled' ? $bookingDate->copy()->subHours(2) : null,
                'cancelled_by' => $status === 'cancelled' ? ['client', 'provider', 'admin'][rand(0, 2)] : null,
                'cancellation_reason' => $status === 'cancelled' ? 'Schedule conflict' : null,
                'created_at' => $bookingDate->copy()->subDays(rand(1, 5)),
            ]);

            // Add 1-3 booking items (services)
            $itemCount = rand(1, 3);
            for ($j = 0; $j < $itemCount; $j++) {
                $service = $services->random();
                $quantity = rand(1, 2);
                $unitPrice = rand(50, 300);
                $totalPrice = $quantity * $unitPrice;

                BookingItem::create([
                    'booking_id' => $booking->id,
                    'service_id' => $service->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'service_location' => ['salon', 'home'][rand(0, 1)],
                ]);
            }
        }

        $this->command->info('Created 20 test bookings successfully!');
    }
}
