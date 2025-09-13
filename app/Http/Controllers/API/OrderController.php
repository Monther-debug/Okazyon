<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * Show buyer's order history.
     */
    public function index(): JsonResponse
    {
        $orders = Order::with(['products', 'buyer'])
                      ->where('buyer_id', Auth::id())
                      ->latest()
                      ->get();

        return response()->json([
            'data' => $orders,
            'message' => 'Orders retrieved successfully.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * Main checkout method.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'delivery_address' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $orderItems = [];

            // Validate products and calculate total
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check if product is approved
                if ($product->status !== 'approved') {
                    return response()->json([
                        'message' => "Product '{$product->name}' is not available for purchase.",
                    ], 422);
                }

                // Use discounted price if available, otherwise regular price
                $price = $product->discounted_price ?? $product->price;
                $itemTotal = $price * $item['quantity'];
                $totalAmount += $itemTotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $price, // Store price at time of order
                ];
            }

            // Create the order
            $order = Order::create([
                'buyer_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'delivery_address' => $validated['delivery_address'],
                'status' => 'pending',
            ]);

            // Attach products to order with pivot data
            foreach ($orderItems as $orderItem) {
                $order->products()->attach($orderItem['product_id'], [
                    'quantity' => $orderItem['quantity'],
                    'price' => $orderItem['price'],
                ]);
            }

            DB::commit();

            // Load relationships for response
            $order->load(['products', 'buyer']);

            return response()->json([
                'data' => $order,
                'message' => 'Order placed successfully.',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Order placement failed. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * Show single order with authorization check.
     */
    public function show(Order $order): JsonResponse
    {
        // Check if the authenticated user is the buyer of this order
        if ($order->buyer_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized. You can only view your own orders.',
            ], 403);
        }

        $order->load(['products', 'buyer']);

        return response()->json([
            'data' => $order,
            'message' => 'Order retrieved successfully.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     * For future use (order status updates by admin/seller)
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        // This method can be implemented later for order status updates
        return response()->json([
            'message' => 'Order updates not implemented yet.',
        ], 501);
    }

    /**
     * Remove the specified resource from storage.
     * For future use (order cancellation)
     */
    public function destroy(Order $order): JsonResponse
    {
        // Check if the authenticated user is the buyer
        if ($order->buyer_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized. You can only cancel your own orders.',
            ], 403);
        }

        // Only allow cancellation of pending orders
        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending orders can be cancelled.',
            ], 422);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Order cancelled successfully.',
        ]);
    }
}
