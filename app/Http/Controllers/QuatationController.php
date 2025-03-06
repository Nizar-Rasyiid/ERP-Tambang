<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DetailQuatation;
use App\Models\Quatation;

class QuatationController extends Controller
{
    public function index()
    {
        $quatation = Quatation::with(['customer', 'employee'])->get();
        return response()->json($quatation);
    }

    public function show(string $id){
        $quatation = Quatation::with(['customer', 'employee'])
            ->where('id_quatation', $id)
            ->get();

        return response()->json($quatation);
    }

    public function store(Request $request)
    {
        $last = Quatation::latest()->first();
        $lastId = $last ? $last->code_quatation : 1000;
        $newId = $lastId + 1; 

        $quatation = Quatation::create([            
            'customer_id' => $request->customer_id,
            'employee_id' => $request->employee_id,
            'termin' => $request->termin,
            'code_quatation' => $newId,
            'sub_total' => $request->sub_total,
            'issue_at' => $request->issue_at,
            'due_at' => $request->due_at                        
        ]);

        $product = [];        

        foreach($request->inquiry_details as $key => $pro){              

            $detailso = DetailQuatation::create([
            'id_quatation' => $quatation->id_quatation,                
            'product_id' => $pro['product_id'],
            'quantity' => $pro['quantity'],
            'price' => $pro['price'],
            'amount' => $pro['amount'],
            'created_at' => now(),
            'updated_at' => now(),
            ]);

        } 
        
        return response()->json([
            'message' => 'Successfully to Save Quatation',
            'quatation' => $quatation,
            'detail_quatation' => $detailso,
        ]);
    }
}
