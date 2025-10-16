<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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

    try {
        DB::beginTransaction();
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
            'customer_id'   => $request->customer_id,
            'employee_id'   => $request->employee_id,
            'code_tandater' => $formattedCodePo,
            'resi'          => $request->resi,                  
            'issue_at'      => $request->issue_at,
            'due_at'        => $request->due_at,               
        ]);

        foreach ($request->tandaterima_details as $pro) {                                                                
            DetailTandater::create([
                'id_tandater' => $purchaseOrder->id_tandater,
                'id_so' => $pro['id_so'],
                'id_invoice'=> $pro['id_invoice'], 
                'issue_at'  => $request->issue_at,            
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Invoice::findOrFail($pro['id_invoice'])->update([
                'has_tandater' => 1,
            ]);      
            SalesOrder::findOrFail($pro['id_so'])->update([
                'has_tandater'  => 1,
            ]);
        } 
        DB::commit();
        return response()->json([
            'message'        => 'Purchase Order berhasil dibuat!',
            'purchase_order' => $purchaseOrder,
        ], 201);
    } catch (\Excaption $e) {
        return response()->json([
            'message'       => 'Purchase Order berhasil dibuat!',
            'error'         => $e,
        ], 403);
    }         
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
        try {
            DB::beginTransaction();
            $purchaseOrder = Tandaterima::findOrFail($id);

            $purchaseOrder->update([                    
                'id_so'         => $request->id_so,
                'customer_id'   => $request->customer_id,
                'resi'          => $request->resi,   
                'issue_at'      => $request->issue_at,
                'due_at'        => $request->due_at,                 
            ]);

            $existingDetail = DetailTandater::where('id_tandater', $id)->pluck('id_detail_tandater')->toArray();
            $processIds = [];

            foreach($request->tandaterima_details as $pro){
                if (isset($pro['id_detail_tandater']) && in_array($pro['id_detail_tandater'], $existingDetail)) {
                    DetailTandater::where('id_detail_tandater', $pro['id_detail_tandater'])->update([
                        'id_tandater' => $id,
                        'id_so' => $pro['id_so'],
                        'id_invoice'=> $pro['id_invoice'], 
                        'issue_at'  => $request->issue_at,                                
                        'updated_at' => now(),
                    ]);
                    $allInvoiceIds = DetailTandater::pluck('id_invoice')->unique()->toArray();
                    Invoice::whereIn('id_invoice', $allInvoiceIds)->update(['has_tandater' => 1]);
                    Invoice::whereNotIn('id_invoice', $allInvoiceIds)->update(['has_tandater' => 0]);

                    $allSoIds = DetailTandater::pluck('id_so')->unique()->toArray();
                    SalesOrder::whereIn('id_so', $allSoIds)->update(['has_tandater' => 1]);
                    SalesOrder::whereNotIn('id_so', $allSoIds)->update(['has_tandater' => 0]);

                    $processIds[] = $pro['id_detail_tandater'];
                }else {
                    $detail = DetailTandater::create([
                        'id_tandater' => $purchaseOrder->id_tandater,
                        'id_so' => $pro['id_so'],
                        'id_invoice'=> $pro['id_invoice'], 
                        'issue_at'  => $request->issue_at,            
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $allInvoiceIds = DetailTandater::pluck('id_invoice')->unique()->toArray();
                    Invoice::whereIn('id_invoice', $allInvoiceIds)->update(['has_tandater' => 1]);
                    Invoice::whereNotIn('id_invoice', $allInvoiceIds)->update(['has_tandater' => 0]);

                    $allSoIds = DetailTandater::pluck('id_so')->unique()->toArray();
                    SalesOrder::whereIn('id_so', $allSoIds)->update(['has_tandater' => 1]);
                    SalesOrder::whereNotIn('id_so', $allSoIds)->update(['has_tandater' => 0]);
                    $processIds[] = $detail->id_detail_tandater;
                }
            }

            $detailsToDelete = array_diff($existingDetail, $processIds);
            if (!empty($detailsToDelete)) {                
                // Hapus detail tandaterima
                DetailTandater::whereIn('id_detail_tandater', $detailsToDelete)->delete();
            }
            DB::commit();
            return response()->json([
                'message'   => 'Sukses',
                'success'   => $detailsToDelete
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message'   => 'Gagal',
                'error'     => $e->getMessage()
            ], 403);
        }
    }

    public function destroy ($id)
    {
        $tandater = Tandaterima::findOrFail($id)->delete();

        $detail = DetailTandater::where('id_tandater', $id)->delete();
    }
}
