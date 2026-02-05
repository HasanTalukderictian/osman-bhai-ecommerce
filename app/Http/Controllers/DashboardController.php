<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //

    public function index()
    {
        $orders = Order::with(['items.product'])->orderBy('id', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}
