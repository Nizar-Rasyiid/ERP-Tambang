<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\SalesOrder;
use App\Models\Quatation;

class ReportController extends Controller
{
    public function getReport()
    {
        // $report = DB::table('detailinvoices')
        //     ->leftJoin('invoices', 'detailinvoices.id_invoice', '=', 'invoices.id_invoice')
        //     ->leftJoin('salesorders', 'detailinvoices.id_so', '=', 'salesorders.id_so')            
        //     ->leftJoin('customers', 'salesorders.customer_id', '=', 'customers.customer_id')
        //     ->leftJoin('deliveryorders', 'detailinvoices.id_do', '=', 'deliveryorders.id_do')
        //     ->join('products', 'detailinvoices.product_id', '=', 'products.product_id')
        //     ->join('detailso', 'detailinvoices.id_detail_so', '=', 'detailso.id_detail_so')
        //     ->join('detail_do', 'detailinvoices.id_detail_do', '=', 'detail_do.id_detail_do')
        //     ->join('detailpo', 'detailinvoices.id_detail_po', '=', 'detailpo.id_detail_po')
        //     ->leftJoin('purchaseorders', 'detailpo.id_po', '=', 'purchaseorders.id_po')
        //     ->select([
        //         'salesorders.po_number',
        //         'customers.customer_code',
        //         'customers.customer_name',
        //         'products.product_sn',
        //         'products.product_desc',
        //         'products.product_brand',
        //         'detailpo.quantity as quantity_po',
        //         'detailpo.price as price_po',
        //         'detailpo.amount as amount_po',
        //         'purchaseorders.deposit as po_deposit',   
        //         'salesorders.code_so',
        //         'salesorders.issue_at as so_date',
        //         'detailso.quantity as quantity_so',
        //         'detailso.price as price_so',
        //         'detailso.amount as amount_so',
        //         'deliveryorders.code_do',
        //         'deliveryorders.issue_at as do_date',
        //         'detail_do.quantity as quantity_do',
        //         'detail_do.price as price_do',
        //         'invoices.code_invoice',
        //         'invoices.issue_at as invoice_date',
        //         'invoices.grand_total as amount_invoice',
        //         'invoices.deposit as invoice_deposit',            
        //     ])
        //     ->groupBy(
        //     'salesorders.po_number',
        //     'customers.customer_code',
        //     'customers.customer_name',
        //     'products.product_sn',
        //     'products.product_desc',
        //     'detailpo.quantity',
        //     'detailpo.price',
        //     'detailpo.amount',            
        //     'salesorders.code_so',
        //     'salesorders.issue_at',
        //     'detailso.quantity',
        //     'detailso.price',
        //     'detailso.amount',
        //     'deliveryorders.code_do',
        //     'deliveryorders.issue_at',
        //     'detail_do.quantity',
        //     'detail_do.price',
        //     'invoices.code_invoice',
        //     'invoices.issue_at',
        //     'invoices.grand_total',                        
        // )        
        // ->get(); 

        // return response()->json($report);
        $report = DB::table('detailinvoices')
            ->leftJoin('invoices', 'detailinvoices.id_invoice', '=', 'invoices.id_invoice')
            ->leftJoin('salesorders', 'detailinvoices.id_so', '=', 'salesorders.id_so')
            ->leftJoin('fakturpajak', 'detailinvoices.id_so', '=', 'fakturpajak.id_so')
            ->leftJoin('detailtandater', 'detailinvoices.id_so', '=', 'detailtandater.id_so')
            ->leftJoin('tandaterima', 'detailtandater.id_tandater', '=', 'tandaterima.id_tandater')
            ->leftJoin('customers', 'salesorders.customer_id', '=', 'customers.customer_id')
            ->leftJoin('deliveryorders', 'detailinvoices.id_do', '=', 'deliveryorders.id_do')
            ->join('products', 'detailinvoices.product_id', '=', 'products.product_id')
            ->join('detailso', 'detailinvoices.id_detail_so', '=', 'detailso.id_detail_so')
            ->join('detail_do', 'detailinvoices.id_detail_do', '=', 'detail_do.id_detail_do')
            ->join('detailpo', 'detailinvoices.id_detail_po', '=', 'detailpo.id_detail_po')
            ->leftJoin('purchaseorders', 'detailpo.id_po', '=', 'purchaseorders.id_po')
            ->select([
                'salesorders.po_number',
                'customers.customer_code',
                'customers.customer_name',
                'products.product_sn',
                'products.product_desc',
                'products.product_brand',
                'detailpo.quantity as quantity_po',
                'detailpo.price as price_po',
                'detailpo.amount as amount_po',
                'purchaseorders.deposit as po_deposit',   
                'salesorders.code_so',
                'salesorders.issue_at as so_date',
                'detailso.quantity as quantity_so',
                'detailso.price as price_so',
                'detailso.amount as amount_so',
                'deliveryorders.code_do',
                'deliveryorders.issue_at as do_date',
                'detail_do.quantity as quantity_do',
                'detail_do.price as price_do',
                'invoices.code_invoice',
                'invoices.issue_at as invoice_date',
                'invoices.grand_total as amount_invoice',
                'invoices.deposit as invoice_deposit',
                DB::raw('((detailso.quantity * detailso.price) - (detailpo.quantity * detailpo.price)) as gross_profit'),
                DB::raw('ROUND(COALESCE(((detailso.quantity * detailso.price) - (detailpo.quantity * detailpo.price)) / NULLIF(invoices.grand_total, 0) * 100, 0), 2) as percen'),                  
                'fakturpajak.code_faktur_pajak as code_pajak',
                'fakturpajak.created_at as pajak_date',
                'tandaterima.resi',
                'detailtandater.issue_at as tandater_date',
            ])
            ->groupBy(
            'salesorders.po_number',
            'customers.customer_code',
            'customers.customer_name',
            'products.product_sn',
            'products.product_desc',
            'products.product_brand',
            'detailpo.quantity',
            'detailpo.price',
            'detailpo.amount',            
            'salesorders.code_so',
            'salesorders.issue_at',
            'detailso.quantity',
            'detailso.price',
            'detailso.amount',
            'deliveryorders.code_do',
            'deliveryorders.issue_at',
            'detail_do.quantity',
            'detail_do.price',
            'invoices.code_invoice',
            'invoices.issue_at',
            'invoices.grand_total', 
            'invoices.deposit',           
            'fakturpajak.code_faktur_pajak',
            'fakturpajak.created_at',
            'tandaterima.resi',
            'detailtandater.issue_at'
        )        
        ->get(); 

        return response()->json($report);
    }

