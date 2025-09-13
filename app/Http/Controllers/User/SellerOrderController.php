<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Order\UpdateProductStatusRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerOrderController extends Controller
{
    /**
     * Display a listing of orders containing the seller's products.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Get all orders that contain products from this seller
        $orders = Order::whereHas('products', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['buyer:id,name,email', 'products' => function ($query) use ($user) {
            // Only load products that belong to this seller
            $query->where('user_id', $user->id)->withPivot(['quantity', 'price', 'status']);
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return response()->json([
            'success' => true,
            'message' => __('Orders retrieved successfully'),
            'data' => $orders
        ]);
    }

    /**
     * Display the specified order with seller's products only.
     */
    public function show(Order $order): JsonResponse
    {
        $user = Auth::user();
        
        // Check if this order contains any products from this seller
        $hasSellerProducts = $order->products()->where('user_id', $user->id)->exists();
        
        if (!$hasSellerProducts) {
            return response()->json([
                'success' => false,
                'message' => __('Order not found or you do not have permission to view it'),
            ], 404);
        }

        // Load the order with only the seller's products
        $order->load([
            'buyer:id,name,email,phone',
            'products' => function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->withPivot(['quantity', 'price', 'status']);
            }
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Order retrieved successfully'),
            'data' => $order
        ]);
    }

    /**
     * Update the status of specific products in an order (seller can only update their own products).
     */
    public function updateProductStatus(UpdateProductStatusRequest $request, Order $order): JsonResponse
    {
        $user = Auth::user();
        $productId = $request->product_id;

        // Verify the product belongs to this seller and is in this order
        $product = Product::where('id', $productId)
                          ->where('user_id', $user->id)
                          ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => __('Product not found or you do not have permission to update it'),
            ], 404);
        }

        // Check if the product is in this order
        $orderProduct = $order->products()->where('product_id', $productId)->first();
        
        if (!$orderProduct) {
            return response()->json([
                'success' => false,
                'message' => __('Product is not part of this order'),
            ], 404);
        }

        // Update the product status in the order_items pivot table
        $order->products()->updateExistingPivot($productId, [
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Product status updated successfully'),
            'data' => [
                'product_id' => $productId,
                'new_status' => $request->status
            ]
        ]);
    }

    /**
     * Get order statistics for the seller.
     */
    public function statistics(): JsonResponse
    {
        $user = Auth::user();
        
        // Get statistics for orders containing seller's products
        $totalOrders = Order::whereHas('products', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        $pendingOrders = Order::where('status', 'pending')
                             ->whereHas('products', function ($query) use ($user) {
                                 $query->where('user_id', $user->id);
                             })->count();

        $completedOrders = Order::where('status', 'delivered')
                                ->whereHas('products', function ($query) use ($user) {
                                    $query->where('user_id', $user->id);
                                })->count();

        // Calculate total revenue from seller's products in orders
        $totalRevenue = Order::whereHas('products', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['products' => function ($query) use ($user) {
            $query->where('user_id', $user->id)->withPivot(['quantity', 'price', 'status']);
        }])
        ->get()
        ->sum(function ($order) {
            return $order->products->sum(function ($product) {
                return $product->pivot->quantity * $product->pivot->price;
            });
        });

        return response()->json([
            'success' => true,
            'message' => __('Statistics retrieved successfully'),
            'data' => [
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'completed_orders' => $completedOrders,
                'total_revenue' => number_format($totalRevenue, 2)
            ]
        ]);
    }
}
