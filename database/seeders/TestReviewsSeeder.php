<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Booking;
use App\Models\ServiceProvider;

class TestReviewsSeeder extends Seeder
{
    public function run()
    {
        // Get completed bookings that don't have reviews yet
        $completedBookings = Booking::where('status', 'completed')
            ->whereDoesntHave('review')
            ->with(['client', 'provider'])
            ->limit(15)
            ->get();

        if ($completedBookings->isEmpty()) {
            $this->command->warn('No completed bookings found without reviews.');
            return;
        }

        $comments = [
            5 => [
                'Excellent service! Very professional and friendly.',
                'Amazing experience! Highly recommend.',
                'Perfect! Will definitely book again.',
                'Outstanding service, exceeded expectations!',
                'Absolutely wonderful! Best service ever.',
            ],
            4 => [
                'Very good service, just minor delays.',
                'Great overall, would recommend.',
                'Good experience, professional staff.',
                'Nice service, worth the price.',
            ],
            3 => [
                'Average service, nothing special.',
                'Okay experience, could be better.',
                'Decent service but room for improvement.',
            ],
            2 => [
                'Below expectations, not satisfied.',
                'Service was okay but had issues.',
                'Not great, had some problems.',
            ],
            1 => [
                'Very disappointed with the service.',
                'Poor service, would not recommend.',
                'Terrible experience, not worth it.',
            ],
        ];

        foreach ($completedBookings as $booking) {
            // Random rating (weighted towards higher ratings)
            $rand = rand(1, 100);
            if ($rand <= 50) {
                $rating = 5;
            } elseif ($rand <= 75) {
                $rating = 4;
            } elseif ($rand <= 90) {
                $rating = 3;
            } elseif ($rand <= 97) {
                $rating = 2;
            } else {
                $rating = 1;
            }

            $comment = $comments[$rating][array_rand($comments[$rating])];

            // Random flags (5% chance)
            $isFlagged = rand(1, 100) <= 5;

            $review = Review::create([
                'booking_id' => $booking->id,
                'client_id' => $booking->client_id,
                'provider_id' => $booking->provider_id,
                'rating' => $rating,
                'comment' => $comment,
                'is_visible' => true,
                'is_flagged' => $isFlagged,
                'flag_reason' => $isFlagged ? 'Inappropriate content' : null,
                'flagged_at' => $isFlagged ? now() : null,
                'created_at' => $booking->completed_at ?? now(),
            ]);

            // Add admin response to some reviews (30% chance)
            if (rand(1, 100) <= 30) {
                $review->update([
                    'admin_response' => 'Thank you for your feedback! We appreciate your review.',
                    'responded_at' => now(),
                    'responded_by' => 1, // Admin user ID
                ]);
            }

            // Update provider's average rating
            if ($booking->provider) {
                $avgRating = Review::where('provider_id', $booking->provider_id)->avg('rating');
                $totalReviews = Review::where('provider_id', $booking->provider_id)->count();
                
                $booking->provider->update([
                    'average_rating' => round($avgRating, 2),
                    'total_reviews' => $totalReviews,
                ]);
            }
        }

        $this->command->info('Created ' . $completedBookings->count() . ' test reviews successfully!');
    }
}
