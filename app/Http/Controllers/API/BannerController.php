<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    /**
     * Get the active banner for the home page.
     * Public endpoint - returns the first active banner.
     */
    public function index(): JsonResponse
    {
        $banner = Banner::where('is_active', true)
                       ->orderBy('created_at', 'desc')
                       ->first();

        if (!$banner) {
            return response()->json([
                'data' => null,
                'message' => 'No active banner found.',
            ]);
        }

        return response()->json([
            'data' => [
                'id' => $banner->id,
                'title' => $banner->title,
                'subtitle' => $banner->subtitle,
                'image_url' => $banner->image_url,
                'link' => $banner->link,
                'is_active' => $banner->is_active,
                'created_at' => $banner->created_at,
            ],
            'message' => 'Active banner retrieved successfully.',
        ]);
    }
}
