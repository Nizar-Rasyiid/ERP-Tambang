<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\DetailPackage;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $index = Package::with([
            'detailpackage',
            'detailpackage.product'
        ])
        ->get();
        return response()->json($index);
    }

    public function detailindex(string $id)
    {
        $index = Package::with([
            'detailpackage',
            'detailpackage.product'
        ])
        ->where('package_id', $id)
        ->get();

        return response()->json($index);
    }

    public function detailpack(string $id)
    {
        $index = DetailPackage::with([            
            'product'
        ])
        ->where('id_detail_package', $id)
        ->get();

        return response()->json($index);
    }

    public function detailpackage(  )
    {
        $index = DetailPackage::with([            
            'product'
        ])        
        ->get();

        return response()->json($index);
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
        // $request->validate([            
        //     'package_details' => 'required|array',
        // ]); 
        $lastCode = Package::latest()->first();
        $lastCodeProd = $lastCode ? $lastCode->code_package : 1000;
        $newCodeProd = $lastCodeProd + 1;

        $package = Package::create([  
            'code_package'  => $newCodeProd,
            'package_desc'  => $request->package_desc,
            'package_sn'    => $request->package_sn,         
        ]);

        foreach ($request->package_details as $product) {
            DetailPackage::create([
                'package_id' => $package->package_id,
                'product_id' => $product['product_id'],
            ]);
        }

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
