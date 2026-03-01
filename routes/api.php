<?php

use App\Http\Controllers\AllCategoryController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CouierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HeaderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ParentCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\TeamController;
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


Route::middleware('auth:sanctum')->group(function () {

Route::post('/logout', [AuthController::class, 'logout']);

    // Users
    Route::get('/users', [UserController::class, 'getAllUsers']);
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus']);
    Route::post('/create-user', [UserController::class, 'createUser']);



});
 Route::get('/dashboard-data', [DashboardController::class, 'index']);

Route::post('/add-products', [ProductImageController::class, 'store']);
Route::get('/get-products', [ProductImageController::class, 'index']);
Route::post('/update-products/{id}', [ProductImageController::class, 'update']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/api/edit-userInfo/{id}', [UserInfoController::class, 'update']);
Route::get('/get-userInfo', [UserInfoController::class, 'index']);

Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/order/store', [OrderController::class, 'store']);
Route::get('/orders', [OrderController::class, 'index']);
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
Route::post('/orders/{id}/tracking', [OrderController::class, 'updateTrackingNumber']);

Route::get('/products', [ProductController::class, 'index']);
Route::delete('/products-del/{id}', [ProductController::class, 'destroy']);
Route::post('/products-update/{id}', [ProductController::class, 'update']);
Route::post('/products-add', [ProductController::class, 'store']);

Route::get('/admin-all', [DashboardController::class, 'index']);

Route::post('/add-userInfo', [UserInfoController::class, 'store']);

Route::post('/edit-userInfo/{id}', [UserInfoController::class, 'update']);

Route::post('/add-banner', [BannerController::class, 'store']);
Route::get('/get-banner', [BannerController::class, 'index']);
Route::delete('/del-banner/{id}', [BannerController::class, 'destroy']);

Route::get('/get-contact', [ContactController::class, 'index'])->withoutMiddleware('throttle:api');
Route::post('/add-contact', [ContactController::class, 'store']);
Route::post('/edit-contact/{id}', [ContactController::class, 'update']);
Route::delete('/del-contact/{id}', [ContactController::class, 'destroy']);



Route::post('/add-team', [TeamController::class, 'store']);
Route::get('/get-team', [TeamController::class, 'index']);
Route::delete('/del-team/{id}', [TeamController::class, 'destroy']);


Route::post('/add-header', [HeaderController::class, 'store']);
Route::get('/get-header', [HeaderController::class, 'index'])->withoutMiddleware('throttle:api');
Route::delete('/del-header/{id}', [HeaderController::class, 'destroy']);

Route::post('/add-reviews', [ReviewController::class, 'store']);
Route::delete('/del-reviews/{id}', [ReviewController::class, 'destroy']);
Route::get('/get-reviews', [ReviewController::class, 'index']);



Route::delete('/delete-parent-category/{id}', [ParentCategoryController::class, 'destroy']);
Route::delete('/delete-sub-category/{id}', [SubCategoryController::class, 'destroy']);
Route::get('/products/{parent}/{subcategory}', [ProductController::class, 'productsBySubcategory']);
Route::get('/all-category', [AllCategoryController::class, 'index']);
Route::post('/parent-category/store', [ParentCategoryController::class, 'store']);
Route::post('/sub-category/store', [SubCategoryController::class, 'store']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/create-user', [UserController::class, 'createUser']);

});

Route::prefix('couriers')->group(function () {
    Route::get('/', [CouierController::class, 'index']);        // List all courier settings
    Route::post('/', [CouierController::class, 'store']);       // Create new courier
    Route::delete('/{id}', [CouierController::class, 'destroy']); // Delete courier by ID
});




Route::prefix('stores')->group(function () {
    Route::get('/', [StoreController::class, 'index']);   // List all stores
    Route::post('/', [StoreController::class, 'store']);  // Create new store
});
