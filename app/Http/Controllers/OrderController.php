<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Validate input
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
        ]);

        // Create Order without cart_items
        $order = Order::create([
            'customer_name'   => $request->customerName,
            'phone'           => $request->phone,
            'address'         => $request->address,
            'district'        => $request->district,
            'thana'           => $request->thana,
            'total_price'     => $request->totalPrice,
            'delivery_charge' => $request->deliveryCharge,
            'final_total'     => $request->finalTotal,
        ]);

        // Save each item to OrderItem table
        foreach ($request->cartItems as $item) {
            OrderItem::create([
                'order_id'      => $order->id,
                'product_id'    => $item['id'],
                'product_name'  => $item['product_name'] ?? $item['productName'] ?? 'Unnamed Product',
                'image_url'     => $item['image_url'] ?? $item['imageUrl'] ?? null,
                'price'         => $item['price'] ?? 0,
                'quantity'      => $item['quantity'] ?? 1,
            ]);
        }


        return response()->json([
            'status'   => true,
            'message'  => 'Order placed successfully!',
            'order_id' => $order->id,
        ], 200);
    }

    public function index()
    {
        // Load orders with related items
        $orders = Order::with('items')->orderBy('id', 'DESC')->get();

        return response()->json([
            'status'  => true,
            'message' => 'All orders fetched successfully',
            'data'    => $orders
        ], 200);
    }
}
