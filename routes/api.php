<?php

use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\VendorController;
use Illuminate\Support\Facades\Route;

Route::apiResource('vendors', VendorController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'store']);
Route::get('inventory', [InventoryController::class, 'index']);
Route::get('inventory/{product}', [InventoryController::class, 'show']);
Route::patch('inventory/{product}', [InventoryController::class, 'update']);
