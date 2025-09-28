<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Search for products based on query.
     * Public endpoint - searches across product names, descriptions, and categories.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        // Return empty results if no query provided
        if (empty(trim($query))) {
            return response()->json([
                'data' => [],
                'total' => 0,
                'query' => $query,
                'message' => 'Please provide a search query.',
            ]);
        }

        // Build comprehensive search query
        $searchQuery = Product::with(['user', 'category', 'images'])
            ->where('status', 'approved') // Only approved products
            ->where(function ($q) use ($query) {
                $searchTerm = '%' . $query . '%';
                
                // Search in product name and description
                $q->where('name', 'LIKE', $searchTerm)
                  ->orWhere('description', 'LIKE', $searchTerm)
                  // Search in category name
                  ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'LIKE', $searchTerm);
                  });
            });

        // Add category filter if provided
        if ($request->has('category_id') && !empty($request->category_id)) {
            $searchQuery->where('category_id', $request->category_id);
        }

        // Add sorting options
        $sortBy = $request->get('sort_by', 'relevance');
        switch ($sortBy) {
            case 'price_low':
                $searchQuery->orderBy('price', 'asc');
                break;
            case 'price_high':
                $searchQuery->orderBy('price', 'desc');
                break;
            case 'newest':
                $searchQuery->orderBy('created_at', 'desc');
                break;
            case 'relevance':
            default:
                // Order by relevance: exact name matches first, then description matches
                $searchQuery->orderByRaw("
                    CASE 
                        WHEN name LIKE ? THEN 1
                        WHEN description LIKE ? THEN 2
                        ELSE 3
                    END
                ", ['%' . $query . '%', '%' . $query . '%'])
                ->orderBy('created_at', 'desc');
                break;
        }

        // Get results with pagination
        $perPage = $request->get('per_page', 20);
        $products = $searchQuery->paginate($perPage);

        // Get user's favorites if authenticated
        $userFavorites = [];
        if (Auth::check()) {
            $userFavorites = Auth::user()->favorites()->pluck('product_id')->toArray();
        }

        // Transform products with is_favorited attribute (same format as ProductController)
        $transformedProducts = $products->getCollection()->map(function ($product) use ($userFavorites) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'discounted_price' => $product->discounted_price,
                'discount_percentage' => $product->discount_percentage,
                'status' => $product->status,
                'images' => $product->images->pluck('image_url'),
                'category' => $product->category,
                'seller' => [
                    'name' => trim($product->user->first_name . ' ' . $product->user->last_name),
                ],
                'is_favorited' => in_array($product->id, $userFavorites),
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

        return response()->json([
            'data' => $transformedProducts,
            'total' => $products->total(),
            'per_page' => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'query' => $query,
            'sort_by' => $sortBy,
            'message' => $products->total() > 0 
                ? "Found {$products->total()} results for '{$query}'"
                : "No results found for '{$query}'",
        ]);
    }
}
