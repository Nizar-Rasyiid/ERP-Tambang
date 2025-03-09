<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\DetailPo;


class DetailPoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detailPo = DetailPo::all();
        return response()->json($detailPo);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function laporan()
    {
        $laporan = DetailPo::with([
            'detailso', 
            'product',
            'purchaseorders',
            'detailso.salesorders',
            'purchaseorders.vendor',
            'detailso.salesorders.employee',
            'detailso.salesorders.customer',
            ])            
            ->get();
        
        foreach ($laporan as $detailPO) {
            foreach ($detailPO->detailso as $detailSO) {                
                    $formattedReport[] = [
                        'id_po' => $detailPO->purchaseorders->code_po,
                        'id_so' => $detailSO->salesorders->code_so,
                        'product_id' => $detailPO->product_id,
                        'id_detail_po' => $detailPO->id_detail_po,
                        'id_detail_so' => $detailSO->id_detail_so,                    
                        'vendor' => $detailPO->purchaseorders->vendor->vendor_name,
                        'customer' => $detailSO->salesorders->customer->customer_name,
                        'product_name' => $detailPO->product->product_desc,                                 
                        'harga_beli' => $detailPO->purchaseorders->grand_total,
                        'harga_jual' => $detailSO->salesorders->grand_total,
                        'gross_profit' => $detailPO->purchaseorders->grand_total - $detailSO->salesorders->grand_total,
                        'issue_at' => $detailSO->salesorders->issue_at,
                        'due_at' => $detailSO->salesorders->due_at,
                        'gp%' => ($detailPO->purchaseorders->grand_total - $detailSO->salesorders->grand_total) / $detailPO->purchaseorders->grand_total,
                    ];                
            }
        }

        return response()->json($formattedReport);            
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $detailPo = DetailPo::with('product')
            ->where('id_po', $id)
            ->get();
            
        return response()->json($detailPo);
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
