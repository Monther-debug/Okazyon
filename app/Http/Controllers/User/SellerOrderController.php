<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Order\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerOrderController extends Controller
{
    /**
     * Display a listing of incoming orders for the seller.
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
     * Display the specified order details with authorization check.
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
     * Update the order's status with authorization check.
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $user = Auth::user();
        
        // Check if this order contains any products from this seller
        $hasSellerProducts = $order->products()->where('user_id', $user->id)->exists();
        
        if (!$hasSellerProducts) {
            return response()->json([
                'success' => false,
                'message' => __('Order not found or you do not have permission to update it'),
            ], 404);
        }

        // Update the order status
        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Order status updated successfully'),
            'data' => [
                'order_id' => $order->id,
                'new_status' => $order->status,
                'updated_at' => $order->updated_at
            ]
        ]);
    }
}
