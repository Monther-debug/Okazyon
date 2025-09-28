<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
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
            'images' => 'nullable|array',
            'images.*' => 'nullable|string|url',
            'expiration_date' => 'nullable|date|after:today',
            'storage_instructions' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        // Remove images from validated data before creating product
        $images = $validated['images'] ?? [];
        unset($validated['images']);

        $product = Product::create($validated);

        // Create product images if provided
        if (!empty($images)) {
            foreach ($images as $imageUrl) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $imageUrl,
                ]);
            }
        }

        $product->load(['user', 'category', 'images']);

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

        // Eager load basic relationships
        $product->load(['images', 'category', 'user:id,first_name,last_name,is_verified']);

        // Prepare seller information
        $seller = [
            'id' => $product->user->id,
            'name' => trim($product->user->first_name . ' ' . $product->user->last_name),
            'is_verified' => $product->user->is_verified ?? false,
        ];

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

            if ($product->reviews->count() > 0) {
                $reviewData = [
                    'reviews' => $product->reviews->map(function ($review) {
                        return [
                            'id' => $review->id,
                            'rating' => $review->rating,
                            'comment' => $review->comment,
                            'user' => [
                                'name' => trim($review->user->first_name . ' ' . $review->user->last_name),
                            ],
                            'created_at' => $review->created_at->diffForHumans(),
                        ];
                    }),
                    'average_rating' => round($product->reviews->avg('rating'), 1),
                    'total_reviews_count' => $product->reviews->count()
                ];
            }
        }

        // Prepare the complete response
        $response = [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'discounted_price' => $product->discounted_price,
            'discount_percentage' => $product->discount_percentage,
            'status' => $product->status,
            'expiration_date' => $product->expiration_date,
            'storage_instructions' => $product->storage_instructions,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
            'images' => $product->images->pluck('image_url'),
            'category' => $product->category,
            'seller' => $seller,
        ];

        // Merge review data
        $response = array_merge($response, $reviewData);

        return response()->json([
            'data' => $response,
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
