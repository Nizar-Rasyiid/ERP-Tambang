<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\DetailSO;
use App\Models\Customer;
use App\Models\Product;

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
            'po_number'      => $request->po_number,
            'termin'         => $request->termin,            
            'total_tax'      => $request->total_tax,
            'status_payment' => $request->status_payment,
            'sub_total'      => 0,            
            'total_service'  => 0,
            'has_do'         => 0,
            'has_invoice'    => 0,
            'deposit'        => $request->deposit,
            'has_invoice'    => $request->has_invoice,
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
                'quantity_left' => 0,
                'has_do'     => 0,
                'price'      => $pro['price'],
                'discount'   => $pro['discount'],
                'amount'     => $pro['amount'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);    
        }    
        
        $ppn = $sub_total * 0.11;

        $salesorder = SalesOrder::where('id_so', $salesOrder->id_so)->update([
            'sub_total' => $sub_total,
            'ppn' => $ppn,
            'grand_total' => $sub_total + $ppn,
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

        $salesOrder->update([
            'customer_id'    => $request->customer_id,
            'employee_id'    => $request->employee_id,            
            'po_number'      => $request->po_number,
            'termin'         => $request->termin,            
            'total_tax'      => $request->total_tax,
            'status_payment' => $request->status_payment,
            'deposit'        => $request->deposit,
            'has_invoice'    => $request->has_invoice,
            'issue_at'       => $request->issue_at,
            'due_at'         => $request->due_at,
        ]);;
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
    public function monthlySales()
    {
        // Ambil data penjualan per bulan
        $monthlySales = SalesOrder::select(
            DB::raw('YEAR(issue_at) as year'),
            DB::raw('MONTH(issue_at) as month'),
            DB::raw('SUM(grand_total) as total_sales')
        )
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        // Format data untuk response
        $formattedSales = $monthlySales->map(function ($item) {
            return [
                'year' => $item->year,
                'month' => $item->month,
                'month_name' => date('F', mktime(0, 0, 0, $item->month, 10)), // Nama bulan
                'total_sales' => (float) $item->total_sales, // Pastikan nilai numerik
            ];
        });

        return response()->json($formattedSales);
    }
}
