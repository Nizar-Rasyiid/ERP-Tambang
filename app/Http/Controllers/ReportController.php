<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getReport()
    {
        $query = DB::table('detail_do')
        ->join('deliveryorders', 'detail_do.id_do', '=', 'deliveryorders.id_do')
        ->join('customers', 'deliveryorders.customer_id', '=', 'customers.customer_id')
        ->join('products', 'detail_do.product_id', '=', 'products.product_id')
        ->leftJoin('salesorders', 'deliveryorders.id_so', '=', 'salesorders.id_so')
        ->leftJoin('detailso', 'salesorders.id_so', '=', 'detailso.id_so')
        ->leftJoin('invoices', 'salesorders.id_so', '=', 'invoices.id_so')
        ->leftJoin('fakturpajak', 'salesorders.id_so', '=', 'fakturpajak.id_so')
        ->leftJoin('tandaterima', 'salesorders.id_so', '=', 'tandaterima.id_so')
        ->leftJoin('purchaseorders', 'detail_do.id_po', '=', 'purchaseorders.id_po')
        ->leftJoin('detailpo', 'detail_do.id_detail_po', '=', 'detailpo.id_detail_po')
        ->select([
            'salesorders.po_number as po_number',
            'customers.customer_code',
            'customers.customer_name',
            'products.product_sn',
            'product_desc',
            'detailso.quantity as quantity_so',
            'detailso.price as price_so',
            'salesorders.sub_total as amount_so',
            'salesorders.code_so',
            'salesorders.issue_at as so_date',
            'detailpo.quantity as quantity_po',
            'detailpo.price as price_po',
            'purchaseorders.sub_total as amount_po',
            'products.product_brand as brand',
            'deliveryorders.code_do',
            'deliveryorders.issue_at as do_date',
            DB::raw('detailpo.quantity - detailso.quantity as outstanding_supply'),            
            'invoices.code_invoice',
            'invoices.issue_at as invoice_date',
            'invoices.grand_total',
            DB::raw('COALESCE(SUM((detailso.quantity * detailso.price) - (detailpo.quantity * detailpo.price)), 0) as gross_profit'),               
            DB::raw('ROUND(COALESCE(((detailso.quantity * detailso.price) - (detailpo.quantity * detailpo.price)) / NULLIF(invoices.grand_total, 0) * 100, 0), 2) as percen'),
            'fakturpajak.code_faktur_pajak',
            'fakturpajak.created_at as fp_date',
            'tandaterima.code_tandater',
            'tandaterima.issue_at',
            'tandaterima.resi',            
        ])
        ->get();
        
        return response()->json($query);
    }

    public function laporanKeuangan(){
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
        'salesorders.po_number as po_number',
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
    ->orderBy('detailso.id_detail_so', 'desc')
    ->get();
        return response()->json($query);
    }
}
