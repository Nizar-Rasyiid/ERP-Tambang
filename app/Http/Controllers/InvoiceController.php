<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\DetailInvoice;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoice = Invoice::with(['customer', 'employee'])->get();
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
            'customer_id'     => $request->customer_id,
            'employee_id'     => $request->employee_id,
            'code_invoice'    => $formattedCodeInvoice,                
            'issue_at'        => $request->issue_at,
            'due_at'          => $request->due_at,
        ]);
    
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
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    
        // Update sub_total pada Invoice
        $invoice->update(['sub_total' => $sub_total]);
    
        return response()->json([
            'message'  => 'Invoice berhasil dibuat!',
            'invoice'  => $invoice,            
        ], 201);
    }

    public function detail( string $id){
        $detail = DetailInvoice::with('product')->where('id_invoice', $id)->get();

        return response()->json($detail);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
