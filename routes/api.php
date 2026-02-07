<?php

use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserInfoController;
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

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/users', [UserController::class, 'getAllUsers']);
Route::middleware('auth:sanctum')->post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus']);


Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/order/store', [OrderController::class, 'store']);
Route::get('/orders', [OrderController::class, 'index']);

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products-add', [ProductController::class, 'store']);

Route::get('/admin-all', [DashboardController::class, 'index']);

Route::post('/add-userInfo', [UserInfoController::class, 'store']);

Route::post('/edit-userInfo/{id}', [UserInfoController::class, 'update']);




Route::middleware('auth:sanctum')->group(function () {

    Route::post('/create-user', [UserController::class, 'createUser']);

});
