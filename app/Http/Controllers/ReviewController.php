<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Display reviews list with filters
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $city = $request->input('city');
        $minRating = $request->input('min_rating', 0);

        // Get providers with reviews
        $query = User::where('user_type', 'provider')
            ->with(['city', 'providerProfile'])
            ->withCount('receivedReviews as reviews_count')
            ->withAvg('receivedReviews as avg_rating', 'rating')
            ->has('receivedReviews');

        // Apply filters
        if ($search) {
            $query->where('name', 'ilike', "%{$search}%");
        }

        if ($city) {
            $query->where('city_id', $city);
        }

        if ($minRating > 0) {
            $query->havingRaw('AVG("reviews"."rating") >= ?', [$minRating]);
        }

        $providers = $query->orderByDesc('avg_rating')
            ->paginate(20);

        // Get latest review date for each provider
        foreach ($providers as $provider) {
            $latestReview = $provider->receivedReviews()
                ->orderByDesc('created_at')
                ->first();
            $provider->latest_review = $latestReview;
        }

        // Statistics
        $stats = [
            'total_reviews' => Review::count(),
            'avg_rating' => round(Review::avg('rating') ?? 0, 1),
            'flagged_reviews' => Review::where('is_flagged', true)->count(),
            'providers_with_reviews' => User::where('user_type', 'provider')->has('receivedReviews')->count(),
        ];

        // Get cities for filter
        $cities = DB::table('cities')->orderBy('name_en')->get();

        $filters = [
            'search' => $search,
            'city' => $city,
            'min_rating' => $minRating,
        ];

        return view('reviews.list', compact('providers', 'stats', 'cities', 'filters'));
    }

    /**
     * Display provider reviews details
     */
    public function show($providerId)
    {
        $provider = \App\Models\ServiceProvider::with('user')->findOrFail($providerId);
        
        $reviews = Review::where('provider_id', $providerId)
            ->with(['client', 'booking'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get provider services
        $services = \App\Models\Service::where('provider_id', $providerId)
            ->where('is_active', true)
            ->get();

        $stats = [
            'total_reviews' => $reviews->total(),
            'avg_rating' => round(Review::where('provider_id', $providerId)->avg('rating'), 1),
            'rating_breakdown' => [
                5 => Review::where('provider_id', $providerId)->where('rating', 5)->count(),
                4 => Review::where('provider_id', $providerId)->where('rating', 4)->count(),
                3 => Review::where('provider_id', $providerId)->where('rating', 3)->count(),
                2 => Review::where('provider_id', $providerId)->where('rating', 2)->count(),
                1 => Review::where('provider_id', $providerId)->where('rating', 1)->count(),
            ],
            'flagged' => Review::where('provider_id', $providerId)->where('is_flagged', true)->count(),
        ];

        return view('reviews.details', compact('provider', 'reviews', 'stats'));
    }

    /**
     * Toggle review flagged status
     */
    public function toggleFlag($id)
    {
        $review = Review::findOrFail($id);
        $review->is_flagged = !$review->is_flagged;
        $review->save();

        return response()->json([
            'success' => true,
            'message' => $review->is_flagged ? 'Review flagged successfully' : 'Review unflagged successfully',
            'is_flagged' => $review->is_flagged,
        ]);
    }

    /**
     * Delete review
     */
    public function destroy($id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete review',
            ], 500);
        }
    }

    /**
     * Update review response (admin reply)
     */
    public function updateResponse(Request $request, $id)
    {
        $validated = $request->validate([
            'admin_response' => 'nullable|string|max:1000',
        ]);

        $review = Review::findOrFail($id);
        $review->admin_response = $validated['admin_response'];
        $review->responded_at = now();
        $review->responded_by = auth()->id();
        $review->save();

        return response()->json([
            'success' => true,
            'message' => 'Response saved successfully',
        ]);
    }
}
