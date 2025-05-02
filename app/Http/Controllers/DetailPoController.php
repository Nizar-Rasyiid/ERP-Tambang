<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\DetailPo;
use App\Models\DetailSo;


class DetailPoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detailPo = DetailPo::with([
            'product',
            'purchaseorders',
            'purchaseorders.vendor',
        ])
        ->get();
        return response()->json($detailPo);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function laporan()
    {
        $formattedReport = [];

$purchaseOrders = DetailPo::with([
    'product',
    'purchaseorders.vendor',
    'detailso.salesorders.customer',
    'detailso.salesorders.employee',
])->orderBy('id_detail_po', 'asc') // FIFO: Ambil stok dari PO yang paling lama
->get();

foreach ($purchaseOrders as $detailPO) {
    $stockAvailable = $detailPO->quantity_left; // Stok yang tersedia dari PO

    // Ambil semua Sales Orders yang membutuhkan stok dari produk ini
    $salesOrders = DetailSo::where('product_id', $detailPO->product_id)
        ->where('quantity_left', '>', 0) // Hanya yang masih membutuhkan stok
        ->orderBy('id_detail_so', 'asc') // FIFO untuk SO juga
        ->get();

    foreach ($salesOrders as $detailSO) {
        if ($stockAvailable <= 0) break; // Jika stok dari PO ini habis, lanjut ke PO berikutnya

        $neededStock = $detailSO->quantity_left; // Stok yang dibutuhkan oleh SO

        // Tentukan jumlah yang bisa diberikan dari PO ini
        $usedStock = min($stockAvailable, $neededStock);

        // Kurangi stok yang tersedia di PO dan SO
        $stockAvailable -= $usedStock;
        $detailSO->decrement('quantity_left', $usedStock);
        $detailPO->decrement('quantity_left', $usedStock);

        // Hitung Gross Profit dari harga beli terakhir
        $harga_beli = $detailPO->price;
        $harga_jual = $detailSO->price;
        $gross_profit = ($harga_jual - $harga_beli) * $usedStock;

        $formattedReport[] = [
            'id_po'         => $detailPO->purchaseorders->code_po,
            'id_so'         => $detailSO->salesorders->code_so,
            'product_id'    => $detailPO->product_id,
            'id_detail_po'  => $detailPO->id_detail_po,
            'id_detail_so'  => $detailSO->id_detail_so,
            'vendor'        => $detailPO->purchaseorders->vendor->vendor_name,
            'customer'      => $detailSO->salesorders->customer->customer_name,
            'product_name'  => $detailPO->product->product_desc,
            'harga_beli'    => $harga_beli,
            'harga_jual'    => $harga_jual,
            'quantity_used' => $usedStock,
            'gross_profit'  => $gross_profit,
            'gp%'           => $gross_profit / ($harga_beli * $usedStock) * 100,
            'issue_at'      => $detailSO->salesorders->issue_at,
            'due_at'        => $detailSO->salesorders->due_at,
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
