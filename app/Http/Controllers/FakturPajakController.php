<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FakturPajak;

class FakturPajakController extends Controller
{
    public function index()
    {
        $fakturpajaks = FakturPajak::with(['customer', 'invoice', 'so'])->get();
        return response()->json($fakturpajaks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_invoice' => 'required',
            'customer_id' => 'required',
            'code_faktur_pajak' => 'required',
        ]);

        FakturPajak::create([
            'id_so' => $request->id_so,
            'id_invoice' => $request->id_invoice,
            'customer_id' => $request->customer_id,
            'code_faktur_pajak' => $request->code_faktur_pajak,
        ]);
        return response()->json([
            'message' => 'Berhasil Membuat Faktur pajak',
        ]);
    }

    public function show($id)
    {
        $fakturpajak = FakturPajak::find($id);
        if (is_null($fakturpajak)) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        return response()->json($fakturpajak);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_invoice' => 'required',
            'customer_id' => 'required',
            'code_faktur_pajak' => 'required',
        ]);

        $fakturpajak = FakturPajak::find($id);
        if (is_null($fakturpajak)) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $fakturpajak->update($request->all());
        return response()->json($fakturpajak);
    }

    public function destroy($id)
    {
        $fakturpajak = FakturPajak::find($id);
        if (is_null($fakturpajak)) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $fakturpajak->delete();
        return response()->json(null, 204);
    }
}
