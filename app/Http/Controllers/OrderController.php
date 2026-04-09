<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{



// 🔥 꼭 add korte hobe

public function store(Request $request)
{
    // ✅ Validate input
    $request->validate([
        'customerName' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string',
        'district' => 'required|string',
        'thana' => 'required|string',
        'totalPrice' => 'required|numeric',
        'deliveryCharge' => 'required|numeric',
        'finalTotal' => 'required|numeric',
        'cartItems' => 'required|array',
        'cartItems.*.id' => 'required|integer',
        'cartItems.*.quantity' => 'required|integer|min:1',
    ]);

    // ✅ OTP CHECK
    $otpVerified = DB::table('otps')
        ->where('phone', $request->phone)
        ->where('is_verified', 1)
        ->first();

    if (!$otpVerified) {
        return response()->json([
            'status' => false,
            'message' => 'OTP not verified'
        ], 400);
    }

    DB::beginTransaction();

    try {

        // ✅ customer id (optional fallback)
        $customerId = auth('sanctum')->id() ?? $request->customer_id ?? null;

        // ✅ Create Order
        $order = Order::create([
            'customer_name'   => $request->customerName,
            'customer_id'     => $customerId,
            'phone'           => $request->phone,
            'address'         => $request->address,
            'district'        => $request->district,
            'thana'           => $request->thana,
            'total_price'     => $request->totalPrice,
            'delivery_charge' => $request->deliveryCharge,
            'final_total'     => $request->finalTotal,
        ]);

        // ✅ Loop cart items
        foreach ($request->cartItems as $item) {

            $product = Product::find($item['id']);

            if (!$product) {
                throw new \Exception("Product not found");
            }

            // 🔥 Stock check
            if ($product->quantity < $item['quantity']) {
                throw new \Exception($product->name . " stock not available");
            }

            // 🔥 Reduce quantity
            $product->quantity -= $item['quantity'];
            $product->save();

            // ✅ Save order item
            OrderItem::create([
                'order_id'      => $order->id,
                'product_id'    => $product->id,
                'product_name'  => $item['product_name'] ?? $item['productName'] ?? 'Unnamed Product',
                'image_url'     => $item['image_url'] ?? $item['imageUrl'] ?? null,
                'price'         => $item['price'] ?? 0,
                'quantity'      => $item['quantity'],
            ]);
        }

        // ✅ OTP delete after use
        DB::table('otps')->where('phone', $request->phone)->delete();

        DB::commit();

        return response()->json([
            'status'   => true,
            'message'  => 'Order placed successfully!',
            'order_id' => $order->id,
        ], 200);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => 'Order failed',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function index()
{
    $orders = Order::with([
        'items.product.images' // VERY IMPORTANT
    ])
    ->latest()
    ->get();

    $formatted = $orders->map(function ($order) {

        return [
            'id' => $order->id,
            'customer_name' => $order->customer_name,
            'phone' => $order->phone,
            'address' => $order->address,
            'district' => $order->district,
            'thana' => $order->thana,
            'total_price' => $order->total_price,
            'delivery_charge' => $order->delivery_charge,
            'final_total' => $order->final_total,
            'tracking_number' => $order->tracking_number,
            'created_at' => $order->created_at,

            'items' => $order->items->map(function ($item) {

                $imageUrl = null;

                if (
                    $item->product &&
                    $item->product->images &&
                    $item->product->images->count() > 0
                ) {
                    $imageUrl = asset(
                        'storage/' .
                        $item->product->images->first()->image_path
                    );
                }

                return [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'image_url' => $imageUrl
                ];
            })
        ];
    });

    return response()->json([
        'status' => true,
        'message' => 'All orders fetched successfully',
        'data' => $formatted
    ]);
}
public function destroy($id)
{
    // Find the order
    $order = Order::find($id);

    if (!$order) {
        return response()->json([
            'status' => false,
            'message' => 'Order not found'
        ], 404);
    }

    // Delete associated items first
    $order->items()->delete();

    // Delete the order
    $order->delete();

    return response()->json([
        'status' => true,
        'message' => 'Order deleted successfully'
    ]);
}


public function updateTrackingNumber(Request $request, $id)
{
    $request->validate([
        'tracking_number' => 'required|string'
    ]);

    $order = Order::find($id);

    if (!$order) {
        return response()->json([
            'status' => false,
            'message' => 'Order not found'
        ], 404);
    }

    $order->tracking_number = $request->tracking_number;
    $order->save();

    return response()->json([
        'status' => true,
        'message' => 'Tracking number saved successfully',
        'tracking_number' => $order->tracking_number
    ]);
}





    // ✅ 3. STORE ORDER


    // ✅ SMS FUNCTION


}
