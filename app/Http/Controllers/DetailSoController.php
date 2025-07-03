<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DetailSo;
use App\Models\DetailDo;

class DetailSoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detailSo = DetailSo::with(
            ['product', 'salesorders', 'salesorders.customer', 'package'])
            ->get();
        return response()->json($detailSo);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        $detailSo = DetailSo::with('product', 'package')
            ->where('id_so', $id)->get();    
                
        return response()->json($detailSo);
    }

    public function DoShow(string $id)
    {
        $detailDo = DetailDo::with('product', 'do')
            ->where('id_do', $id)
            ->get();
            
        return response()->json($detailDo);
    }

    public function DetailDO(){
        $detailDo = DetailDo::with(['product', 'do', 'do.customer', 'do.salesorder'])->get();
        return response()->json($detailDo);
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
