<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    /**
     * Display the banners management page
     */
    public function index()
    {
        $banners = DB::table('banners')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($banner) {
                // Add full image URL and determine status badge
                $now = now();
                $startDate = new \DateTime($banner->start_date);
                $endDate = new \DateTime($banner->end_date);

                if ($now >= $startDate && $now <= $endDate) {
                    $statusBadge = 'Active';
                    $statusClass = 'bg-success';
                } elseif ($now < $startDate) {
                    $statusBadge = 'Upcoming';
                    $statusClass = 'bg-warning';
                } else {
                    $statusBadge = 'Expired';
                    $statusClass = 'bg-danger';
                }

                $banner->image_full_url = $banner->image_url ? asset('storage/' . $banner->image_url) : null;
                $banner->status_badge = $statusBadge;
                $banner->status_class = $statusClass;

                return $banner;
            });

        // Get all approved and active providers
        $providers = DB::table('service_providers')
            ->join('users', 'service_providers.user_id', '=', 'users.id')
            ->where('service_providers.verification_status', 'approved')
            ->where('service_providers.is_active', true)
            ->select(
                'service_providers.id',
                'service_providers.business_name',
                'users.name as owner_name'
            )
            ->orderBy('service_providers.business_name')
            ->get();

        return view('banners.banners', compact('banners', 'providers'));
    }

    /**
     * Store a new banner
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
     * Delete a banner
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
     * Get banner data for editing
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

        return response()->json([
            'success' => true,
            'data' => ['banner' => $banner],
        ]);
    }
}
