<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TandaTerima;
use App\Models\Invoice;
use App\Models\SalesOrder;
use App\Models\DetailTandater;

class TandaTerimaController extends Controller
{
    public function index(){
        $tandater = TandaTerima::with(['so','customer', 'detailtandater.so', 'detailtandater.invoice'])->get();
        return response()->json($tandater);
    }
    public function store(Request $request)
{
    $request->validate([
        'tandaterima_details' => 'required|array',
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
    $lastPo = TandaTerima::whereYear('created_at', $currentYear)
                           ->whereMonth('created_at', $currentMonth)
                           ->latest('id_tandater')
                           ->first();

    // Ambil ID terakhir & buat ID baru dengan format 2 digit
    $lastIdPo  = $lastPo ? intval(explode('/', $lastPo->code_tandater)[0]) : 0;
    $newIdPo   = str_pad($lastIdPo + 1, 2, '0', STR_PAD_LEFT); // Format 2 digit (00, 01, 02, ...)

    // Format kode PO: 00(ID_PO)/PO/NamaVendor/II/2025
    $formattedCodePo = "{$newIdPo}/TT/{$monthRoman[$currentMonth]}/{$currentYear}";

    // Buat Purchase Order
    $purchaseOrder = Tandaterima::create([        
        'code_tandater' => $formattedCodePo,
        'id_so'         => $request->id_so,
        'customer_id'   => $request->customer_id,
        'resi'          => $request->resi,   
        'issue_at'      => $request->issue_at,
        'due_at'        => $request->due_at,                 
    ]);

    foreach ($request->tandaterima_details as $pro) {                                                                
        DetailTandater::create([
            'id_invoice'=> $pro['id_invoice'], 
            'id_so' => $pro['id_so'],
            'id_tandater' => $purchaseOrder->id_tandater,                           
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Invoice::findOrFail($pro['id_invoice'])->update([
            'has_tandater' => 1,
        ]);        
    }      
    $allInvoice = Invoice::where('id_so', $request->id_so)->get();

    $allHasTandaterima = true;
    foreach($allInvoice as $inv)
    {
        if ($inv->has_tandater != 1) {
            $allHasTandaterima = false;
            break;
        }
    }

    if ($allHasTandaterima) {
        SalesOrder::findOrFail($request->id_so)->update(['has_tandater' => 1]);
    }


    return response()->json([
        'message'        => 'Purchase Order berhasil dibuat!',
        'purchase_order' => $purchaseOrder,
    ], 201);
}

    public function show($id){
        $tandater = Tandaterima::with(['customer', 'so'])
            ->where('id_tandater', $id)
            ->get();

        return response()->json($tandater);
    }

    public function detail($id) {
        $detail = DetailTandater::with([
            'so',
            'invoice'
        ])
        ->where('id_tandater', $id)
        ->get();        

        return response()->json($detail);
    }

    public function getDetail() {
        $detail = DetailTandater::with([
            'so',
            'so.customer',
            'invoice',
            'tandater'
        ])    
        ->get();        

        return response()->json($detail);
    }

    public function update(Request $request, string $id){
        $purchaseOrder = Tandaterima::findOrFail($id);

        $purchaseOrder->update([                    
            'id_so'         => $request->id_so,
            'customer_id'   => $request->customer_id,
            'resi'          => $request->resi,   
            'issue_at'      => $request->issue_at,
            'due_at'        => $request->due_at,                 
        ]);
    }
}
