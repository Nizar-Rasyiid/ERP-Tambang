<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opex;

class OpexController extends Controller
{
    // Get all Opex records
    public function index()
    {
        $opexes = Opex::all();
        return response()->json($opexes);
    }

    // Get a single Opex record
    public function show($id)
    {
        $opex = Opex::find($id);
        if (is_null($opex)) {
            return response()->json(['message' => 'Opex not found'], 404);
        }
        return response()->json($opex);
    }

    // Create a new Opex record
    public function store(Request $request)
    {
        $opex = Opex::create($request->all());
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
