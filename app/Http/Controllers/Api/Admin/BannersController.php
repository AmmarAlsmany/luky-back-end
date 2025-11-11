<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BannersController extends Controller
{
    /**
     * Get list of banners with pagination and filters
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');
        $status = $request->input('status');
        $type = $request->input('type');
        $location = $request->input('location');
        $sortBy = $request->input('sort_by', 'display_order');
        $sortOrder = $request->input('sort_order', 'asc');

        $query = DB::table('banners');

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($status) {
            $query->where('status', $status);
        }

        // Type filter
        if ($type) {
            $query->where('type', $type);
        }

        // Location filter
        if ($location) {
            $query->where('location', $location);
        }

        // Sorting
        $query->orderBy($sortBy, $sortOrder);

        $banners = $query->paginate($perPage);

        // Get stats
        $stats = [
            'total_banners' => DB::table('banners')->count(),
            'active_banners' => DB::table('banners')->where('status', 'active')->count(),
            'scheduled_banners' => DB::table('banners')
                ->where('status', 'scheduled')
                ->count(),
            'total_clicks' => DB::table('banner_clicks')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'banners' => $banners,
                'stats' => $stats,
            ],
        ]);
    }

    /**
     * Get single banner
     */
    public function show($id)
    {
        $banner = DB::table('banners')->find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found',
            ], 404);
        }

        // Get click statistics
        $clickStats = DB::table('banner_clicks')
            ->where('banner_id', $id)
            ->select(
                DB::raw('COUNT(*) as total_clicks'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users')
            )
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'banner' => $banner,
                'click_stats' => $clickStats,
            ],
        ]);
    }

    /**
     * Create banner
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'provider_name' => 'nullable|string|max:255',
            'offer_text' => 'required|string|max:255',
            'banner_template' => 'required|string',
            'title_color' => 'required|string',
            'title_font' => 'required|string',
            'title_size' => 'required|string',
            'provider_color' => 'required|string',
            'provider_font' => 'required|string',
            'provider_size' => 'required|string',
            'offer_text_color' => 'required|string',
            'offer_bg_color' => 'required|string',
            'offer_font' => 'required|string',
            'offer_size' => 'required|string',
            'banner_image' => 'required|string', // Base64 encoded image
            'link_url' => 'nullable|url',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'display_location' => 'nullable|in:home,services,providers,all',
            'display_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Decode and save the banner image
        $imagePath = null;
        if ($request->banner_image) {
            try {
                // Extract base64 data
                $imageData = $request->banner_image;
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $type = strtolower($type[1]);

                    $imageData = base64_decode($imageData);

                    if ($imageData === false) {
                        throw new \Exception('Base64 decode failed');
                    }

                    // Generate unique filename
                    $filename = 'banner_' . time() . '_' . uniqid() . '.' . $type;
                    $path = 'banners/' . $filename;

                    // Save to storage
                    Storage::disk('public')->put($path, $imageData);
                    $imagePath = $path;
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process banner image: ' . $e->getMessage(),
                ], 422);
            }
        }

        // Determine status based on dates
        $startDate = new \DateTime($request->start_date);
        $endDate = new \DateTime($request->end_date);
        $now = new \DateTime();

        $status = 'scheduled';
        if ($now >= $startDate && $now <= $endDate) {
            $status = 'active';
        } elseif ($now > $endDate) {
            $status = 'expired';
        }

        $bannerId = DB::table('banners')->insertGetId([
            'title' => $request->title,
            'provider_name' => $request->provider_name,
            'offer_text' => $request->offer_text,
            'banner_template' => $request->banner_template,
            'title_color' => $request->title_color,
            'title_font' => $request->title_font,
            'title_size' => $request->title_size,
            'provider_color' => $request->provider_color,
            'provider_font' => $request->provider_font,
            'provider_size' => $request->provider_size,
            'offer_text_color' => $request->offer_text_color,
            'offer_bg_color' => $request->offer_bg_color,
            'offer_font' => $request->offer_font,
            'offer_size' => $request->offer_size,
            'image_url' => $imagePath,
            'link_url' => $request->link_url,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $status,
            'display_location' => $request->display_location ?? 'home',
            'display_order' => $request->display_order ?? 0,
            'is_active' => true,
            'click_count' => 0,
            'impression_count' => 0,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $banner = DB::table('banners')->find($bannerId);

        return response()->json([
            'success' => true,
            'data' => ['banner' => $banner],
            'message' => 'Banner created successfully',
        ], 201);
    }

    /**
     * Update banner
     */
    public function update(Request $request, $id)
    {
        $banner = DB::table('banners')->find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'sometimes|in:promotional,informational,category,provider',
            'location' => 'sometimes|in:home_top,home_middle,home_bottom,services,providers,booking',
            'target_type' => 'nullable|in:url,service,provider,category',
            'target_id' => 'nullable|integer',
            'target_url' => 'nullable|url',
            'display_order' => 'nullable|integer',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'status' => 'in:active,inactive,scheduled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = [
            'updated_at' => now(),
        ];

        // Update image if provided
        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image_url) {
                Storage::disk('public')->delete($banner->image_url);
            }

            $updateData['image_url'] = $request->file('image')->store('banners', 'public');
        }

        // Add other fields
        if ($request->has('title')) $updateData['title'] = $request->title;
        if ($request->has('description')) $updateData['description'] = $request->description;
        if ($request->has('type')) $updateData['type'] = $request->type;
        if ($request->has('location')) $updateData['location'] = $request->location;
        if ($request->has('target_type')) $updateData['target_type'] = $request->target_type;
        if ($request->has('target_id')) $updateData['target_id'] = $request->target_id;
        if ($request->has('target_url')) $updateData['target_url'] = $request->target_url;
        if ($request->has('display_order')) $updateData['display_order'] = $request->display_order;
        if ($request->has('starts_at')) $updateData['starts_at'] = $request->starts_at;
        if ($request->has('ends_at')) $updateData['ends_at'] = $request->ends_at;
        if ($request->has('status')) $updateData['status'] = $request->status;

        DB::table('banners')->where('id', $id)->update($updateData);

        $updatedBanner = DB::table('banners')->find($id);

        return response()->json([
            'success' => true,
            'data' => ['banner' => $updatedBanner],
            'message' => 'Banner updated successfully',
        ]);
    }

    /**
     * Delete banner
     */
    public function destroy($id)
    {
        $banner = DB::table('banners')->find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found',
            ], 404);
        }

        // Delete image
        if ($banner->image_url) {
            Storage::disk('public')->delete($banner->image_url);
        }

        // Delete banner clicks
        DB::table('banner_clicks')->where('banner_id', $id)->delete();

        // Delete banner
        DB::table('banners')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully',
        ]);
    }

    /**
     * Toggle banner status
     */
    public function toggleStatus($id)
    {
        $banner = DB::table('banners')->find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found',
            ], 404);
        }

        $newStatus = $banner->status === 'active' ? 'inactive' : 'active';

        DB::table('banners')
            ->where('id', $id)
            ->update([
                'status' => $newStatus,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'data' => ['status' => $newStatus],
            'message' => 'Banner status updated successfully',
        ]);
    }

    /**
     * Update banner display order
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'banners' => 'required|array',
            'banners.*.id' => 'required|exists:banners,id',
            'banners.*.display_order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach ($request->banners as $banner) {
            DB::table('banners')
                ->where('id', $banner['id'])
                ->update([
                    'display_order' => $banner['display_order'],
                    'updated_at' => now(),
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Banner order updated successfully',
        ]);
    }

    /**
     * Get banner analytics
     */
    public function analytics($id)
    {
        $banner = DB::table('banners')->find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found',
            ], 404);
        }

        // Get click data by day
        $clicksByDay = DB::table('banner_clicks')
            ->where('banner_id', $id)
            ->select(
                DB::raw('DATE(clicked_at) as date'),
                DB::raw('COUNT(*) as clicks'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        // Get total clicks and unique users
        $totalStats = DB::table('banner_clicks')
            ->where('banner_id', $id)
            ->select(
                DB::raw('COUNT(*) as total_clicks'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users')
            )
            ->first();

        // Calculate CTR (if impressions were tracked, placeholder for now)
        $impressions = 1000; // Placeholder - should be tracked
        $ctr = $impressions > 0 ? ($totalStats->total_clicks / $impressions) * 100 : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'banner' => $banner,
                'total_clicks' => $totalStats->total_clicks,
                'unique_users' => $totalStats->unique_users,
                'click_through_rate' => round($ctr, 2),
                'clicks_by_day' => $clicksByDay,
            ],
        ]);
    }

    /**
     * Get banner statistics
     */
    public function stats()
    {
        $stats = [
            'total_banners' => DB::table('banners')->count(),
            'active_banners' => DB::table('banners')->where('status', 'active')->count(),
            'inactive_banners' => DB::table('banners')->where('status', 'inactive')->count(),
            'scheduled_banners' => DB::table('banners')->where('status', 'scheduled')->count(),
            'total_clicks' => DB::table('banner_clicks')->count(),
            'clicks_today' => DB::table('banner_clicks')
                ->whereDate('clicked_at', today())
                ->count(),
            'most_clicked_banners' => DB::table('banners')
                ->leftJoin('banner_clicks', 'banners.id', '=', 'banner_clicks.banner_id')
                ->select(
                    'banners.id',
                    'banners.title',
                    'banners.location',
                    DB::raw('COUNT(banner_clicks.id) as click_count')
                )
                ->groupBy('banners.id', 'banners.title', 'banners.location')
                ->orderByDesc('click_count')
                ->limit(5)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
