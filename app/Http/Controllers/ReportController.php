<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getReport()
    {
        //Ini yang dari purchaseorder
// $query = DB::table('purchaseorders')
//     ->join('vendors', 'purchaseorders.vendor_id', '=', 'vendors.vendor_id')
//     ->join('detailpo', 'purchaseorders.id_po', '=', 'detailpo.id_po')
//     ->join('products', 'detailpo.product_id', '=', 'products.product_id')
//     ->leftJoin('detailso', 'detailpo.product_id', '=', 'detailso.product_id')
//     ->leftJoin('salesorders', 'detailso.id_so', '=', 'salesorders.id_so')
//     ->leftJoin('customers', 'salesorders.customer_id', '=', 'customers.customer_id')
//     ->leftJoin('deliveryorders', 'salesorders.id_so', '=', 'deliveryorders.id_so')
//     ->leftJoin('detail_do', function($join) {
//         $join->on('deliveryorders.id_do', '=', 'detail_do.id_do')
//              ->on('detailso.product_id', '=', 'detail_do.product_id');
//     })
//     ->leftJoin('invoices', 'salesorders.id_so', '=', 'invoices.id_so')
//     ->leftJoin('fakturpajak as fp', 'invoices.id_invoice', '=', 'fp.id_invoice')
//     ->leftJoin('tandaterima', 'salesorders.id_so', '=', 'tandaterima.id_so')

//     ->select([
//         'detailpo.id_detail_po as no',
//         'purchaseorders.code_po as po_number',
//         'purchaseorders.issue_at as po_date',
//         'customers.customer_name as cust_name',
//         'customers.customer_code as cust_code',
//         'vendors.vendor_name as vendor',
//         'products.product_sn as pn',
//         'products.product_desc as desc',
//         'detailpo.quantity as qty_po',
//         'detailpo.price as price_po',
//         DB::raw('(detailpo.quantity * detailpo.price) as amount_po'),
//         'products.product_brand as product_brand',

//         // Sales Order
//         DB::raw('COALESCE(salesorders.code_so, "-") as sales_order'),
//         DB::raw('COALESCE(salesorders.po_number, "-") as po_code'),
//         DB::raw('COALESCE(salesorders.issue_at, "-") as so_date'),
//         DB::raw('COALESCE(detailso.quantity, 0) as qty_so'),
//         DB::raw('COALESCE(detailso.price, 0) as price_so'),
//         DB::raw('COALESCE((detailso.quantity * detailso.price), 0) as amount_so'),

//         // Delivery Order
//         DB::raw('COALESCE(deliveryorders.code_do, "-") as delivery_order'),
//         DB::raw('COALESCE(deliveryorders.issue_at, "-") as do_date'),
//         DB::raw('COALESCE(detail_do.quantity, 0) as qty_do'),

//         // Invoice dan Gross Profit
//         DB::raw('COALESCE(invoices.code_invoice, "-") as bill_no'),
//         DB::raw('COALESCE(invoices.issue_at, "-") as billing_date'),
//         DB::raw('COALESCE(invoices.grand_total, 0) as amount_invoice'),
//         DB::raw('COALESCE(((detailso.quantity * detailso.price) - (detailpo.quantity * detailpo.price)), 0) as gross_profit'),
//         DB::raw('ROUND(COALESCE((((detailso.quantity * detailso.price) - (detailpo.quantity * detailpo.price)) / NULLIF(invoices.grand_total, 0) * 100), 0), 2) as gp_percentage'),

//         // Faktur Pajak & Tanda Terima
//         DB::raw('COALESCE(fp.code_faktur_pajak, "-") as faktur_pajak'),
//         DB::raw('COALESCE(fp.created_at, "-") as faktur_date'),
//         DB::raw('COALESCE(tandaterima.code_tandater, "-") as tanda_terima_invoice'),
//         DB::raw('COALESCE(tandaterima.issue_at, "-") as tanda_terima_invoice_date'),
    
//     ])

//     ->groupBy([
//         'detailpo.id_detail_po',
//         'purchaseorders.code_po',
//         'purchaseorders.issue_at',
//         'vendors.vendor_name',
//         'products.product_sn',
//         'products.product_desc',
//         'detailpo.quantity',
//         'detailpo.price',
//         'products.product_brand',
//         'salesorders.code_so',
//         'salesorders.issue_at',
//         'detailso.quantity',
//         'detailso.price',
//         'deliveryorders.code_do',
//         'deliveryorders.issue_at',
//         'detail_do.quantity',
//         'invoices.code_invoice',
//         'invoices.issue_at',
//         'invoices.grand_total',
//         'fp.code_faktur_pajak',
//         'fp.created_at',
//         'tandaterima.code_tandater',
//         'tandaterima.issue_at'
//     ])

//     ->orderBy('purchaseorders.code_po')
//     ->orderBy('salesorders.code_so')
//     ->get();
// $query = DB::table('purchaseorders')
//     ->join('vendors', 'purchaseorders.vendor_id', '=', 'vendors.vendor_id')
//     ->join('detailpo', 'purchaseorders.id_po', '=', 'detailpo.id_po')
//     ->join('products', 'detailpo.product_id', '=', 'products.product_id')
//     ->leftJoin('detailso', function ($join) {
//         $join->on('detailpo.product_id', '=', 'detailso.product_id');
//     })
//     ->leftJoin('salesorders', 'detailso.id_so', '=', 'salesorders.id_so')
//     ->leftJoin('customers', 'salesorders.customer_id', '=', 'customers.customer_id')
//     ->leftJoin('deliveryorders', 'salesorders.id_so', '=', 'deliveryorders.id_so')
//     ->leftJoin('detail_do', function ($join) {
//         $join->on('deliveryorders.id_do', '=', 'detail_do.id_do')
//              ->on('detailso.product_id', '=', 'detail_do.product_id')
//              ->on('detailso.id_so', '=', 'deliveryorders.id_so'); // Tambahan untuk spesifisitas
//     })
//     ->leftJoin('invoices', 'salesorders.id_so', '=', 'invoices.id_so')
//     ->leftJoin('fakturpajak as fp', 'invoices.id_invoice', '=', 'fp.id_invoice')
//     ->leftJoin('tandaterima', 'salesorders.id_so', '=', 'tandaterima.id_so')

//     ->select([
//         'detailpo.id_detail_po as no',
//         'purchaseorders.code_po as po_number',
//         'purchaseorders.issue_at as po_date',
//         DB::raw('COALESCE(customers.customer_name, "-") as cust_name'), // Tambahkan COALESCE untuk null
//         DB::raw('COALESCE(customers.customer_code, "-") as cust_code'),
//         'vendors.vendor_name as vendor',
//         'products.product_sn as pn',
//         'products.product_desc as desc',
//         'detailpo.quantity as qty_po',
//         'detailpo.price as price_po',
//         DB::raw('(detailpo.quantity * detailpo.price) as amount_po'),
//         'products.product_brand as product_brand',

//         // Sales Order
//         DB::raw('COALESCE(MAX(salesorders.code_so), "-") as sales_order'), // Gunakan MAX untuk menghindari duplikat
//         DB::raw('COALESCE(MAX(salesorders.po_number), "-") as po_code'),
//         DB::raw('COALESCE(MAX(salesorders.issue_at), "-") as so_date'),
//         DB::raw('COALESCE(SUM(detailso.quantity), 0) as qty_so'), // Gunakan SUM untuk total kuantitas
//         DB::raw('COALESCE(MAX(detailso.price), 0) as price_so'), // Ambil harga terakhir
//         DB::raw('COALESCE(SUM(detailso.quantity * detailso.price), 0) as amount_so'),

//         // Delivery Order
//         DB::raw('COALESCE(MAX(deliveryorders.code_do), "-") as delivery_order'),
//         DB::raw('COALESCE(MAX(deliveryorders.issue_at), "-") as do_date'),
//         DB::raw('COALESCE(SUM(detail_do.quantity), 0) as qty_do'),

//         // Invoice dan Gross Profit
//         DB::raw('COALESCE(MAX(invoices.code_invoice), "-") as bill_no'),
//         DB::raw('COALESCE(MAX(invoices.issue_at), "-") as billing_date'),
//         DB::raw('COALESCE(MAX(invoices.grand_total), 0) as amount_invoice'),
//         DB::raw('COALESCE(SUM((detailso.quantity * detailso.price) - (detailpo.quantity * detailpo.price)), 0) as gross_profit'),
//         DB::raw('ROUND(COALESCE(SUM(((detailso.quantity * detailso.price) - (detailpo.quantity * detailpo.price))) / NULLIF(MAX(invoices.grand_total), 0) * 100, 0), 2) as gp_percentage'),

//         // Faktur Pajak & Tanda Terima
//         DB::raw('COALESCE(MAX(fp.code_faktur_pajak), "-") as faktur_pajak'),
//         DB::raw('COALESCE(MAX(fp.created_at), "-") as faktur_date'),
//         DB::raw('COALESCE(MAX(tandaterima.code_tandater), "-") as tanda_terima_invoice'),
//         DB::raw('COALESCE(MAX(tandaterima.issue_at), "-") as tanda_terima_invoice_date'),
//     ])

//     ->groupBy([
//         'detailpo.id_detail_po',
//         'purchaseorders.code_po',
//         'purchaseorders.issue_at',
//         'vendors.vendor_name',
//         'products.product_sn',
//         'products.product_desc',
//         'detailpo.quantity',
//         'detailpo.price',
//         'products.product_brand',
//         'customers.customer_name', // Tambahkan ke GROUP BY
//         'customers.customer_code', // Tambahkan ke GROUP BY
//     ])

//     ->orderBy('purchaseorders.code_po')
//     ->orderBy('salesorders.code_so')
//     ->get();

//Ini yang dari salesorder
$query = DB::table('salesorders')
    ->join('detailso', 'salesorders.id_so', '=', 'detailso.id_so')
    ->join('customers', 'salesorders.customer_id', '=', 'customers.customer_id')
    ->join('products', 'detailso.product_id', '=', 'products.product_id')
    ->leftJoin('deliveryorders', 'salesorders.id_so', '=', 'deliveryorders.id_so')
    ->leftJoin('detail_do', function ($join) {
        $join->on('deliveryorders.id_do', '=', 'detail_do.id_do')
             ->on('detailso.product_id', '=', 'detail_do.product_id')
             ->on('detailso.id_so', '=', 'deliveryorders.id_so');
    })
    ->leftJoin('invoices', 'salesorders.id_so', '=', 'invoices.id_so')
    ->leftJoin('fakturpajak as fp', 'invoices.id_invoice', '=', 'fp.id_invoice')
    ->leftJoin('tandaterima', 'salesorders.id_so', '=', 'tandaterima.id_so')
    ->leftJoin('detailpo', 'detailso.product_id', '=', 'detailpo.product_id')
    ->leftJoin('purchaseorders', 'detailpo.id_po', '=', 'purchaseorders.id_po')
    ->leftJoin('vendors', 'purchaseorders.vendor_id', '=', 'vendors.vendor_id')

    ->select([
        'detailso.id_detail_so as no',
        'salesorders.code_so as sales_order',
        'salesorders.po_number as po_code',
        'salesorders.issue_at as so_date',
        'customers.customer_name as cust_name',
        'customers.customer_code as cust_code',
        DB::raw('COALESCE(vendors.vendor_name, "-") as vendor'),
        'products.product_sn as pn',
        'products.product_desc as desc',
        'detailso.quantity as qty_so',
        'detailso.price as price_so',
        DB::raw('(detailso.quantity * detailso.price) as amount_so'),
        'products.product_brand as product_brand',

        // Purchase Order
        DB::raw('COALESCE(MAX(purchaseorders.code_po), "-") as po_number'),
        DB::raw('COALESCE(MAX(purchaseorders.issue_at), "-") as po_date'),
        DB::raw('COALESCE(SUM(detailpo.quantity), 0) as qty_po'),
        DB::raw('COALESCE(MAX(detailpo.price), 0) as price_po'),
        DB::raw('COALESCE(SUM(detailpo.quantity * detailpo.price), 0) as amount_po'),

        // Delivery Order
        DB::raw('COALESCE(MAX(deliveryorders.code_do), "-") as delivery_order'),
        DB::raw('COALESCE(MAX(deliveryorders.issue_at), "-") as do_date'),
        DB::raw('COALESCE(SUM(detail_do.quantity), 0) as qty_do'),

        // Invoice dan Gross Profit
        DB::raw('COALESCE(MAX(invoices.code_invoice), "-") as bill_no'),
        DB::raw('COALESCE(MAX(invoices.issue_at), "-") as billing_date'),
        DB::raw('COALESCE(MAX(invoices.grand_total), 0) as amount_invoice'),
        DB::raw('COALESCE(SUM((detailso.quantity * detailso.price) - (detailpo.quantity * detailpo.price)), 0) as gross_profit'),
        DB::raw('ROUND(COALESCE(SUM((detailso.quantity * detailso.price) - (detailpo.quantity * detailpo.price)) / NULLIF(MAX(invoices.grand_total), 0) * 100, 0), 2) as gp_percentage'),

        // Faktur Pajak & Tanda Terima
        DB::raw('COALESCE(MAX(fp.code_faktur_pajak), "-") as faktur_pajak'),
        DB::raw('COALESCE(MAX(fp.created_at), "-") as faktur_date'),
        DB::raw('COALESCE(MAX(tandaterima.code_tandater), "-") as tanda_terima_invoice'),
        DB::raw('COALESCE(MAX(tandaterima.issue_at), "-") as tanda_terima_invoice_date'),
    ])

    ->groupBy([
        'detailso.id_detail_so',
        'salesorders.code_so',
        'salesorders.po_number',
        'salesorders.issue_at',
        'customers.customer_name',
        'customers.customer_code',
        'products.product_sn',
        'products.product_desc',
        'detailso.quantity',
        'detailso.price',
        'products.product_brand',
    ])

    ->orderBy('salesorders.code_so')
    ->orderBy('detailso.id_detail_so')
    ->get();
        return response()->json($query);
    }
}
