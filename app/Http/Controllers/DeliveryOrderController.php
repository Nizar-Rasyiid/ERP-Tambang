<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryOrder;
use App\Models\DetailDo;

class DeliveryOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deliveryOrder = DeliveryOrder::with(['customer','employee','salesorder'])->get();
        return response()->json($deliveryOrder);
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
        $lastIdDo = $lastDo ? $lastDo->code_do : 1000;
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
        $deliveryOrder = DeliveryOrder::with(['customer','employee'])->find($id);
        return response()->json($deliveryOrder);
    }

    public function SoShow(string $id)
    {
        $salesOrder = DeliveryOrder::with('customer', 'employee')->where('id_so', $id)->get();
        return response()->json($salesOrder);
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
