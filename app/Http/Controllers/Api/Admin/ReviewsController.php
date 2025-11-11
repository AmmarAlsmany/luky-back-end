<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ReviewsController extends Controller
{
    /**
     * Get list of reviews with pagination and filters
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');
        $rating = $request->input('rating');
        $status = $request->input('status');
        $reviewType = $request->input('review_type'); // provider or service
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Review::with(['user', 'provider', 'booking']);

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('provider', function ($pq) use ($search) {
                      $pq->where('business_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Rating filter
        if ($rating) {
            $query->where('rating', $rating);
        }

        // Status filter
        if ($status) {
            if ($status === 'flagged') {
                $query->where('is_flagged', true);
            } elseif ($status === 'responded') {
                $query->whereNotNull('admin_response');
            } elseif ($status === 'hidden') {
                $query->where('is_visible', false);
            }
        }

        // Date range filter
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Sorting
        $query->orderBy($sortBy, $sortOrder);

        $reviews = $query->paginate($perPage);

        // Get stats
        $stats = [
            'total_reviews' => Review::count(),
            'average_rating' => round(Review::avg('rating'), 2),
            'flagged_reviews' => Review::where('is_flagged', true)->count(),
            'hidden_reviews' => Review::where('is_visible', false)->count(),
            'reviews_with_response' => Review::whereNotNull('admin_response')->count(),
            'rating_breakdown' => [
                '5_stars' => Review::where('rating', 5)->count(),
                '4_stars' => Review::where('rating', 4)->count(),
                '3_stars' => Review::where('rating', 3)->count(),
                '2_stars' => Review::where('rating', 2)->count(),
                '1_star' => Review::where('rating', 1)->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'reviews' => $reviews,
                'stats' => $stats,
            ],
        ]);
    }

    /**
     * Get single review
     */
    public function show($id)
    {
        $review = Review::with(['user', 'provider', 'booking', 'service'])->find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['review' => $review],
        ]);
    }

    /**
     * Flag review as inappropriate
     */
    public function flag(Request $request, $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $review->update([
            'is_flagged' => true,
            'flag_reason' => $request->reason,
            'flagged_by' => auth()->id(),
            'flagged_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => ['review' => $review],
            'message' => 'Review flagged successfully',
        ]);
    }

    /**
     * Unflag review
     */
    public function unflag($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found',
            ], 404);
        }

        $review->update([
            'is_flagged' => false,
            'flag_reason' => null,
            'flagged_by' => null,
            'flagged_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'data' => ['review' => $review],
            'message' => 'Review unflagged successfully',
        ]);
    }

    /**
     * Hide/Show review
     */
    public function toggleVisibility($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found',
            ], 404);
        }

        $review->update([
            'is_visible' => !$review->is_visible,
        ]);

        return response()->json([
            'success' => true,
            'data' => ['review' => $review],
            'message' => $review->is_visible ? 'Review shown successfully' : 'Review hidden successfully',
        ]);
    }

    /**
     * Add admin response to review
     */
    public function respond(Request $request, $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'response' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $review->update([
            'admin_response' => $request->response,
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => ['review' => $review],
            'message' => 'Response added successfully',
        ]);
    }

    /**
     * Delete admin response
     */
    public function deleteResponse($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found',
            ], 404);
        }

        $review->update([
            'admin_response' => null,
            'responded_by' => null,
            'responded_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'data' => ['review' => $review],
            'message' => 'Response deleted successfully',
        ]);
    }

    /**
     * Delete review
     */
    public function destroy($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found',
            ], 404);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
        ]);
    }

    /**
     * Get flagged reviews
     */
    public function flagged()
    {
        $reviews = Review::with(['user', 'provider', 'booking'])
            ->where('is_flagged', true)
            ->orderBy('flagged_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => ['reviews' => $reviews],
        ]);
    }

    /**
     * Get reviews by provider
     */
    public function byProvider($providerId)
    {
        $reviews = Review::with(['user', 'booking'])
            ->where('provider_id', $providerId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_reviews' => Review::where('provider_id', $providerId)->count(),
            'average_rating' => round(Review::where('provider_id', $providerId)->avg('rating'), 2),
            'rating_breakdown' => [
                '5_stars' => Review::where('provider_id', $providerId)->where('rating', 5)->count(),
                '4_stars' => Review::where('provider_id', $providerId)->where('rating', 4)->count(),
                '3_stars' => Review::where('provider_id', $providerId)->where('rating', 3)->count(),
                '2_stars' => Review::where('provider_id', $providerId)->where('rating', 2)->count(),
                '1_star' => Review::where('provider_id', $providerId)->where('rating', 1)->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'reviews' => $reviews,
                'stats' => $stats,
            ],
        ]);
    }

    /**
     * Get reviews by user
     */
    public function byUser($userId)
    {
        $reviews = Review::with(['provider', 'booking', 'service'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => ['reviews' => $reviews],
        ]);
    }

    /**
     * Get review statistics
     */
    public function stats()
    {
        $stats = [
            'total_reviews' => Review::count(),
            'average_rating' => round(Review::avg('rating'), 2),
            'flagged_reviews' => Review::where('is_flagged', true)->count(),
            'hidden_reviews' => Review::where('is_visible', false)->count(),
            'reviews_with_response' => Review::whereNotNull('admin_response')->count(),
            'reviews_today' => Review::whereDate('created_at', today())->count(),
            'reviews_this_week' => Review::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'reviews_this_month' => Review::whereMonth('created_at', now()->month)->count(),
            'rating_breakdown' => [
                '5_stars' => Review::where('rating', 5)->count(),
                '4_stars' => Review::where('rating', 4)->count(),
                '3_stars' => Review::where('rating', 3)->count(),
                '2_stars' => Review::where('rating', 2)->count(),
                '1_star' => Review::where('rating', 1)->count(),
            ],
            'top_rated_providers' => DB::table('reviews')
                ->join('service_providers', 'reviews.provider_id', '=', 'service_providers.id')
                ->select(
                    'service_providers.id',
                    'service_providers.business_name',
                    DB::raw('AVG(reviews.rating) as avg_rating'),
                    DB::raw('COUNT(reviews.id) as review_count')
                )
                ->groupBy('service_providers.id', 'service_providers.business_name')
                ->having('review_count', '>=', 5)
                ->orderByDesc('avg_rating')
                ->limit(10)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Bulk actions on reviews
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:flag,unflag,hide,show,delete',
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $affected = 0;

        switch ($request->action) {
            case 'flag':
                $affected = Review::whereIn('id', $request->review_ids)
                    ->update(['is_flagged' => true, 'flagged_by' => auth()->id(), 'flagged_at' => now()]);
                break;
            case 'unflag':
                $affected = Review::whereIn('id', $request->review_ids)
                    ->update(['is_flagged' => false, 'flag_reason' => null, 'flagged_by' => null, 'flagged_at' => null]);
                break;
            case 'hide':
                $affected = Review::whereIn('id', $request->review_ids)
                    ->update(['is_visible' => false]);
                break;
            case 'show':
                $affected = Review::whereIn('id', $request->review_ids)
                    ->update(['is_visible' => true]);
                break;
            case 'delete':
                $affected = Review::whereIn('id', $request->review_ids)->delete();
                break;
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk action completed. {$affected} reviews affected.",
        ]);
    }
}
