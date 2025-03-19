<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\PaymentType;
use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\DetailPo;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;


class PurchaseOrderController extends Controller
{
    // 🟢 GET: Tampilkan semua Purchase Orders
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['vendor', 'detailPo', 'detailPo.product'])->get();
        return response()->json($purchaseOrders);
    }

    // 🔵 POST: Simpan Purchase Order Baru
    public function store(Request $request)
{
    $request->validate([
        'purchase_order_details' => 'required|array',
    ]);

    // Ambil bulan & tahun saat ini
    $currentMonth = date('m'); // 02
    $currentYear  = date('Y'); // 2025
    $monthRoman   = [
        '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV',
        '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII',
        '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
    ];

    // Cari PO terakhir dalam bulan dan tahun yang sama
    $lastPo = PurchaseOrder::whereYear('created_at', $currentYear)
                           ->whereMonth('created_at', $currentMonth)
                           ->latest('id_po')
                           ->first();

    // Ambil ID terakhir & buat ID baru dengan format 2 digit
    $lastIdPo  = $lastPo ? intval(explode('/', $lastPo->code_po)[0]) : 0;
    $newIdPo   = str_pad($lastIdPo + 1, 2, '0', STR_PAD_LEFT); // Format 2 digit (00, 01, 02, ...)

    // Ambil Nama Vendor dari tabel `vendors` berdasarkan `vendor_id`
    $vendor = Vendor::where('vendor_id', $request->vendor_id)->value('vendor_singkatan') ?? 'Unknown';

    // Format kode PO: 00(ID_PO)/PO/NamaVendor/II/2025
    $formattedCodePo = "{$newIdPo}/PO/{$vendor}/{$monthRoman[$currentMonth]}/{$currentYear}";

    // Buat Purchase Order
    $purchaseOrder = PurchaseOrder::create([
        'vendor_id'      => $request->vendor_id, // ✅ Vendor, bukan Customer
        'employee_id'    => $request->employee_id,        
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

    foreach ($request->purchase_order_details as $pro) {                                                                
        $line_total = $pro['price'] * $pro['quantity'];
        $sub_total += $line_total;

        DetailPO::create([
            'id_po'      => $purchaseOrder->id_po,                
            'product_id' => $pro['product_id'],
            'quantity'   => $pro['quantity'],
            'quantity_left' => 0,
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

    // Update Purchase Order dengan PPN & Grand Total
    $purchaseOrder->update([
        'sub_total'   => $sub_total,
        'ppn'         => $ppn, // ✅ PPN otomatis dihitung
        'grand_total' => $grand_total, // ✅ Grand Total otomatis dihitung
    ]);  

    return response()->json([
        'message'        => 'Purchase Order berhasil dibuat!',
        'purchase_order' => $purchaseOrder,
    ], 201);
}


    // 🟠 GET: Tampilkan Purchase Order berdasarkan ID
    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with(['vendor', 'employee'])->find($id);
        return response()->json($purchaseOrder);
    }

    // 🟡 PUT: Update Purchase Order
    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        $purchaseOrder->update([
            'vendor_id'      => $request->vendor_id, // ✅ Vendor, bukan Customer
            'employee_id'    => $request->employee_id,            
            'termin'         => $request->termin,            
            'total_tax'      => $request->total_tax,
            'status_payment' => $request->status_payment,
            'deposit'        => $request->deposit,
            'issue_at'       => $request->issue_at,
            'due_at'         => $request->due_at,
        ]);

        return response()->json($purchaseOrder);
    }

    //Show per ID

    // 🔴 DELETE: Hapus Purchase Order
    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->delete();

        return response()->json(['message' => 'Purchase Order deleted successfully']);
    }

    public function goodReceive(Request $request)
    {             
        $allItemsReceived = true;         
        foreach($request->purchase_order_details as $pro) 
        {
            DetailPo::findOrFail($pro['id_detail_po'])
                ->increment('quantity_left', $pro['quantity_left']);

            Product::findOrFail($pro['product_id'])
                ->increment('product_stock', $pro['quantity_left']);                                                
        }    
        foreach($request->purchase_order_details as $pro)
        {
            $detailPro = DetailPo::findOrFail($pro['id_detail_po']);            
            if ($pro['quantity'] != $detailPro->quantity_left) {
                $allItemsReceived = false;
                break;
            }
        }
        if ($allItemsReceived) {
            $has_gr = PurchaseOrder::findOrFail($request->id_po)->update([
                'has_gr' => 1,
            ]); 
        }  
                        
        return response()->json([   
            'validate' => $allItemsReceived,         
            'message' => 'Good Receive berhasil dilakukan!',
        ]) ;       
    }

    public function getAP(){
        $purchaseOrder = PurchaseOrder::with(['vendor', 'employee'])           
            ->whereColumn('deposit', '<', 'grand_total')
            ->get();
        
        return response()->json($purchaseOrder);
    }

    public function approved($id){
        $approve = PurchaseOrder::findOrFail($id)->update([
            'approved' => 1,
        ]);

        return response()->json([
            'approved' => 'Purchase Order been Approved',
        ]);
    }

    public function monthlyPurchase()
    {
        // Ambil data penjualan per bulan
        $monthlyPurchase = PurchaseOrder::select(
            DB::raw('YEAR(issue_at) as year'),
            DB::raw('MONTH(issue_at) as month'),
            DB::raw('SUM(grand_total) as total_purchases')
        )
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        // Format data untuk response
        $formattedPurchase = $monthlyPurchase->map(function ($item) {
            return [
                'year' => $item->year,
                'month' => $item->month,
                'month_name' => date('F', mktime(0, 0, 0, $item->month, 10)), // Nama bulan
                'total_purchases' => (float) $item->total_purchases, // Pastikan nilai numerik
            ];
        });

        return response()->json($formattedPurchase);
    }
}
