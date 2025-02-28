<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\DetailSO;

class SalesOrderController extends Controller
{
    public function index()
    {
        $salesOrders = SalesOrder::all();
        return response()->json($salesOrders);
    }

    public function show($id)
    {
        $salesOrder = SalesOrder::find($id);
        if (is_null($salesOrder)) {
            return response()->json(['message' => 'Sales Order not found'], 404);
        }
        return response()->json($salesOrder);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_product'      => 'required|array|exists:products,id_product',
            'quantity'        => 'required|array',
            'id_customer'     => 'required|exists:customers,id_customer',
            'id_payment_type' => 'required|exists:payment_types,id_payment_type',
            'id_bank_account' => 'required|exists:bank_accounts,id_bank_account',
            'code_so'         => 'required|string',              
            'so_type'         => 'required|in:type1,type2,type3',
            'status_payment'  => 'required|string',
            'sub_total'       => 'required|integer',
            'total_tax'       => 'required|integer',
            'total_service'   => 'required|integer',
            'deposit'         => 'required|integer',
            'issue_at'        => 'required|date',
            'due_at'          => 'required|date',            
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

        // Ambil id_product sebagai array atau kosongkan jika null
        $id_products = $request->input('id_product', []);

        if (!is_array($id_products) || empty($id_products)) {
            return response()->json([
                'message' => 'Produk tidak ditemukan atau kosong!',
            ], 422);
        }

        // variable total_biaya from product_price
        $product = [];
        $sub_total = 0;

        foreach($request->id_product as $key => $pro){  
            $product_price = $request->price[$key];                                         
            $quantity = $request->input('quantity')[$key];
            $line_total = $product_price * $quantity;

            $sub_total += $line_total;

            $detailso = DetailSO::create([
            'id_so' => $salesOrder->id_so,                
            'product_id' => $pro,
            'quantity' => $quantity,
            'price' => $product_price,
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
        
        // $customer = Customer::where('id_customer', $request->id_customer)->first();
        // $currentMonth = date('m');
        // $currentYear = date('y');

        // $nomor_invoice = sprintf(
        //     "%s/AHM/%s/%s/%s",
        //     $salesOrder->code_so,
        //     $customer->customer_name,
        //     $currentMonth,
        //     $currentYear,
        // );    
    
        // // 2️⃣ Buat Invoice dari Sales Order yang baru dibuat
        // $invoice = Invoice::create([            
        //     'id_so'          => $salesOrder->id_so,
        //     'id_customer'    => $salesOrder->id_customer,
        //     'id_bank_account'=> $salesOrder->id_bank_account,
        //     'id_payment_type'=> $salesOrder->id_payment_type,
        //     'no_invoice'     => $nomor_invoice,
        // ]);        
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
}
