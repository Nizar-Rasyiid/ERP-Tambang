<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DetailQuatation;

class DetailQuatationController extends Controller
{
    public function index(){
        $detailQuatation = DetailQuatation::with(['product', 'quo', 'quo.customer'])->get();
        return response()->json($detailQuatation);
    }

    public function show(string $id){
        $detailQuatation = DetailQuatation::with(
            [
                'product', 
                'quo',
                'quo.customer',
            ])
            ->where('id_quatation', $id)
            ->get();

        return response()->json($detailQuatation);
    }
}
