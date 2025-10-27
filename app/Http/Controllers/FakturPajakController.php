<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\FakturPajak;
use App\Models\Invoice;

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
        try {
            DB::beginTransaction();
            FakturPajak::create([
                'id_so' => $request->id_so,
                'id_invoice' => $request->id_invoice,
                'customer_id' => $request->customer_id,
                'code_faktur_pajak' => $request->code_faktur_pajak,
            ]);

            Invoice::findOrFail($request->id_invoice)->update([
                'has_faktur'    => 1,
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Berhasil Membuat Faktur pajak',
            ]);
        } catch (\Exception $e) {
            DB::rollbak();
            return response()->json([
                'message'   => 'error',
                'error'     => $e->getMessage(),
            ]);
        }                
    }

    public function show($id)
    {
        $fakturpajak = FakturPajak::with([
            'invoice',
            'invoice',
            'so.customer'
        ])
        ->where('id', $id)
        ->get();      
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

        $oldInvoiceId = $fakturpajak->id_invoice;
        $newInvoiceId = $request->id_invoice;

        if ($oldInvoiceId != $newInvoiceId) {
            $oldInvoice = Invoice::find($oldInvoiceId);            
            if ($oldInvoice) {
                $oldInvoice->update(['has_faktur' => 0]);
            }
            $newInvoice = Invoice::find($newInvoiceId);
            if ($newInvoice) {
                $newInvoice->update(['has_faktur' => 1]);
            }            
        }

        $fakturpajak->update([
            'id_so'         => $request->id_so,
            'id_invoice'    => $request->id_invoice,
            'customer_id'   => $request->customer_id,
            'code_faktur_pajak' => $request->code_faktur_pajak,            
        ]);
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
