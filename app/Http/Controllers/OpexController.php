<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsorbDetail;
use App\Models\Opex;
use App\Models\Product;
use App\Models\Customer;
use App\Models\StockHistory;

class OpexController extends Controller
{
    // Get all Opex records
    public function index()
    {
        $opexes = Opex::with('customer', 'absorbDetail')->get();
        return response()->json($opexes);
    }

    // Get a single Opex record
    public function show($id)
    {
        $opex = Opex::find($id);
        
        if (is_null($opex)) {
            return response()->json(['message' => 'Opex not found'], 404);
        }
        $opex->load('customer');
        return response()->json($opex);
    }

    // Create a new Opex record
    public function store(Request $request)
    {
        $last = Opex::latest()->first();
        $lastId = $last ? $last->opex_code : 1000;
        $newId = $lastId + 1; 

        $opex = Opex::create([
            'opex_code' => $newId,
            'opex_name' => $request->opex_name,
            'opex_type' => $request->opex_type,
            'opex_price' => $request->opex_price,
            'customer_id' => $request->customer_id,
            'issue_at' => $request->issue_at,
        ]);
        
        return response()->json($opex, 201);
    }

    public function storeAbsorb(Request $request)
    {
        $last = Opex::latest()->first();
        $lastId = $last ? $last->opex_code : 1000;
        $newId = $lastId + 1; 

        $opex = Opex::create([
            'opex_code' => $newId,
            'opex_name' => $request->opex_name,
            'opex_type' => $request->opex_type,
            'opex_price' => $request->opex_price,
            'customer_id' => $request->customer_id,
            'issue_at' => $request->issue_at,
        ]);
        
        foreach ($request->sales_order_details as $absorb) {
            AbsorbDetail::create([
                'opex_id' => $opex->opex_id,
                'product_id' => $absorb['product_id'],
                'quantity' => $absorb['quantity'],
                'price' => $absorb['price'],
            ]);
        }    
        return response()->json($opex, 201);
    }

    public function absorbDetail($id){
        $absorbDetail = AbsorbDetail::with([            
            'product'
        ])        
        ->where('opex_id', $id)
        ->get();

        return response()->json($absorbDetail);
    }

    // Update an existing Opex record
    public function update(Request $request, $id)
    {        
        $opex = Opex::findOrFail($id)->update([            
            'opex_name' => $request->opex_name,
            'opex_type' => $request->opex_type,
            'opex_price' => $request->opex_price,
            'customer_id' => $request->customer_id,
            'issue_at' => $request->issue_at,
        ]);

        return response()->json($opex);
    }

    public function approved(Request $request, $id)
    {
        $request->validate([
            'detail'    => 'required|array',             
        ]);                
        $opex = Opex::findOrFail($id)->update([
            'approved' => 1,
        ]);

        foreach ($request->detail as $detail) {
            $stock = StockHistory::with('product')
                    ->where('product_id', $detail['product_id'])
                    ->where('quantity_left', '>=', $detail['quantity'])
                    ->first();
            
            if ($stock) {
                $product = Product::findOrFail($detail['product_id'])
                    ->decrement('product_stock', $detail['quantity']);
            }                                             
        }
        return response()->json('Berhasil Approve');
    }

    public function updateAbsorb(Request $request, $id){
        $opex = Opex::findOrFail($id)->update([            
            'opex_name' => $request->opex_name,
            'opex_type' => $request->opex_type,
            'opex_price' => $request->opex_price,
            'customer_id' => $request->customer_id,
            'issue_at' => $request->issue_at,
        ]);
        $existingDetailIds = AbsorbDetail::where('opex_id', $id)->pluck('id_absorb_detail')->toArray();

        $processedIds = [];

        foreach ($request->sales_order_details as $detail) {                
            if (isset($detail['id_absorb_detail']) && in_array($detail['id_absorb_detail'], $existingDetailIds)) {
                AbsorbDetail::where('id_absorb_detail', $detail['id_absorb_detail'])->update([                    
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                ]);     
                $processedIds[] = $pro['id_absorb_detail'];                                                   
            }else{
                $detail = AbsorbDetail::create([
                    'opex_id' => $id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                ]);                    
                $processedIds[] = $detail->id_absorb_detail;
            }
        };

        $detailsToDelete = array_diff($existingDetailIds, $processedIds);
        if (!empty($detailsToDelete)) {
            AbsorbDetail::whereIn('id_absorb_detail', $detailsToDelete)->delete();
        }
        return response()->json($opex);
    }

    // Delete an Opex record
    public function destroy($id)
    {
        $opex = Opex::find($id);
        if (is_null($opex)) {
            return response()->json(['message' => 'Opex not found'], 404);
        }
        $opex->delete();
        return response()->json(null, 204);
    }
}
