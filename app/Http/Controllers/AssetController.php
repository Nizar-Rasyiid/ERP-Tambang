<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = Asset::with('vendor')->get();
        return response()->json($assets);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not needed for API
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {        
        $lastCode = Asset::latest()->first();
        $lastCode = $lastCode ? $lastCode->code : 1000;
        $newCode = $lastCode + 1;

        $assets = Asset::create([
            'vendor_id' => $request->vendor_id,
            'assets_code' => $newCode,
            'assets_name' => $request->assets_name,
            'assets_price' => $request->price,
            'assets_life' => $request->assets_life, 
            'issue_at' => $request->issue_at,
            'due_at' => $request->due_at,
        ]);

        return response()->json($assets);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $asset = Asset::with('vendor')->where('asset_id', $id)->get();
        return response()->json($asset);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not needed for API
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {        
        $asset = Asset::findOrFail($id);
        $asset->update([
            'vendor_id' => $request->vendor_id,            
            'assets_name' => $request->assets_name,
            'assets_price' => $request->price,
            'assets_life' => $request->assets_life,            
            'issue_at' => $request->issue_at,
            'due_at' => $request->due_at,
        ]);
        return response()->json($asset);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();
        return response()->json(null, 204);
    }
}
