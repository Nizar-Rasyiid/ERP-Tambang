<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\DeliveryOrder;
use App\Models\SalesOrder;
use App\Models\DetailDO;
use App\Models\DetailInvoice;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoice = Invoice::with(
            [
                'customer',                  
                'salesorder',
                'detailInv',
                'detailInv.product'
            ])->get();
        return response()->json($invoice);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'delivery_order_details' => 'required|array',
            'due_at' => 'required|string',            
        ]);
    
        // Ambil bulan & tahun saat ini
        $currentMonth = date('m'); // 02
        $currentYear  = date('Y'); // 2025
        $monthRoman   = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV',
            '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII',
            '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ];
    
        // Cari invoice terakhir dalam bulan dan tahun yang sama
        $lastInvoice = Invoice::whereYear('created_at', $currentYear)
                              ->whereMonth('created_at', $currentMonth)
                              ->latest('id_invoice')
                              ->first();
    
        // Ambil ID terakhir & buat ID baru dengan format 2 digit
        $lastIdInvoice = $lastInvoice ? intval(explode('/', $lastInvoice->code_invoice)[0]) : 0;
        $newIdInvoice  = str_pad($lastIdInvoice + 1, 2, '0', STR_PAD_LEFT); // Format 2 digit (00, 01, 02, ...)
    
        // Format kode Invoice: 00ID/INV/II/2025
        $formattedCodeInvoice = "{$newIdInvoice}/INV/{$monthRoman[$currentMonth]}/{$currentYear}";
    
        // Buat Invoice
        $invoice = Invoice::create([
            'id_so'           => $request->id_so,
            'customer_id'     => $request->customer_id,
            'employee_id'     => $request->employee_id,
            'code_invoice'    => $formattedCodeInvoice,                
            'issue_at'        => $request->issue_at,
            'due_at'          => $request->due_at,            
        ]);

        $detailDo = DetailDo::findOrFail($request->id_so)->get();
        $sub_total = 0;
    
        foreach ($request->delivery_order_details as $pro) {
            $line_total = $pro['price'] * $pro['quantity'];
            $sub_total += $line_total;
    
            DetailInvoice::create([
                'id_invoice'    => $invoice->id_invoice,
                'id_do'         => $pro['id_do'],
                'product_id'    => $pro['product_id'],
                'quantity'      => $pro['quantity'],
                'price'         => $pro['price'],
                'amount'        => $pro['amount'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            DeliveryOrder::findOrFail($pro['id_do'])->update(['has_inv' => 1]);
        }        
        $allDeliveryOrders = DeliveryOrder::where('id_so', $request->id_so)->get();
        
        $allHasInvoice = true;

        foreach ($allDeliveryOrders as $do) {
            if ($do->has_inv != 1) {
                $allHasInvoice = false;
                break;
            }
        }
        
        if ($allHasInvoice) {
            SalesOrder::findOrFail($request->id_so)->update(['has_invoice' => 1]);
        }
        
    
        // Update sub_total pada Invoice
        $invoice->update(['sub_total' => $sub_total]);
    
        return response()->json([
            'message'  => 'Invoice berhasil dibuat!',
            'invoice'  => $invoice,            
        ], 201);
    }

    public function detail( string $id){
        $detail = DetailInvoice::with([
            'product', 
            'do', 
            'invoice'
            ])
            ->where('id_invoice', $id)->get();

        return response()->json($detail);
    }

    public function approved(Request $request) {
        $approved = Invoice::findOrFail($request->id_invoice)->update([
            'approved' => 1,
        ]);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::with([
            'customer', 
            'employee', 
            'salesorder',
            'salesorder.detailSo',            
            ])
            ->where('id_invoice', $id)
            ->get();
        return response()->json($invoice);
    }
    
    public function InvoiceSo(string $id)
    {
        $invoice = Invoice::with([
            'customer', 
            'employee', 
            'salesorder',
            'salesorder.detailSo',            
            ])
            ->where('id_so', $id)
            ->get();
        return response()->json($invoice);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id)->update([
            'id_so'           => $request->id_so,
            'customer_id'     => $request->customer_id,
            'employee_id'     => $request->employee_id,
            'code_invoice'    => $request->code_invoice,                
            'issue_at'        => $request->issue_at,
            'due_at'          => $request->due_at,
            'sub_total'       => $request->sub_total,
        ]);
        foreach ($request->delivery_order_details as $pro) {
            DetailInvoice::findOrFail($pro['id_detail_invoice'])->update([                
                'id_do'         => $pro['id_do'],
                'product_id'    => $pro['product_id'],
                'quantity'      => $pro['quantity'],
                'price'         => $pro['price'],
                'amount'        => $pro['amount'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            DeliveryOrder::findOrFail($pro['id_do'])->update(['has_inv' => 1]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
