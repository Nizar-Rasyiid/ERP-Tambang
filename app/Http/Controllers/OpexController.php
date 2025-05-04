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
        $opexes = Opex::with('customer')->get();
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
        ]);
        
        return response()->json($opex, 201);
    }

    public function storeAbsorb(Request $request)
    {
        $opex = Opex::create([
            'opex_code' => $request->opex_code,
            'opex_name' => $request->opex_name,
            'opex_type' => $request->opex_type,
            'opex_price' => $request->opex_price,
            'customer_id' => $request->customer_id,
        ]);
        
        foreach ($request->sales_order_details as $absorb) {
            AbsorbDetail::create([
                'opex_id' => $opex->id,
                'product_id' => $absorb['product_id'],
            ]);
        }

        
        return response()->json($opex, 201);
    }

    // Update an existing Opex record
    public function update(Request $request, $id)
    {
        $opex = Opex::find($id);
        if (is_null($opex)) {
            return response()->json(['message' => 'Opex not found'], 404);
        }
        $opex->update($request->all());
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
