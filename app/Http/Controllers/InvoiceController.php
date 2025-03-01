<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoice = Invoice::all();
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
        ]);
        $lastDo = DeliveryOrder::latest()->first();
        $lastIdDo = $lastDo ? $lastDo->code_do : 0;
        $newIdDo = $lastIdDo + 1;        
    
        // 1️⃣ Buat Delivery Order (DO)
        $deliveryOrder = DeliveryOrder::create([
            'customer_id'     => $request->customer_id,
            'employee_id'     => $request->employee_id,
            'id_so'           => $request->id_so,
            'code_do'         => $newIdDo, 
            'sub_total'       => 0,          
            'issue_at'        => $request->issue_at,
            'due_at'          => $request->due_at,
        ]);     
        
        $product = [];
        $sub_total = 0;

        foreach ($request->delivery_order_details as $key => $pro) {
            $line_total = $pro['price'] * $pro['quantity'];
            $sub_total += $line_total;
            
            $detailso = DetailDo::create([
                'id_do'         => $deliveryOrder->id_do,
                'product_id'    => $pro['product_id'],
                'quantity'      => $pro['quantity'],
                'price'         => $pro['price'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        $Do = DeliveryOrder::where('id_do', $deliveryOrder->id_do)->update([
            'sub_total'     => $sub_total,
        ]);
    
        return response()->json([
            'message'       => 'Delivery Order dan Invoice berhasil dibuat atau diperbarui!',
            'delivery_order'=> $deliveryOrder,            
        ], 201);
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
