<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryOrder;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\PurchaseOrder;

class DeliveryOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deliveryOrders = DeliveryOrder::all();
        return response()->json($deliveryOrders);
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
        $lastDo = DeliveryOrder::latest()->first();
        $lastIdDo = $lastDo ? $lastDo->code_do : 0;
        $newIdDo = $lastIdDo + 1;        
    
        // 1️⃣ Buat Delivery Order (DO)
        $deliveryOrder = DeliveryOrder::create([
            'customer_id'     => $request->id_customer,
            'employee_id'     => $request->id_employee,            
            'id_po'           => $request->id_po,
            'code_do'         => $code_do,
            'issue_at'        => $request->issue_at,
            'due_at'          => $request->due_at,
        ]);        
    
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
        $deliveryOrder = DeliveryOrder::findOrFail($id);
        return response()->json($deliveryOrder);
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
        $request->validate([
            'id_customer'     => 'required|exists:customers,id_customer',
            'id_employee'     => 'required|exists:employees,id_employee',
            'id_bank_account' => 'required|exists:bank_accounts,id_bank_account',
            'id_po'           => 'required|exists:purchaseorders,id_po',
            'issued_at'       => 'required|date',
            'due_at'          => 'required|date',
        ]);

        $deliveryOrder = DeliveryOrder::findOrFail($id);
        $deliveryOrder->update($request->all());

        return response()->json([
            'message'       => 'Delivery Order berhasil diperbarui!',
            'delivery_order'=> $deliveryOrder
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deliveryOrder = DeliveryOrder::findOrFail($id);
        $deliveryOrder->delete();

        return response()->json([
            'message' => 'Delivery Order berhasil dihapus!'
        ]);
    }
}
