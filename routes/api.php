<?php

use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\VendorController;
use Illuminate\Support\Facades\Route;

Route::apiResource('vendors', VendorController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('orders', OrderController::class)->only(['index', 'show']);
Route::get('inventory', [InventoryController::class, 'index']);
Route::get('inventory/{product}', [InventoryController::class, 'show']);

Route::prefix('auth')->group(function (): void {
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->group(function (): void {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

Route::middleware('auth:api')->group(function (): void {
    Route::patch('inventory/{product}', [InventoryController::class, 'update']);
    Route::post('orders', [OrderController::class, 'store']);
});
