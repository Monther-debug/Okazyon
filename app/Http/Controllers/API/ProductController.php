<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * Public endpoint - only shows approved products.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['user', 'category'])->where('status', 'approved');

        // Filter by category if provided
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->get();

        return response()->json([
            'data' => $products,
            'message' => 'Products retrieved successfully.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * Protected endpoint - requires authentication.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0|lt:price',
            'image_url' => 'nullable|string|url',
            'expiration_date' => 'nullable|date|after:today',
            'storage_instructions' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        $product = Product::create($validated);
        $product->load(['user', 'category']);

        return response()->json([
            'data' => $product,
            'message' => 'Product created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     * Public endpoint - only shows approved products.
     */
    public function show(Product $product): JsonResponse
    {
        // Only show approved products to public
        if ($product->status !== 'approved') {
            return response()->json([
                'message' => 'Product not found.',
            ], 404);
        }

        $product->load(['user', 'category']);

        // Initialize review data
        $reviewData = [
            'reviews' => [],
            'average_rating' => 0,
            'total_reviews_count' => 0
        ];

        // Only include reviews for non-food products
        if ($product->category && $product->category->type !== 'food') {
            $product->load(['reviews' => function ($query) {
                $query->with('user:id,first_name,last_name')->orderBy('created_at', 'desc');
            }]);

            $reviewData = [
                'reviews' => $product->reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'user' => [
                            'id' => $review->user->id,
                            'name' => trim($review->user->first_name . ' ' . $review->user->last_name),
                        ],
                        'created_at' => $review->created_at,
                    ];
                }),
                'average_rating' => $product->reviews->count() > 0 ? round($product->reviews->avg('rating'), 1) : 0,
                'total_reviews_count' => $product->reviews->count()
            ];
        }

        // Prepare response data
        $responseData = $product->toArray();
        $responseData = array_merge($responseData, $reviewData);

        return response()->json([
            'data' => $responseData,
            'message' => 'Product retrieved successfully.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     * Protected endpoint - requires authentication and ownership.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        // Check if the authenticated user owns this product
        if ($product->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized. You can only update your own products.',
            ], 403);
        }

        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0|lt:price',
            'image_url' => 'nullable|string|url',
            'status' => ['sometimes', Rule::in(['pending', 'approved', 'rejected', 'sold'])],
            'expiration_date' => 'nullable|date|after:today',
            'storage_instructions' => 'nullable|string',
        ]);

        $product->update($validated);
        $product->load(['user', 'category']);

        return response()->json([
            'data' => $product,
            'message' => 'Product updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * Protected endpoint - requires authentication and ownership.
     */
    public function destroy(Product $product): JsonResponse
    {
        // Check if the authenticated user owns this product
        if ($product->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized. You can only delete your own products.',
            ], 403);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.',
        ]);
    }
}
