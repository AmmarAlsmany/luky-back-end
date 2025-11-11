<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StaticPagesController extends Controller
{
    /**
     * Get list of static pages
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = DB::table('static_pages');

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title_en', 'LIKE', "%{$search}%")
                  ->orWhere('title_ar', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($status) {
            $query->where('status', $status);
        }

        // Sorting
        $query->orderBy($sortBy, $sortOrder);

        $pages = $query->get();

        return response()->json([
            'success' => true,
            'data' => ['pages' => $pages],
        ]);
    }

    /**
     * Get single static page
     */
    public function show($id)
    {
        $page = DB::table('static_pages')->find($id);

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['page' => $page],
        ]);
    }

    /**
     * Get page by slug
     */
    public function getBySlug($slug)
    {
        $page = DB::table('static_pages')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['page' => $page],
        ]);
    }

    /**
     * Create static page
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:static_pages,slug',
            'content_en' => 'required|string',
            'content_ar' => 'required|string',
            'meta_description_en' => 'nullable|string',
            'meta_description_ar' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'status' => 'in:draft,published',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pageId = DB::table('static_pages')->insertGetId([
            'title_en' => $request->title_en,
            'title_ar' => $request->title_ar,
            'slug' => Str::slug($request->slug),
            'content_en' => $request->content_en,
            'content_ar' => $request->content_ar,
            'meta_description_en' => $request->meta_description_en,
            'meta_description_ar' => $request->meta_description_ar,
            'meta_keywords' => $request->meta_keywords,
            'status' => $request->status ?? 'draft',
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $page = DB::table('static_pages')->find($pageId);

        return response()->json([
            'success' => true,
            'data' => ['page' => $page],
            'message' => 'Page created successfully',
        ], 201);
    }

    /**
     * Update static page
     */
    public function update(Request $request, $id)
    {
        $page = DB::table('static_pages')->find($id);

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title_en' => 'sometimes|string|max:255',
            'title_ar' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:static_pages,slug,' . $id,
            'content_en' => 'sometimes|string',
            'content_ar' => 'sometimes|string',
            'meta_description_en' => 'nullable|string',
            'meta_description_ar' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'status' => 'in:draft,published',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = array_filter([
            'title_en' => $request->title_en,
            'title_ar' => $request->title_ar,
            'slug' => $request->has('slug') ? Str::slug($request->slug) : null,
            'content_en' => $request->content_en,
            'content_ar' => $request->content_ar,
            'meta_description_en' => $request->meta_description_en,
            'meta_description_ar' => $request->meta_description_ar,
            'meta_keywords' => $request->meta_keywords,
            'status' => $request->status,
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ], function ($value) {
            return $value !== null;
        });

        DB::table('static_pages')->where('id', $id)->update($updateData);

        $updatedPage = DB::table('static_pages')->find($id);

        return response()->json([
            'success' => true,
            'data' => ['page' => $updatedPage],
            'message' => 'Page updated successfully',
        ]);
    }

    /**
     * Delete static page
     */
    public function destroy($id)
    {
        $page = DB::table('static_pages')->find($id);

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }

        // Prevent deletion of system pages
        $systemPages = ['privacy-policy', 'terms-and-conditions', 'about-us', 'faq'];
        if (in_array($page->slug, $systemPages)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete system pages. You can edit them instead.',
            ], 422);
        }

        DB::table('static_pages')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Page deleted successfully',
        ]);
    }

    /**
     * Toggle page status
     */
    public function toggleStatus($id)
    {
        $page = DB::table('static_pages')->find($id);

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }

        $newStatus = $page->status === 'published' ? 'draft' : 'published';

        DB::table('static_pages')
            ->where('id', $id)
            ->update([
                'status' => $newStatus,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'data' => ['status' => $newStatus],
            'message' => 'Page status updated successfully',
        ]);
    }

    /**
     * Duplicate page
     */
    public function duplicate($id)
    {
        $page = DB::table('static_pages')->find($id);

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }

        // Generate unique slug
        $newSlug = $page->slug . '-copy';
        $counter = 1;
        while (DB::table('static_pages')->where('slug', $newSlug)->exists()) {
            $newSlug = $page->slug . '-copy-' . $counter;
            $counter++;
        }

        $newPageId = DB::table('static_pages')->insertGetId([
            'title_en' => $page->title_en . ' (Copy)',
            'title_ar' => $page->title_ar . ' (نسخة)',
            'slug' => $newSlug,
            'content_en' => $page->content_en,
            'content_ar' => $page->content_ar,
            'meta_description_en' => $page->meta_description_en,
            'meta_description_ar' => $page->meta_description_ar,
            'meta_keywords' => $page->meta_keywords,
            'status' => 'draft',
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $newPage = DB::table('static_pages')->find($newPageId);

        return response()->json([
            'success' => true,
            'data' => ['page' => $newPage],
            'message' => 'Page duplicated successfully',
        ]);
    }

    /**
     * Get page statistics
     */
    public function stats()
    {
        $stats = [
            'total_pages' => DB::table('static_pages')->count(),
            'published_pages' => DB::table('static_pages')->where('status', 'published')->count(),
            'draft_pages' => DB::table('static_pages')->where('status', 'draft')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
