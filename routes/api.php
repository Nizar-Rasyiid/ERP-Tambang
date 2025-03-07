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
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DetailPoController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\OpexController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\DetailSoController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\QuatationController;
use App\Http\Controllers\DetailQuatationController;
use App\Http\Controllers\TandaTerimaController;

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

//a
Route::get('/assets', [AssetController::class, 'index'])->name('get.asset');
Route::post('/assets_code', [AssetController::class, 'store'])->name('post.asset');

Route::get('/account_receivable', [SalesOrderController::class, 'getAR'])->name('get.ar');
Route::get('/account_payable', [PurchaseOrderController::class, 'getAP'])->name('get.ap');
//b
Route::get('/bank_accounts', [BankAccountController::class, 'index'])->name('get.bank_account');
Route::get('/bank_accounts/{id}', [BankAccountController::class, 'show'])->name('show.bank_account');
Route::post('/bank_accounts_code', [BankAccountController::class, 'store'])->name('post.bank_account');
//c
// Route::apiResource('customers', CustomerController::class);
Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('show.customer');
Route::get('/customers', [CustomerController::class, 'index'])->name('get.customer');
Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('update.customer');
Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('delete.customer');
Route::post('/store-customers', [CustomerController::class, 'store'])->name('post.customer');
// Route::post('/customers_code', [CustomerController::class, 'store'])->name('post.customer');


Route::get('/delivery_orders', [DeliveryOrderController::class, 'index'])->name('get.delivery_order');
Route::get('/delivery_orders/{id}', [DeliveryOrderController::class, 'show'])->name('get.do');
Route::get('/delivery_sales/{id}', [DeliveryOrderController::class, 'SoShow']);
Route::post('/store-do', [DeliveryOrderController::class, 'store'])->name('store.do');

Route::get('/details_po', [DetailPoController::class, 'index'])->name('get.detailpo');
Route::get('/detail_po/{id}', [DetailPoController::class, 'show'])->name('show.detailpo');

Route::get('/details_so', [DetailSoController::class, 'index'])->name('get.detailso');
Route::get('/details_so/{id}', [DetailSoController::class, 'show'])->name('show.detailso');

Route::get('/detail_do/{id}', [DetailSoController::class, 'DoShow'])->name('get.do');
Route::get('/detail_quatation/{id}', [DetailQuatationController::class, 'show'])->name('get.do');

Route::get('/employees', [EmployeeController::class, 'index'])->name('get.employees');
Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('show.employees');
Route::post('/employees_code', [EmployeeController::class, 'store'])->name('post.employees');

Route::get('/invoices', [InvoiceController::class, 'index'])->name('get.invoices');
Route::post('/invoices_code', [InvoiceController::class, 'store'])->name('post.invoices');
Route::get('/detail_invoices/{id}', [InvoiceController::class, 'detail'])->name('detail.invoices');

Route::get('/inquiry', [InquiryController::class, 'index'])->name('get.inquiry');
Route::post('/inquiry_code', [InquiryController::class, 'store'])->name('store.inquiry');

Route::get('/products', [ProductController::class, 'index'])->name('get.product');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('show.product');
Route::get('/products/search', [ProductController::class, 'search'])->name('search.product');
Route::post('/products_code', [ProductController::class, 'store'])->name('post.products');

Route::get('/payment_types', [PaymentTypeController::class, 'index'])->name('get.payment_type');
Route::post('/payment_types_code', [PaymentTypeController::class, 'store'])->name('post.payment_type');

Route::get('/purchase_orders', [PurchaseOrderController::class, 'index'])->name('get.purchase_order');
Route::post('/purchase_orders_code', [PurchaseOrderController::class, 'store'])->name('post.purchase_order');
Route::get('/purchase_orders/{id}', [PurchaseOrderController::class, 'show'])->name('show.purchase_order');

Route::get('/quatations', [QuatationController::class, 'index'])->name('get.quatation');
Route::get('/quatations/{id}', [QuatationController::class, 'show'])->name('get.quatation');
Route::post('/quatations_code', [QuatationController::class, 'store'])->name('post.quatation');

Route::get('/vendors', [VendorController::class, 'index'])->name('get.vendor');
Route::post('/vendors_code', [VendorController::class, 'store'])->name('post.vendor');

Route::get('/opex', [OpexController::class, 'index'])->name('get.opex');
Route::post('/opex_code', [OpexController::class, 'store'])->name('post.opex');

Route::get('/sales_orders', [SalesOrderController::class, 'index'])->name('get.sales_order');
Route::get('/sales_orders/{id}', [SalesOrderController::class, 'show'])->name('show.sales_order');
Route::post('/sales_orders_code', [SalesOrderController::class, 'store'])->name('post.sales_order');
//q
//r
//s
//t
Route::get('/tandater', [TandaTerimaController::class, 'index'])->name('get.tandater');
//u
//v
//w
//x
//y
//z
