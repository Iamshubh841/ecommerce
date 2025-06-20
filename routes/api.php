<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SellerController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/login', [AuthController::class, 'adminLogin']);
Route::post('/seller/login', [AuthController::class, 'sellerLogin']);

Route::middleware(['auth:sanctum', 'ability:role:admin'])->group(function () {
    Route::post('/create-sellers', [AdminController::class, 'createSeller']);
    Route::get('/sellers', [AdminController::class, 'listSellers']);
});

Route::middleware(['auth:sanctum', 'ability:role:seller'])->group(function () {
    Route::post('/products', [SellerController::class, 'addProduct']);
    Route::get('/products', [SellerController::class, 'listProducts']);
    Route::delete('/products/{id}', [SellerController::class, 'deleteProduct']);
});
