<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SellerDashboardController extends Controller
{
    /**
     * Display seller dashboard statistics.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        
        // Get all orders that contain products from this seller
        $orderQuery = Order::whereHas('products', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });
        
        // Calculate total revenue from seller's products in all orders
        $totalRevenue = Order::whereHas('products', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['products' => function ($query) use ($user) {
            $query->where('user_id', $user->id)->withPivot(['quantity', 'price']);
        }])
        ->get()
        ->sum(function ($order) {
            return $order->products->sum(function ($product) {
                return $product->pivot->quantity * $product->pivot->price;
            });
        });
        
        // Count new orders (pending status)
        $newOrdersCount = (clone $orderQuery)->where('status', 'pending')->count();
        
        // Count completed orders (delivered status)
        $completedOrdersCount = (clone $orderQuery)->where('status', 'delivered')->count();
        
        // Calculate total number of products sold
        $totalProductsSold = Order::whereHas('products', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['products' => function ($query) use ($user) {
            $query->where('user_id', $user->id)->withPivot(['quantity']);
        }])
        ->get()
        ->sum(function ($order) {
            return $order->products->sum('pivot.quantity');
        });
        
        // Get total number of seller's products
        $totalProducts = Product::where('user_id', $user->id)->count();
        
        // Get recent orders (last 5)
        $recentOrders = Order::whereHas('products', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['buyer:id,name,email', 'products' => function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->withPivot(['quantity', 'price', 'status']);
        }])
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
        
        return response()->json([
            'success' => true,
            'message' => __('Dashboard statistics retrieved successfully'),
            'data' => [
                'statistics' => [
                    'total_revenue' => number_format($totalRevenue, 2),
                    'new_orders' => $newOrdersCount,
                    'completed_orders' => $completedOrdersCount,
                    'total_products_sold' => $totalProductsSold,
                    'total_products' => $totalProducts,
                ],
                'recent_orders' => $recentOrders
            ]
        ]);
    }
}
