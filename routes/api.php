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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//a
Route::apiResource('assets', AssetController::class);
Route::get('/assets', [AssetController::class, 'index'])->name('get.asset');
Route::post('/assets_code', [AssetController::class, 'store'])->name('post.asset');
//b
Route::get('/bank_accounts', [BankAccountController::class, 'index'])->name('get.bank_account');
Route::post('/bank_accounts_code', [BankAccountController::class, 'store'])->name('post.bank_account');
//c
Route::get('/customers', [CustomerController::class, 'index'])->name('get.customer');
Route::post('/customers_code', [CustomerController::class, 'store'])->name('post.customer');

//d
Route::get('/deliver_orders', [DeliveryOrderController::class, 'index'])->name('get.delivery_order');
Route::post('/delivery_orders_code', [DeliveryOrderController::class, 'store'])->name('post.delivery_order');
//e
Route::get('/employees', [EmployeeController::class, 'index'])->name('get.employees');
Route::post('/employees_code', [EmployeeController::class, 'index'])->name('post.employees');
//f
//g
//h
//i
//k
//l
//m
//n
//o
//p
Route::get('/products', [ProductController::class, 'index'])->name('get.product');
Route::post('/products_code', [ProductController::class, 'store'])->name('post.products');

Route::get('/payment_types', [PaymentTypeController::class, 'index'])->name('get.payment_type');
Route::post('/payment_types_code', [PaymentTypeController::class, 'store'])->name('post.payment_type');

Route::get('/purchase_orders', [PurchaseOrderController::class, 'index'])->name('get.purchase_order');
Route::post('/purchase_orders_code', [PurchaseOrderController::class, 'store'])->name('post.purchase_order');
//q
//r
//s
//t
//u
//v
//w
//x
//y
//z
