<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\DetailSO;

class SalesOrderController extends Controller
{
    public function index()
    {
        $salesOrders = SalesOrder::with(['customer', 'employee'])->get();
        return response()->json($salesOrders);
    }

    public function show($id)
    {
        $salesOrder = SalesOrder::with(['customer', 'employee'])->find($id);
        if (is_null($salesOrder)) {
            return response()->json(['message' => 'Sales Order not found'], 404);
        }
        return response()->json($salesOrder);
    }

    public function store(Request $request)
    {        
        $request->validate([
            'sales_order_details' => 'required|array'
        ]);

        $lastSo = SalesOrder::latest()->first();
        $lastIdSo = $lastSo ? $lastSo->code_so : 1001;
        $newIdSo = $lastIdSo + 1;        

        // Buat Sales Order dengan PPN & Grand Total
        $salesOrder = SalesOrder::create([            
            'customer_id'     => $request->customer_id,
            'employee_id'     => $request->employee_id,
            'code_so'         => $newIdSo,
            'termin'          => $request->termin, 
            'payment_type'    => $request->payment_type,           
            'total_tax'       => $request->total_tax,
            'status_payment'  => $request->status_payment,
            'sub_total'       => 0,            
            'total_service'   => $request->total_service,
            'deposit'         => $request->deposit,
            'ppn'             => 0, // ✅ PPN otomatis dihitung
            'grand_total'     => 0, // ✅ Grand Total otomatis dihitung
            'issue_at'        => $request->issue_at,
            'due_at'          => $request->due_at,
        ]);         

        // variable total_biaya from product_price
        $product = [];
        $sub_total = 0;

        foreach($request->sales_order_details as $key => $pro){  
            $line_total = $pro['price'] * $pro['quantity'];

            $sub_total += $line_total;

            $detailso = DetailSO::create([
            'id_so' => $salesOrder->id_so,                
            'product_id' => $pro['product_id'],
            'quantity' => $pro['quantity'],
            'price' => $pro['price'],
            'created_at' => now(),
            'updated_at' => now(),
            ]);
        }             
    
        // ✅ Hitung PPN (11% dari sub_total)
        $ppn = $sub_total * 0.11;
    
        // ✅ Hitung Grand Total
        $grand_total = $sub_total  + $ppn;        

        // Buat Sales Order dengan PPN & Grand Total
        $newSalesOrder = SalesOrder::where('id_so', $salesOrder->id_so)->update([            
            'sub_total'       => $sub_total,
            'ppn'             => $ppn, // ✅ PPN otomatis dihitung
            'grand_total'     => $grand_total, // ✅ Grand Total otomatis dihitung
        ]);  
              
        return response()->json([
            'message'  => 'Sales Order dan Invoice berhasil dibuat!',
            'sales_order' => $salesOrder,
            // 'invoice'  => $invoice
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $salesOrder = SalesOrder::find($id);
        if (is_null($salesOrder)) {
            return response()->json(['message' => 'Sales Order not found'], 404);
        }

        $validatedData = $request->validate([
            'employee_id' => 'required|integer',
            'code_so' => 'required|string|max:255',
            'termin' => 'required|string|max:255',
            'total_tax' => 'required|numeric',
            'status_payment' => 'required|string|max:255',
            'sub_total' => 'required|numeric',
            'deposit' => 'required|numeric',
            'ppn' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'issue_at' => 'required|date',
            'due_at' => 'required|date',
        ]);

        $salesOrder->update($validatedData);
        return response()->json($salesOrder);
    }

    public function destroy($id)
    {
        $salesOrder = SalesOrder::find($id);
        if (is_null($salesOrder)) {
            return response()->json(['message' => 'Sales Order not found'], 404);
        }

        $salesOrder->delete();
        return response()->json(['message' => 'Sales Order deleted successfully']);
    }

    public function getAR(){
        $salesOrder = SalesOrder::with(['customer','employee'])
            ->whereColumn('deposit', '<', 'grand_total')
            ->get();
        
        return response()->json($salesOrder);
    }
}
