<?php

use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/order/store', [OrderController::class, 'store']);
Route::get('/orders', [OrderController::class, 'index']);

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products-add', [ProductController::class, 'store']);

Route::get('/admin-all', [DashboardController::class, 'index']);

