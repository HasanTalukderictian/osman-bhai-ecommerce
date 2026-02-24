<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Reivew;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //


    public function index()
    {
        $orders = Order::all();
        $products = Product::all();
         $users = User::all();
        $review = Reivew::all();

        return response()->json([
            'orders' => $orders,
            'products' => $products,
            'users' => $users,
            'review' => $review

        ], 200);
    }

}
