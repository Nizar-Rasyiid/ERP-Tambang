<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getReport()
    {
//         $query = DB::select("
//          SELECT 
//     dpo.id_detail_po AS 'no',
//     po.code_po AS 'po_number',
//     po.issue_at AS 'po_date',
//     c.customer_code AS 'cust_code',
//     c.customer_name AS 'cust_name', 
//     p.product_sn AS 'pn',
//     p.product_desc AS 'desc',
//     dpo.quantity AS 'qty_po',
//     dpo.price AS 'price_po',
//     ROUND
//     (dpo.quantity * dpo.price) AS 'amount_po',
//     p.product_brand AS 'product_brand',

//     -- Ambil SO pertama
//     COALESCE(so.code_so, '-') AS 'sales_order',
//     COALESCE(so.issue_at, '-') AS 'so_date',
//     COALESCE(dso.quantity, 0) AS 'qty_so',
//     COALESCE(dso.price, 0) AS 'price_so',
//     COALESCE((dso.quantity * dso.price), 0) AS 'amount_so',

//     -- Ambil DO pertama
//     COALESCE(do_.code_do, '-') AS 'delivery_order',
//     COALESCE(do_.issue_at, '-') AS 'do_date',
//     COALESCE(dd.quantity, 0) AS 'qty_do',

//     -- Ambil Invoice pertama
//     COALESCE(inv.code_invoice, '-') AS 'bill_no',
//     COALESCE(inv.issue_at, '-') AS 'billing_date',
//     COALESCE(inv.grand_total, 0) AS 'amount_invoice',

//     -- Perhitungan Gross Profit
//     COALESCE((inv.grand_total - (dpo.quantity * dpo.price)), 0) AS 'gross_profit',
//     ROUND(COALESCE(((inv.grand_total - (dpo.quantity * dpo.price)) / NULLIF(inv.grand_total, 0) * 100), 0), 2) AS 'gp_percentage',

//     -- Faktur Pajak & Tanda Terima
//         COALESCE(fp.code_faktur_pajak, '-') AS 'faktur_pajak',
//         COALESCE(fp.created_at, '-') AS 'faktur_date',
//         COALESCE(tt.code_tandater, '-') AS 'tanda_terima_invoice',
//         COALESCE(tt.issue_at, '-') AS 'tanda_terima_invoice_date'

// FROM detailpo dpo

// -- Hubungkan ke Purchase Order
// LEFT JOIN purchaseorders po ON dpo.id_po = po.id_po

// -- Hubungkan ke Produk
// LEFT JOIN products p ON dpo.product_id = p.product_id

// -- Sales Order: Ambil hanya SO pertama
// LEFT JOIN (
//    SELECT 
//     dso.product_id,
//     MAX(so.id_so) AS id_so,
//     MAX(so.code_so) AS code_so,
//     MAX(so.issue_at) AS issue_at,
//     MAX(so.customer_id) AS customer_id
// FROM salesorders so 
// JOIN detailso dso ON so.id_so = dso.id_so
// GROUP BY dso.product_id
// ) so ON dpo.product_id = so.product_id


// -- Ambil detail SO
// LEFT JOIN detailso dso ON so.id_so = dso.id_so AND dpo.product_id = dso.product_id

// -- Customer lewat SO
// LEFT JOIN customers c ON so.customer_id = c.customer_id

// -- Delivery Order: Ambil DO pertama
// LEFT JOIN (
//     SELECT do_.id_do, do_.id_so, do_.code_do, do_.issue_at
//     FROM deliveryorders do_
//     GROUP BY do_.id_so
// ) do_ ON so.id_so = do_.id_so

// LEFT JOIN detail_do dd ON do_.id_do = dd.id_do AND dpo.product_id = dd.product_id

// -- Invoice: Ambil invoice pertama
// LEFT JOIN (
//     SELECT inv.id_invoice, inv.id_so, inv.code_invoice, inv.issue_at, inv.grand_total
//     FROM invoices inv
//     GROUP BY inv.id_so
// ) inv ON so.id_so = inv.id_so

// -- Faktur Pajak
// LEFT JOIN (
//     SELECT fp.id_invoice, fp.code_faktur_pajak, fp.created_at
//     FROM fakturpajak fp
//     GROUP BY fp.id_invoice
// ) fp ON inv.id_invoice = fp.id_invoice

// -- Tanda Terima
// LEFT JOIN (
//     SELECT tt.id_so, tt.code_tandater, tt.issue_at
//     FROM tandaterima tt
//     GROUP BY tt.id_so
// ) tt ON so.id_so = tt.id_so

// -- Biar hasil lebih rapi
// GROUP BY dpo.id_detail_po;
// ");

$query = DB::table('purchaseorders')
    ->join('vendors', 'purchaseorders.vendor_id', '=', 'vendors.vendor_id')
    ->join('detailpo', 'purchaseorders.id_po', '=', 'detailpo.id_po')
    ->join('products', 'detailpo.product_id', '=', 'products.product_id')
    ->leftJoin('detailso', 'detailpo.product_id', '=', 'detailso.product_id')
    ->leftJoin('salesorders', 'detailso.id_so', '=', 'salesorders.id_so')
    ->leftJoin('customers', 'salesorders.customer_id', '=', 'customers.customer_id')
    ->leftJoin('deliveryorders', 'salesorders.id_so', '=', 'deliveryorders.id_so')
    ->leftJoin('detail_do', function($join) {
        $join->on('deliveryorders.id_do', '=', 'detail_do.id_do')
             ->on('detailso.product_id', '=', 'detail_do.product_id');
    })
    ->leftJoin('invoices', 'salesorders.id_so', '=', 'invoices.id_so')
    ->leftJoin('fakturpajak as fp', 'invoices.id_invoice', '=', 'fp.id_invoice')
    ->leftJoin('tandaterima', 'salesorders.id_so', '=', 'tandaterima.id_so')

    ->select([
        'detailpo.id_detail_po as no',
        'purchaseorders.code_po as po_number',
        'purchaseorders.issue_at as po_date',
        'customers.customer_name as cust_name',
        'customers.customer_code as cust_code',
        'vendors.vendor_name as vendor',
        'products.product_sn as pn',
        'products.product_desc as desc',
        'detailpo.quantity as qty_po',
        'detailpo.price as price_po',
        DB::raw('(detailpo.quantity * detailpo.price) as amount_po'),
        'products.product_brand as product_brand',

        // Sales Order
        DB::raw('COALESCE(salesorders.code_so, "-") as sales_order'),
        DB::raw('COALESCE(salesorders.issue_at, "-") as so_date'),
        DB::raw('COALESCE(detailso.quantity, 0) as qty_so'),
        DB::raw('COALESCE(detailso.price, 0) as price_so'),
        DB::raw('COALESCE((detailso.quantity * detailso.price), 0) as amount_so'),

        // Delivery Order
        DB::raw('COALESCE(deliveryorders.code_do, "-") as delivery_order'),
        DB::raw('COALESCE(deliveryorders.issue_at, "-") as do_date'),
        DB::raw('COALESCE(detail_do.quantity, 0) as qty_do'),

        // Invoice dan Gross Profit
        DB::raw('COALESCE(invoices.code_invoice, "-") as bill_no'),
        DB::raw('COALESCE(invoices.issue_at, "-") as billing_date'),
        DB::raw('COALESCE(invoices.grand_total, 0) as amount_invoice'),
        DB::raw('COALESCE((invoices.grand_total - (detailpo.quantity * detailpo.price)), 0) as gross_profit'),
        DB::raw('ROUND(COALESCE(((invoices.grand_total - (detailpo.quantity * detailpo.price)) / NULLIF(invoices.grand_total, 0) * 100), 0), 2) as gp_percentage'),

        // Faktur Pajak & Tanda Terima
        DB::raw('COALESCE(fp.code_faktur_pajak, "-") as faktur_pajak'),
        DB::raw('COALESCE(fp.created_at, "-") as faktur_date'),
        DB::raw('COALESCE(tandaterima.code_tandater, "-") as tanda_terima_invoice'),
        DB::raw('COALESCE(tandaterima.issue_at, "-") as tanda_terima_invoice_date'),
    
    ])

    ->groupBy([
        'detailpo.id_detail_po',
        'purchaseorders.code_po',
        'purchaseorders.issue_at',
        'vendors.vendor_name',
        'products.product_sn',
        'products.product_desc',
        'detailpo.quantity',
        'detailpo.price',
        'products.product_brand',
        'salesorders.code_so',
        'salesorders.issue_at',
        'detailso.quantity',
        'detailso.price',
        'deliveryorders.code_do',
        'deliveryorders.issue_at',
        'detail_do.quantity',
        'invoices.code_invoice',
        'invoices.issue_at',
        'invoices.grand_total',
        'fp.code_faktur_pajak',
        'fp.created_at',
        'tandaterima.code_tandater',
        'tandaterima.issue_at'
    ])

    ->orderBy('purchaseorders.code_po')
    ->orderBy('salesorders.code_so')
    ->get();

        return response()->json($query);
    }
}
