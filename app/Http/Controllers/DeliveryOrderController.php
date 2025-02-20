<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeliveryOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'id_customer'     => 'required|exists:customers,id_customer',
            'id_employee'     => 'required|exists:employees,id_employee',
            'id_bank_account' => 'required|exists:bank_accounts,id_bank_account',
            'id_po'           => 'required|exists:purchaseorders,id_po',
            'issued_at'       => 'required|date',
            'due_at'          => 'required|date',
        ]);
    
        // 1️⃣ Buat Delivery Order (DO)
        $deliveryOrder = DeliveryOrder::create([
            'id_customer'     => $request->id_customer,
            'id_employee'     => $request->id_employee,
            'id_bank_account' => $request->id_bank_account,
            'id_po'           => $request->id_po,
            'issued_at'       => $request->issued_at,
            'due_at'          => $request->due_at,
        ]);
    
        // 2️⃣ Cek apakah sudah ada invoice untuk PO ini
        $invoice = Invoice::where('id_po', $request->id_po)->first();
    
        if ($invoice) {
            // 3️⃣ Jika Invoice sudah ada, update id_do
            $invoice->update([
                'id_do' => $deliveryOrder->id_do
            ]);
        } else {
            // 4️⃣ Jika belum ada Invoice, buat baru
            $invoice = Invoice::create([
                'id_do'          => $deliveryOrder->id_do,
                'id_po'          => $request->id_po,
                'id_customer'    => $request->id_customer,
                'id_bank_account'=> $request->id_bank_account,
                'id_payment_type'=> PurchaseOrder::find($request->id_po)->id_payment_type,
                'no_invoice'     => 'INV-' . now()->format('Ymd') . '-' . $deliveryOrder->id_do,
            ]);
        }
    
        return response()->json([
            'message'       => 'Delivery Order dan Invoice berhasil dibuat atau diperbarui!',
            'delivery_order'=> $deliveryOrder,
            'invoice'       => $invoice
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
