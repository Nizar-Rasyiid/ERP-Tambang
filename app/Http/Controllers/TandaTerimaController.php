<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TandaTerima;

class TandaTerimaController extends Controller
{
    public function index(){
        $tandater = TandaTerima::all();
        return response()->json($tandater);
    }
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
        'code_tandater'  => $formattedCodePo,
        'resi'         => $request->resi,                    
    ]);

    $sub_total = 0;

    foreach ($request->tanda_terima_details as $pro) {                                                                
        $line_total = $pro['price'] * $pro['quantity'];
        $sub_total += $line_total;

        DetailPO::create([
            'id_tandater'=> $purchaseOrder->id_tandater,                
            'id_do'      => $pro['id_do'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }                  

    return response()->json([
        'message'        => 'Purchase Order berhasil dibuat!',
        'purchase_order' => $purchaseOrder,
    ], 201);
}
}
