<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AbsorbDetail;
use App\Models\Opex;
use App\Models\Customer;

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

    public function updateAbsorb(Request $request, $id){
        $opex = Opex::findOrFail($id)->update([            
            'opex_name' => $request->opex_name,
            'opex_type' => $request->opex_type,
            'opex_price' => $request->opex_price,
            'customer_id' => $request->customer_id,
            'issue_at' => $request->issue_at,
        ]);
        foreach ($request->sales_order_details as $detail) {
                $quantity = $detail['quantity'] ?? 0;
                $price = $detail['price'] ?? 0;
                $discount = $detail['discount'] ?? 0;
                $amount = isset($detail['amount']) ? $detail['amount'] : 
                      $price * $quantity * (1 - ($discount / 100));
                
                $sub_total += $amount;

                if (isset($detail['id_detail_so']) && in_array($detail['id_detail_so'], $existingDetailIds)) {
                if ($detail['product_type'] == 'product') {
                    DetailSo::where('id_detail_so', $detail['id_detail_so'])->update([
                        'product_id'    => $detail['product_id'],
                        'package_id'    => 0,
                        'product_type'  => $detail['product_type'],
                        'quantity'      => $quantity,
                        'quantity_left' => $detail['quantity_left'] ?? 0,
                        'has_do'        => $detail['has_do'] ?? 0,
                        'price'         => $price,
                        'discount'      => $discount,
                        'amount'        => $amount,
                    ]);
                }else{
                    DetailSo::where('id_detail_so', $detail['id_detail_so'])->update([
                        'product_id'    => 0,
                        'package_id'    => $detail['package_id'],
                        'product_type'  => $detail['product_type'],
                        'quantity'      => $quantity,
                        'quantity_left' => $detail['quantity_left'] ?? 0,
                        'has_do'        => $detail['has_do'] ?? 0,
                        'price'         => $price,
                        'discount'      => $discount,
                        'amount'        => $amount,
                    ]);
                }
                $processedIds[] = $detail['id_detail_so'];
                } else {
                    if ($detail['product_type'] == 'product') {
                        $newDetail = DetailSo::create([
                            'id_so'         => $id,
                            'product_id'    => $detail['product_id'],
                            'package_id'    => 0,
                            'product_type'  => $detail['product_type'],                    
                            'quantity'      => $quantity,
                            'quantity_left' => $detail['quantity_left'] ?? 0,
                            'has_do'        => $detail['has_do'] ?? 0,
                            'price'         => $price,
                            'discount'      => $discount,
                            'amount'        => $amount,
                        ]);
                    }else{
                        $newDetail = DetailSo::create([
                            'id_so'         => $id,
                            'product_id'    => 0,
                            'package_id'    => $detail['package_id'],
                            'product_type'  => $detail['product_type'],                    
                            'quantity'      => $quantity,
                            'quantity_left' => $detail['quantity_left'] ?? 0,
                            'has_do'        => $detail['has_do'] ?? 0,
                            'price'         => $price,
                            'discount'      => $discount,
                            'amount'        => $amount,
                        ]);
                    }            
                if ($newDetail) {
                    $processedIds[] = $newDetail->id_detail_so;
                }
                }
            }

            $detailsToDelete = array_diff($existingDetailIds, $processedIds);
            if (!empty($detailsToDelete)) {
                DetailSo::whereIn('id_detail_so', $detailsToDelete)->delete();
            }

            $ppn = $sub_total * 0.11;
            $salesOrder->update([
                'sub_total'   => $sub_total,
                'ppn'         => $ppn,
                'grand_total' => $sub_total + $ppn,
            ]);
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
