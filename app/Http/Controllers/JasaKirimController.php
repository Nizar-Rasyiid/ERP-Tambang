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
        $jakir = PoJasaKirim::with('vendor', 'detailjakir')->get();
        return response()->json($jakir);
    }   
    
    public function indexDetail() 
    {
        $detail = DetailJakir::with([
            'jasakirim.vendor',
            'product',
        ])->get();

        return response()->json($detail);
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
                            ->latest('id_jasakirim')
                            ->first();

        // Ambil ID terakhir & buat ID baru dengan format 2 digit
        $lastIdPo  = $lastPo ? intval(explode('/', $lastPo->code_jasakirim)[0]) : 0;
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

        $jakirim = PoJasaKirim::create($po);
        
        foreach ($request->purchase_order_details as $jakir) {
            $detailjakir = DetailJakir::create([
                'id_jasakirim' => $jakirim->id_jasakirim,
                'product_name' => $jakir['product_desc'],
                'quantity' => $jakir['quantity'],
                'price' => $jakir['price'],
                'amount' => $jakir['amount'],
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
        
        return response()->json($jasakirim);
    }  
    
    public function update(Request $request, string $id)
    {        
        $jakir = PoJasaKirim::findOrFail($id);
        $jakir->update([
            'vendor_id'     => $request->vendor_id,
            'employee_id'   => $request->employee_id,            
            'termin'        => $request->termin,
            'deposit'       => $request->deposit,
            'sub_total'     => $request->sub_total,            
            'ppn'           => $request->ppn,
            'grand_total'   => $request->grand_total,
            'issue_at'      => $request->issue_at,
            'due_at'        => $request->due_at,  
        ]);

        if ($request->has('jakir_details')) {
            $existingDetails = DetailJakir::where('id_jasakirim', $id)
                ->pluck('id_detail_jakir')->toArray();
            $procesIds = [];

            foreach ($request->jakir_details as $details) {
                if (isset($details['id_detail_jakir']) && in_array($details['id_detail_jakir'], $existingDetails)) {
                    DetailJakir::where('id_detail_jakir', $details['id_detail_jakir'])
                        ->update([
                            'id_jasakirim' => $jakir->id_jasakirim,
                            'product_name' => $details['product_desc'],
                            'quantity' => $details['quantity'],
                            'price' => $details['amount'], 
                        ]);
                    
                    $procesIds[] = $details['id_detail_jakir'];
                }else{
                    $detailjakir = DetailJakir::create([
                        'id_jasakirim' => $jakir->id_jasakirim,
                        'product_name' => $details['product_desc'],
                        'quantity'     => $details['quantity'],
                        'price'        => $details['amount'], 
                    ]);
                    if ($detailjakir) {
                        $procesIds[] = $detailjakir->id_detail_jakir;
                    }
                }
            }

            $detailDelete = array_diff($existingDetails, $procesIds);
            if (!empty($detailDelete)) {
                DetailJakir::whereIn('id_detail_jakir', $detailDelete)
                    ->delete();
            }            
        }

        return response()->json($jakir);
    }

    public function detail($id) 
    {
        $detail = DetailJakir::with([
            'jasakirim',
            'product',
        ])->where('id_jasakirim', $id)
        ->get();
        return response()->json($detail);
    }

    public function editPPn(Request $request, string $id){
        $sub_total = $request->sub_total;
        $ppns = $request->ppn;
        
        $ppn = 0;
        $grand_total = 0;
        if ($ppns != 0) {            
            $grand_total = $sub_total;
        }else{
            $ppn = $sub_total * 0.11;
            $grand_total = $sub_total + $ppn;   
        }

        $purchaseOrder = PoJasaKirim::findOrFail($id)->update([
            'ppn' => $ppn,
            'grand_total' => $grand_total,
        ]);

        return response()->json($purchaseOrder);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function approved($id){
        $approve = PoJasaKirim::findOrFail($id)->update([
            'approved' => 1,
        ]);

        return response()->json([
            'approved' => 'Purchase Order been Approved',
        ]);
    }

    // 🔴 DELETE: Hapus Purchase Order
    public function destroy($id)
    {
        $purchaseOrder = PoJasaKirim::findOrFail($id);
        $purchaseOrder->delete();

        return response()->json(['message' => 'Purchase Order deleted successfully']);
    }
}
