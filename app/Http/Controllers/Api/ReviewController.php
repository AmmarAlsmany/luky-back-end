<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Booking;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    /**
     * Submit a review for a completed booking (Client only)
     */
    public function submitReview(Request $request, int $bookingId): JsonResponse
    {
        $user = $request->user();

        // Validate input
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Get the booking
        $booking = Booking::where('id', $bookingId)
            ->where('client_id', $user->id)
            ->firstOrFail();

        // Validate booking is completed
        if ($booking->status !== 'completed') {
            throw ValidationException::withMessages([
                'booking' => ['You can only review completed bookings.']
            ]);
        }

        // Check if booking was completed at least 1 hour ago (as per contract requirement)
        if ($booking->completed_at && $booking->completed_at->gt(now()->subHour())) {
            throw ValidationException::withMessages([
                'booking' => ['You can review this booking 1 hour after completion.']
            ]);
        }

        // Check if review already exists
        if ($booking->review()->exists()) {
            throw ValidationException::withMessages([
                'booking' => ['You have already reviewed this booking.']
            ]);
        }

        DB::beginTransaction();
        try {
            // Create review
            $review = Review::create([
                'booking_id' => $booking->id,
                'client_id' => $user->id,
                'provider_id' => $booking->provider_id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'is_visible' => true,
            ]);

            // Update provider average rating and total reviews
            $this->updateProviderRating($booking->provider_id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully',
                'data' => [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get reviews for a specific provider (Public)
     */
    public function getProviderReviews(Request $request, int $providerId): JsonResponse
    {
        $provider = ServiceProvider::findOrFail($providerId);

        $query = Review::with(['client:id,name', 'booking:id,booking_number,booking_date'])
            ->where('provider_id', $providerId)
            ->where('is_visible', true);

        // Filter by rating if provided
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Sort by newest first
        $reviews = $query->orderByDesc('created_at')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'provider' => [
                    'id' => $provider->id,
                    'business_name' => $provider->business_name,
                    'average_rating' => (float) $provider->average_rating,
                    'total_reviews' => $provider->total_reviews,
                ],
                'reviews' => $reviews->items()->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'client_name' => $review->client->name,
                        'booking_number' => $review->booking->booking_number,
                        'booking_date' => $review->booking->booking_date->format('Y-m-d'),
                        'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'total' => $reviews->total(),
                    'per_page' => $reviews->perPage(),
                ]
            ]
        ]);
    }

    /**
     * Get client's own submitted reviews
     */
    public function getMyReviews(Request $request): JsonResponse
    {
        $user = $request->user();

        $reviews = Review::with(['provider:id,business_name', 'booking:id,booking_number,booking_date'])
            ->where('client_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $reviews->items()->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'provider_name' => $review->provider->business_name,
                    'booking_number' => $review->booking->booking_number,
                    'booking_date' => $review->booking->booking_date->format('Y-m-d'),
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
            ]
        ]);
    }

    /**
     * Get reviews received by provider (Provider only)
     */
    public function getReceivedReviews(Request $request): JsonResponse
    {
        $user = $request->user();
        $provider = $user->providerProfile;

        if (!$provider) {
            return response()->json([
                'success' => false,
                'message' => 'Provider profile not found'
            ], 404);
        }

        $query = Review::with(['client:id,name', 'booking:id,booking_number,booking_date'])
            ->where('provider_id', $provider->id);

        // Filter by rating if provided
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by visibility
        if ($request->has('is_visible')) {
            $query->where('is_visible', $request->boolean('is_visible'));
        }

        $reviews = $query->orderByDesc('created_at')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'average_rating' => (float) $provider->average_rating,
                    'total_reviews' => $provider->total_reviews,
                    'rating_breakdown' => $this->getRatingBreakdown($provider->id),
                ],
                'reviews' => $reviews->items()->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'client_name' => $review->client->name,
                        'booking_number' => $review->booking->booking_number,
                        'booking_date' => $review->booking->booking_date->format('Y-m-d'),
                        'is_visible' => $review->is_visible,
                        'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'total' => $reviews->total(),
                ]
            ]
        ]);
    }

    /**
     * Update provider's average rating and total reviews count
     */
    protected function updateProviderRating(int $providerId): void
    {
        $stats = Review::where('provider_id', $providerId)
            ->where('is_visible', true)
            ->selectRaw('AVG(rating) as average, COUNT(*) as total')
            ->first();

        ServiceProvider::where('id', $providerId)->update([
            'average_rating' => $stats->average ? round($stats->average, 2) : 0,
            'total_reviews' => $stats->total ?? 0,
        ]);
    }

    /**
     * Get rating breakdown (how many 1-star, 2-star, etc.)
     */
    protected function getRatingBreakdown(int $providerId): array
    {
        $breakdown = Review::where('provider_id', $providerId)
            ->where('is_visible', true)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        // Ensure all ratings 1-5 are present
        $result = [];
        for ($i = 1; $i <= 5; $i++) {
            $result[$i] = $breakdown[$i] ?? 0;
        }

        return $result;
    }
}
