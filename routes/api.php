<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\MeasurementUnitController;
use App\Http\Controllers\StockMovementController;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // CRUD solo admin (Policy lo protegerá)
    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('measurement-units', MeasurementUnitController::class);
    
    // Stock movements — bodeguero y despacho
    Route::apiResource('stock-movements', StockMovementController::class)
         ->only(['index', 'store']);
});
