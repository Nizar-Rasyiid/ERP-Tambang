<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\PurchaseOrderController;

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

// Employee routes
Route::apiResource('employees', EmployeeController::class);

// Product routes
Route::apiResource('products', ProductController::class);

// Customer routes
Route::apiResource('customers', CustomerController::class);

// Asset routes
Route::apiResource('assets', AssetController::class);

Route::apiResource('payment-types', PaymentTypeController::class);
Route::apiResource('bank-accounts', BankAccountController::class);
Route::apiResource('delivery-orders', DeliveryOrderController::class);
Route::apiResource('purchase-orders', PurchaseOrderController::class);
