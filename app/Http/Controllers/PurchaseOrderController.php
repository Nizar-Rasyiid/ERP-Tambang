<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\PaymentType;
use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\DetailPo;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;


class PurchaseOrderController extends Controller
{
    // 🟢 GET: Tampilkan semua Purchase Orders
    public function index()
    {
        $purchaseOrders = PurchaseOrder::all();
        return response()->json($purchaseOrders);
    }

    // 🔵 POST: Simpan Purchase Order Baru
    public function store(Request $request)
    {
        // $request->validate([
        //     'id_product'      => 'required|array|exists:products,id_product',
        //     'quantity'        => 'required|array',
        //     'id_customer'     => 'required|exists:customers,id_customer',
        //     'id_payment_type' => 'required|exists:payment_types,id_payment_type',
        //     'id_bank_account' => 'required|exists:bank_accounts,id_bank_account',
        //     'code_po'         => 'required|string',              
        //     'po_type'         => 'required|in:type1,type2,type3',
        //     'status_payment'  => 'required|string',
        //     'sub_total'       => 'required|integer',
        //     'total_tax'       => 'required|integer',
        //     'total_service'   => 'required|integer',
        //     'deposit'         => 'required|integer',
        //     'issue_at'        => 'required|date',
        //     'due_at'          => 'required|date',            
        // ]);

        $lastPo = PurchaseOrder::latest()->first();
        $lastIdPo = $lastPo ? intval(substr($lastPo->code_po, 5)) : 0;
        $newIdPo = $lastIdPo + 1;
        $id_purchase_orders = 'PO-'. str_pad($newIdPo, 6, '0', STR_PAD_LEFT);                     

        // Buat Purchase Order dengan PPN & Grand Total
        $purchaseOrder = PurchaseOrder::create([
            'code_po'         => $id_purchase_orders,
            'id_customer'     => $request->id_customer,
            'id_payment_type' => $request->id_payment_type,
            'id_bank_account' => $request->id_bank_account,
            'po_type'         => $request->po_type,
            'status_payment'  => $request->status_payment,
            'sub_total'       => 0,
            'total_tax'       => $request->total_tax,
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

        foreach($request->id_product as $key => $pro){                       
            $product_price = Product::where('id_product', $pro)->value('product_price');              
            
            $quantity = $request->input('quantity')[$key];
            $line_total = $product_price * $quantity;

            $sub_total += $line_total;

            $detailpo = DetailPO::insert([
                'id_po' => $purchaseOrder->id_po,
                'code_po' => $purchaseOrder->code_po,
                'id_product' => $pro,
                'quantity' => $request->input('quantity')[$key],
                'created_at' => now(),
                'updated_at' => now(),
            ]);            
        }             
    
        // ✅ Hitung PPN (11% dari sub_total)
        $ppn = $sub_total * 0.11;
    
        // ✅ Hitung Grand Total
        $grand_total = $sub_total  + $ppn;        

        // Buat Purchase Order dengan PPN & Grand Total
        $newPurchaseOrder = PurchaseOrder::where('id_po', $purchaseOrder->id_po)->update([            
            'sub_total'       => $sub_total,
            'ppn'             => $ppn, // ✅ PPN otomatis dihitung
            'grand_total'     => $grand_total, // ✅ Grand Total otomatis dihitung
        ]);  
        
        $customer = Customer::where('id_customer', $request->id_customer)->first();
        $currentMonth = date('m');
        $currentYear = date('y');

        $nomor_invoice = sprintf(
            "%s/AHM/%s/%s/%s",
            $purchaseOrder->code_po,
            $customer->customer_name,
            $currentMonth,
            $currentYear,
        );    
    
        // 2️⃣ Buat Invoice dari Purchase Order yang baru dibuat
        $invoice = Invoice::create([            
            'id_po'          => $purchaseOrder->id_po,
            'id_customer'    => $purchaseOrder->id_customer,
            'id_bank_account'=> $purchaseOrder->id_bank_account,
            'id_payment_type'=> $purchaseOrder->id_payment_type,
            'no_invoice'     => $nomor_invoice,
        ]);        
        return response()->json([
            'message'  => 'Purchase Order dan Invoice berhasil dibuat!',
            'purchase_order' => $purchaseOrder,
            // 'invoice'  => $invoice
        ], 201);
    }
    

    // 🟠 GET: Tampilkan Purchase Order berdasarkan ID
    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        return response()->json($purchaseOrder);
    }

    // 🟡 PUT: Update Purchase Order
    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        $request->validate([
            'id_customer' => 'sometimes|exists:customers,id_customer',
            'id_payment_type' => 'sometimes|exists:payment_types,id_payment_type',
            'id_bank_account' => 'sometimes|exists:bank_accounts,id_bank_account',
            'po_type' => 'sometimes|in:type1,type2,type3',
            'status_payment' => 'sometimes|string',
            'sub_total' => 'sometimes|integer',
            'total_tax' => 'sometimes|integer',
            'total_service' => 'sometimes|integer',
            'deposit' => 'sometimes|integer',
            'issue_at' => 'sometimes|date',
            'due_at' => 'sometimes|date',
        ]);

        // Hitung ulang PPN dan Grand Total jika nilai sub_total berubah
        if ($request->has('sub_total')) {
            $ppn = $request->sub_total * 0.11;
            $grand_total = $request->sub_total + $request->total_tax + $request->total_service + $ppn - $request->deposit;
            $request->merge(['ppn' => $ppn, 'grand_total' => $grand_total]);
        }

        $purchaseOrder->update($request->all());

        return response()->json($purchaseOrder);
    }

    // 🔴 DELETE: Hapus Purchase Order
    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->delete();

        return response()->json(['message' => 'Purchase Order deleted successfully']);
    }
}
