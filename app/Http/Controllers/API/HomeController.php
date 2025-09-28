<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Get curated product lists for the home page.
     * Public endpoint - returns featured deals, new deals, and other curated content.
     */
    public function index(): JsonResponse
    {
        // Get user's favorites if authenticated
        $userFavorites = [];
        if (Auth::check()) {
            $userFavorites = Auth::user()->favorites()->pluck('product_id')->toArray();
        }

        // Helper function to transform products consistently
        $transformProduct = function ($product) use ($userFavorites) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'discounted_price' => $product->discounted_price,
                'discount_percentage' => $product->discount_percentage,
                'status' => $product->status,
                'is_featured' => $product->is_featured,
                'images' => $product->images->pluck('image_url'),
                'category' => $product->category,
                'seller' => [
                    'name' => trim($product->user->first_name . ' ' . $product->user->last_name),
                ],
                'is_favorited' => in_array($product->id, $userFavorites),
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        };

        // 1. Featured Deals - Products marked as featured
        $featuredDeals = Product::with(['user', 'category', 'images'])
            ->approved()
            ->featured()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map($transformProduct);

        // 2. New Deals - Recently added products
        $newDeals = Product::with(['user', 'category', 'images'])
            ->approved()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map($transformProduct);

        // 3. Best Discounts - Products with highest discount percentages
        $bestDiscounts = Product::with(['user', 'category', 'images'])
            ->approved()
            ->whereNotNull('discounted_price')
            ->where('discounted_price', '>', 0)
            ->get()
            ->sortByDesc('discount_percentage')
            ->take(10)
            ->values()
            ->map($transformProduct);

        // 4. Category Highlights - Popular products from different categories
        $categoryHighlights = Product::with(['user', 'category', 'images'])
            ->approved()
            ->inRandomOrder()
            ->limit(8)
            ->get()
            ->map($transformProduct);

        return response()->json([
            'data' => [
                'featured_deals' => [
                    'title' => 'Featured Deals',
                    'products' => $featuredDeals,
                    'total' => $featuredDeals->count(),
                ],
                'new_deals' => [
                    'title' => 'New Arrivals',
                    'products' => $newDeals,
                    'total' => $newDeals->count(),
                ],
                'best_discounts' => [
                    'title' => 'Best Discounts',
                    'products' => $bestDiscounts,
                    'total' => $bestDiscounts->count(),
                ],
                'category_highlights' => [
                    'title' => 'Trending Now',
                    'products' => $categoryHighlights,
                    'total' => $categoryHighlights->count(),
                ],
            ],
            'message' => 'Home page data retrieved successfully.',
        ]);
    }
}
