<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Order::with('items.product')->where('user_id', Auth::id())->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'user_id' => Auth::id(),
            'total_price' => 0,
            'status' => 'pending',
        ]);

        $total = 0;

        foreach ($request->items as $item) {
            $product = product::find($item['product_id']);

            $total += $product->price * $item['quantity'];

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ]);
        }

        $order->update(['total_price' => $total]);

        return $order->load('items.product');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return $order->load('items.product');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        // Optional: ensure user owns the order
        $user = auth('sanctum')->user();

        if (!$user || $order->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'sometimes|string|in:pending,paid,shipped,cancelled',
            'items' => 'sometimes|array',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $order) {

            $total = $order->total_price;

            // 🔄 Update items if provided
            if ($request->has('items')) {

                // Remove old items
                $order->items()->delete();
                $total = 0;

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    $total += $product->price * $item['quantity'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                    ]);
                }
            }

            // 🔄 Update status if provided
            if ($request->has('status')) {
                $order->status = $request->status;
            }

            $order->total_price = $total;
            $order->save();
        });

        return $order->load('items.product');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json(['message' => 'Order deleted']);
    }
}
