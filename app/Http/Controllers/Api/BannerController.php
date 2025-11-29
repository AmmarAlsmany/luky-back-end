<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    /**
     * Get active banners for mobile app
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveBanners(Request $request)
    {
        $now = now();

        // Get active banners (within date range and is_active = true)
        $banners = DB::table('banners')
            ->where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'provider_name' => $banner->provider_name,
                    'offer_text' => $banner->offer_text,
                    'image_url' => $banner->image_url ? asset('storage/' . $banner->image_url) : null,
                    'link_url' => $banner->link_url,
                    'start_date' => $banner->start_date,
                    'end_date' => $banner->end_date,
                    'display_location' => $banner->display_location,
                    'display_order' => $banner->display_order,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'banners' => $banners,
                'total' => $banners->count(),
            ]
        ]);
    }

    /**
     * Track banner impression (view)
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function trackImpression(Request $request, $id)
    {
        $banner = DB::table('banners')->find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found',
            ], 404);
        }

        // Increment impression count
        DB::table('banners')
            ->where('id', $id)
            ->increment('impression_count');

        return response()->json([
            'success' => true,
            'message' => 'Impression tracked',
        ]);
    }

    /**
     * Track banner click
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function trackClick(Request $request, $id)
    {
        $banner = DB::table('banners')->find($id);

        if (!$banner) {
            return response()->json([
                'success' => false,
                'message' => 'Banner not found',
            ], 404);
        }

        $user = $request->user();

        // Increment click count
        DB::table('banners')
            ->where('id', $id)
            ->increment('click_count');

        // Record the click with user info
        DB::table('banner_clicks')->insert([
            'banner_id' => $id,
            'user_id' => $user ? $user->id : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'clicked_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Click tracked',
            'link_url' => $banner->link_url,
        ]);
    }
}
