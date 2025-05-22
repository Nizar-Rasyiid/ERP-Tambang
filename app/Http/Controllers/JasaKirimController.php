<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PoJasaKirim;
use App\Models\DetailJakir;
use App\Models\Vendor;
use Illuminate\Http\Request;

class JasaKirimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jakir = PoJasaKirim::all();
        return response()->json($jakir);
    }    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Ambil bulan & tahun saat ini
        $currentMonth = date('m'); // 02
        $currentYear  = date('Y'); // 2025
        $monthRoman   = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV',
            '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII',
            '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ];

        // Cari PO terakhir dalam bulan dan tahun yang sama
        $lastPo = PoJasaKirim::whereYear('created_at', $currentYear)
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

        $checkPPN = $request->checkppn;        

        $po = [
            'vendor_id'     => $request->vendor_id,
            'employee_id'   => $request->employee_id,
            'code_jasakirim'=> $formattedCodePo,
            'termin'        => $request->termin,
            'deposit'       => $request->deposit,
            'sub_total'     => $request->sub_total,            
            'ppn'           => $request->ppn,
            'grand_total'   => $request->grand_total,
            'issue_at'      => $request->issue_at,
            'due_at'        => $request->due_at,        
        ];

        $jakir = PoJasaKirim::create($po);
        
        foreach ($jasa_kirim_details as $jakir) {
            $detailjakir = DetailJakir::create([
                'id_jasakirim' => $po->id_jasakirim,
                'product_id' => $jakir['product_id'],
                'quantity' => $jakir['quantity'],
                'price' => $jakir['amount'],                
            
            ]);
        }   
        return response()->json($po);                
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jasakirim = PoJasaKirim::with([
            'vendor'
        ])->where('id_jasakirim', $id)
        ->get();   
    }
    public function detail($id) 
    {
        $detail = DetailJakir::with([
            'jasakirim',
        ])->where('id_jasakirim', $id)
        ->get();
        return response()->json($detail);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
