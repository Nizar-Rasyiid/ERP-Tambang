<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    public function index()
    {
        $paymentTypes = PaymentType::all();
        return response()->json($paymentTypes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $paymentType = PaymentType::create($request->all());
        return response()->json($paymentType, 201);
    }

    public function show($id)
    {
        $paymentType = PaymentType::findOrFail($id);
        return response()->json($paymentType);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'payment_type' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|boolean',
        ]);

        $paymentType = PaymentType::findOrFail($id);
        $paymentType->update($request->all());
        return response()->json($paymentType);
    }

    public function destroy($id)
    {
        $paymentType = PaymentType::findOrFail($id);
        $paymentType->delete();
        return response()->json(null, 204);
    }
}
