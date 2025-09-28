<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a newly created review for a product.
     */
    public function store(StoreReviewRequest $request, Product $product): JsonResponse
    {
        $user = Auth::user();

        // Check if user has purchased this product
        $hasPurchased = Order::where('buyer_id', $user->id)
            ->whereHas('products', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->exists();

        if (!$hasPurchased) {
            return response()->json([
                'success' => false,
                'message' => __('You can only review products you have purchased'),
            ], 403);
        }

        // Check if user has already reviewed this product
        $existingReview = Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => __('You have already reviewed this product'),
            ], 409);
        }

        // Create the review
        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Load the user relationship
        $review->load('user:id,first_name,last_name');

        return response()->json([
            'success' => true,
            'message' => __('Review created successfully'),
            'data' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'user' => [
                    'id' => $review->user->id,
                    'name' => trim($review->user->first_name . ' ' . $review->user->last_name),
                ],
                'created_at' => $review->created_at,
            ]
        ], 201);
    }
}
