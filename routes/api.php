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
use App\Http\Controllers\FakturPajakController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ReportController;

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
Route::post('/regis', [AuthController::class, 'Register']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/users/{user}/assign-role', [AuthController::class, 'assignRole']);
    Route::post('/users/{user}/assign-permissions', [AuthController::class, 'assignPermissions']);
    Route::get('/users/{user}/permissions', [AuthController::class, 'getPermissions']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

//a
Route::get('/assets', [AssetController::class, 'index'])->name('get.asset');
Route::get('/assets/{id}', [AssetController::class, 'show'])->name('get.assetDetail');
Route::post('/assets_code', [AssetController::class, 'store'])->name('post.asset');
Route::put('/assets_code/{id}', [AssetController::class, 'update'])->name('put.asset');

Route::get('/account_receivable', [SalesOrderController::class, 'getAR'])->name('get.ar');
Route::put('/account_receivable_deposit/{id}', [SalesOrderController::class, 'updateDeposit'])->name('put.ar');


Route::get('/account_payable', [PurchaseOrderController::class, 'getAP'])->name('get.ap');
//b
Route::get('/bank_accounts', [BankAccountController::class, 'index'])->name('get.bank_account');
Route::get('/bank_accounts/{id}', [BankAccountController::class, 'show'])->name('show.bank_account');
Route::post('/bank_accounts_code', [BankAccountController::class, 'store'])->name('post.bank_account');
//c
// Route::apiResource('customers', CustomerController::class);
Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('show.customer');
Route::get('/customers', [CustomerController::class, 'index'])->name('get.customer');
Route::get('/customers/point/{id}', [CustomerController::class, 'cusPoint'])->name('cuspoint.customer');
Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('delete.customer');
Route::post('/store-customers', [CustomerController::class, 'store'])->name('post.customer');
Route::put('/store-customers/{id}', [CustomerController::class, 'update'])->name('put.customer');

Route::get('/file', [DocumentController::class, 'index'])->name('get.file');
Route::get('/documents/{filename}', [DocumentController::class, 'show'])->name('show.file');
Route::post('/file-upload', [DocumentController::class, 'uploadFile'])->name('get.doc');

Route::get('/delivery_orders', [DeliveryOrderController::class, 'index'])->name('get.delivery_order');
Route::get('/delivery_orders/{id}', [DeliveryOrderController::class, 'show'])->name('get.do');
Route::get('/delivery_sales/{id}', [DeliveryOrderController::class, 'SoShow']);
Route::post('/store-do', [DeliveryOrderController::class, 'store'])->name('store.do');

Route::get('/details_po', [DetailPoController::class, 'index'])->name('get.detailpo');
Route::get('/detail_po/{id}', [DetailPoController::class, 'show'])->name('show.detailpo');

Route::get('/details_so', [DetailSoController::class, 'index'])->name('get.detailso');
Route::get('/details_so/{id}', [DetailSoController::class, 'show'])->name('show.detailso');

Route::get('/detail_do/{id}', [DetailSoController::class, 'DoShow'])->name('get.doDetail');
Route::get('/detail_quatation/{id}', [DetailQuatationController::class, 'show'])->name('get.quatationDetail');

Route::get('/employees', [EmployeeController::class, 'index'])->name('get.employees');
Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('show.employees');
Route::post('/employees_code', [EmployeeController::class, 'store'])->name('post.employees');
Route::put('/employees_code/{id}', [EmployeeController::class, 'update'])->name('put.employees');

Route::get('/invoices', [InvoiceController::class, 'index'])->name('get.invoices');
Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('show.invoice');
Route::get('/invoices/code/{id}', [InvoiceController::class, 'InvoiceSo'])->name('show.invoices');
Route::post('/invoices_code', [InvoiceController::class, 'store'])->name('post.invoice');
Route::post('/invoices_code/appr/{id}', [InvoiceController::class, 'store'])->name('post.invoices');
Route::put('/invoices_code/{id}', [InvoiceController::class, 'update'])->name('put.invoices');
Route::get('/detail_invoices/{id}', [InvoiceController::class, 'detail'])->name('detail.invoices');

Route::get('/inquiry', [InquiryController::class, 'index'])->name('get.inquiry');
Route::post('/inquiry_code', [InquiryController::class, 'store'])->name('store.inquiry');

Route::get('/products', [ProductController::class, 'index'])->name('get.product');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('show.product');
Route::get('/products/search', [ProductController::class, 'search'])->name('search.product');
Route::post('/products_code', [ProductController::class, 'store'])->name('post.products');
Route::put('/products_code/{id}', [ProductController::class, 'update'])->name('update.products');

Route::get('/payment_types', [PaymentTypeController::class, 'index'])->name('get.payment_type');
Route::post('/payment_types_code', [PaymentTypeController::class, 'store'])->name('post.payment_type');

Route::get('/purchase_orders', [PurchaseOrderController::class, 'index'])->name('get.purchase_order');
Route::get('/purchase_orders/monthly', [PurchaseOrderController::class, 'monthlyPurchase'])->name('monthly.purchase_order');
Route::get('/purchase_orders/{id}', [PurchaseOrderController::class, 'show'])->name('show.purchase_order');
Route::post('/purchase_orders/approve/{id}', [PurchaseOrderController::class, 'approved'])->name('approve.purchase_order');
Route::post('/purchase_orders_code', [PurchaseOrderController::class, 'store'])->name('post.purchase_order');
Route::put('/purchase_orders_code/{id}', [PurchaseOrderController::class, 'update'])->name('put.purchase_order');
Route::post('/purchase_orders/good-receive', [PurchaseOrderController::class, 'goodReceive'])->name('show.purchase_orderReceive');
Route::delete('/purchase_orders_delete/{id}', [PurchaseOrderController::class, 'destroy'])->name('delete.purchase_order');

Route::get('/quatations', [QuatationController::class, 'index'])->name('get.quatation');
Route::get('/quatations/monthly', [QuatationController::class, 'monthlyQuo'])->name('get.quatationMonthly');
Route::get('/quatations/{id}', [QuatationController::class, 'show'])->name('get.quatationById');
Route::post('/quatations_code', [QuatationController::class, 'store'])->name('post.quatation');
Route::put('/quatations_code/{id}', [QuatationController::class, 'put'])->name('put.quatation');
Route::delete('/quatations_delete/{id}', [QuatationController::class, 'destroy'])->name('delete.quatation');

Route::get('/vendors', [VendorController::class, 'index'])->name('get.vendor');
Route::get('/vendors/{id}', [VendorController::class, 'show'])->name('show.vendor');
Route::post('/vendors_code', [VendorController::class, 'store'])->name('post.vendor');
Route::put('/vendors_code/{id}', [VendorController::class, 'update'])->name('put.vendor');

Route::get('/opex', [OpexController::class, 'index'])->name('get.opex');
Route::post('/opex_code', [OpexController::class, 'store'])->name('post.opex');

Route::get('/sales_orders', [SalesOrderController::class, 'index'])->name('get.sales_order');
Route::get('/sales_orders/monthly', [SalesOrderController::class, 'monthlySales']);
Route::get('/sales_orders/{id}', [SalesOrderController::class, 'show'])->name('show.sales_order');
Route::post('/sales_orders_code', [SalesOrderController::class, 'store'])->name('post.sales_order');
Route::put('/sales_orders_code/{id}', [SalesOrderController::class, 'update'])->name('put.sales_order');
Route::delete('/sales_orders_delete/{id}', [SalesOrderController::class, 'destroy'])->name('delete.sales_order');
//q
//r
//s
//t
Route::get('/tandater', [TandaTerimaController::class, 'index'])->name('get.tandater');
Route::get('/tandater/{id}', [TandaTerimaController::class, 'show'])->name('get.tandaterById');
Route::get('/detail_tandater/{id}', [TandaTerimaController::class, 'detail'])->name('detail.tandater');
Route::post('/addTandater', [TandaTerimaController::class, 'store'])->name('post.tandater');
Route::put('/addTandater/{id}', [TandaTerimaController::class, 'update'])->name('put.tandater');


Route::get('/faktur-pajak', [FakturPajakController::class, 'index'])->name('get.fakturpajak');
Route::post('/faktur-pajak-code', [FakturPajakController::class, 'store'])->name('post.fakturpajak');

//u
//v
//w
//x
//y
Route::get('/laporan_keuangan', [DetailPoController::class, 'laporan'])->name('get.laporan');
Route::get('/report_management', [ReportController::class, 'getReport'])->name('get.report');

