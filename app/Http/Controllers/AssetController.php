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
        $assets = Asset::all();
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
        $request->validate([
            'id_asset_type' => 'required|integer',
            'code' => 'required|string',
            'name' => 'required|string',
            'qty' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        $asset = Asset::create($request->all());
        return response()->json($asset, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $asset = Asset::findOrFail($id);
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
        $request->validate([
            'id_asset_type' => 'integer',
            'code' => 'string',
            'name' => 'string',
            'qty' => 'integer',
            'status' => 'boolean',
        ]);

        $asset = Asset::findOrFail($id);
        $asset->update($request->all());
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