    public function getSales()
    {
        $report = DB::table('detailinvoices')
            ->leftJoin('invoices', 'detailinvoices.id_invoice', '=', 'invoices.id_invoice')            
            ->leftJoin('salesorders', 'detailinvoices.id_so', '=', 'salesorders.id_so')            
            ->leftJoin('detailtandater', 'detailinvoices.id_so', '=', 'detailtandater.id_so')
            ->leftJoin('tandaterima', 'detailtandater.id_tandater', '=', 'tandaterima.id_tandater')
            ->leftJoin('customers', 'salesorders.customer_id', '=', 'customers.customer_id')
            ->leftJoin('deliveryorders', 'detailinvoices.id_do', '=', 'deliveryorders.id_do')
            ->join('products', 'detailinvoices.product_id', '=', 'products.product_id')
            ->join('detailso', 'detailinvoices.id_detail_so', '=', 'detailso.id_detail_so')
            ->join('detail_do', 'detailinvoices.id_detail_do', '=', 'detail_do.id_detail_do')            
            ->select([
                'salesorders.po_number',
                'customers.customer_code',
                'customers.customer_name',
                'products.product_sn',
                'products.product_desc',                
                'products.product_brand',                
                'salesorders.code_so',
                'salesorders.issue_at as so_date',
                'detailso.quantity as quantity_so',
                'detailso.price as price_so',
                'detailso.amount as amount_so',
                'deliveryorders.code_do',
                'deliveryorders.issue_at as do_date',
                'detail_do.quantity as quantity_do',
                'detail_do.price as price_do',
                'invoices.code_invoice',
                'invoices.issue_at as invoice_date',
                'invoices.grand_total as amount_invoice',
                'invoices.deposit as invoice_deposit',   
                'tandaterima.resi',
                'detailtandater.issue_at as tandater_date',                     
            ])
            ->groupBy(
            'salesorders.po_number',
            'customers.customer_code',
            'customers.customer_name',
            'products.product_sn',
            'products.product_desc',                        
            'products.product_brand',                        
            'salesorders.code_so',
            'salesorders.issue_at',
            'detailso.quantity',
            'detailso.price',
            'detailso.amount',
            'deliveryorders.code_do',
            'deliveryorders.issue_at',
            'detail_do.quantity',
            'detail_do.price',
            'invoices.code_invoice',
            'invoices.issue_at',
            'invoices.grand_total', 
            'invoices.deposit',
            'tandaterima.resi',
            'detailtandater.issue_at'                       
        )        
        ->get(); 

        return response()->json($report);        
    }

    public function reportSales()
    {
        // Invoice
        $invoice_count = Invoice::count();
        $invoice_total = Invoice::sum('grand_total');

        // Quotation
        $quotation_count = Quatation::count();
        $quotation_total = Quatation::sum('grand_total');

        // Sales Order
        $salesorder_count = SalesOrder::count();
        $salesorder_total = SalesOrder::sum('grand_total');        
        

        return response()->json([
            'invoice' => [
                'count' => $invoice_count,
                'total' => $invoice_total,
            ],
            'quotation' => [
                'count' => $quotation_count,
                'total' => $quotation_total,
            ],
            'salesorder' => [
                'count' => $salesorder_count,
                'total' => $salesorder_total,
            ],            
        ]);
    }
}
