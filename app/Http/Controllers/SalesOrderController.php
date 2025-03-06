<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\DetailSO;
use App\Models\Customer;

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
            'sales_order_details' => 'required|array',
        ]);
    
        // Ambil bulan & tahun saat ini
        $currentMonth = date('m'); // 02
        $currentYear  = date('Y'); // 2025
        $monthRoman   = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV',
            '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII',
            '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ];
    
        // Cari SO terakhir dalam bulan dan tahun yang sama
        $lastSo = SalesOrder::whereYear('created_at', $currentYear)
                            ->whereMonth('created_at', $currentMonth)
                            ->latest('id_so')
                            ->first();
    
        // Ambil ID terakhir & buat ID baru dengan format 2 digit
        $lastIdSo  = $lastSo ? intval(explode('/', $lastSo->code_so)[0]) : 0;
        $newIdSo   = str_pad($lastIdSo + 1, 2, '0', STR_PAD_LEFT); // Format 2 digit (00, 01, 02, ...)
    
        // Ambil Nama Customer dari tabel `customers` berdasarkan `customer_id`
        $customer = Customer::where('customer_id', $request->customer_id)->value('customer_singkatan') ?? 'Unknown';
    
        // Format kode SO: 00(ID_SO)/SO/NamaCustomer/II/2025
        $formattedCodeSo = "{$newIdSo}/SO/{$customer}/{$monthRoman[$currentMonth]}/{$currentYear}";
    
        // Buat Sales Order
        $salesOrder = SalesOrder::create([
            'customer_id'    => $request->customer_id,
            'employee_id'    => $request->employee_id,
            'code_so'        => $formattedCodeSo,
            'termin'         => $request->termin,            
            'total_tax'      => $request->total_tax,
            'status_payment' => $request->status_payment,
            'sub_total'      => 0,            
            'total_service'  => 0,
            'deposit'        => $request->deposit,
            'ppn'            => 0, // ✅ PPN otomatis dihitung
            'grand_total'    => 0, // ✅ Grand Total otomatis dihitung
            'issue_at'       => $request->issue_at,
            'due_at'         => $request->due_at,
        ]);
    
        $sub_total = 0;
    
        foreach ($request->sales_order_details as $pro) {                                                                
            $line_total = $pro['price'] * $pro['quantity'];
            $sub_total += $line_total;
    
            DetailSO::create([
                'id_so'      => $salesOrder->id_so,                
                'product_id' => $pro['product_id'],
                'quantity'   => $pro['quantity'],
                'price'      => $pro['price'],
                'amount'     => $pro['amount'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }             
    
        // ✅ Hitung PPN (11% dari sub_total)
        $ppn = $sub_total * 0.11;
    
        // ✅ Hitung Grand Total
        $grand_total = $sub_total + $ppn;        
    
        // Update Sales Order dengan PPN & Grand Total
        $salesOrder->update([
            'sub_total'   => $sub_total,
            'ppn'         => $ppn, // ✅ PPN otomatis dihitung
            'grand_total' => $grand_total, // ✅ Grand Total otomatis dihitung
        ]);  
    
        return response()->json([
            'message'      => 'Sales Order berhasil dibuat!',
            'sales_order'  => $salesOrder,            
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
