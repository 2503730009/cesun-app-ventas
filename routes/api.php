<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\VendorController;
use Illuminate\Support\Facades\Route;

Route::apiResource('vendors', VendorController::class);
Route::apiResource('products', ProductController::class);
