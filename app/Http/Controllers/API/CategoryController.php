<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // Get all categories with their approved products count
        $categories = Category::select('id', 'name', 'image_url', 'type')
            ->with(['products' => function ($query) {
                $query->where('status', 'approved');
            }])
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image_url' => $category->image_url,
                    'type' => $category->type,
                    'deals_available_count' => $category->products->count()
                ];
            });

        // Group categories by type
        $groupedCategories = $categories->groupBy('type');

        // Format the response
        $response = [
            'goods' => $groupedCategories->get('goods', collect())->map(function ($category) {
                return [
                    'id' => $category['id'],
                    'name' => $category['name'],
                    'image_url' => $category['image_url'],
                    'deals_available_count' => $category['deals_available_count']
                ];
            })->values()->toArray(),
            'food' => $groupedCategories->get('food', collect())->map(function ($category) {
                return [
                    'id' => $category['id'],
                    'name' => $category['name'],
                    'image_url' => $category['image_url'],
                    'deals_available_count' => $category['deals_available_count']
                ];
            })->values()->toArray()
        ];

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
