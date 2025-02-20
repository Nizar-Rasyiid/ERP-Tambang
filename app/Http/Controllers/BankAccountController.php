<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    protected $table = 'bank_accounts';
    protected $primaryKey = 'id_bank_account';

    public function index()
    {
        $bankAccounts = BankAccount::all();
        return response()->json($bankAccounts);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $bankAccount = BankAccount::create($request->all());
        return response()->json($bankAccount, 201);
    }

    public function show($id)
    {
        $bankAccount = BankAccount::find($id);
        if (is_null($bankAccount)) {
            return response()->json(['message' => 'Bank Account not found'], 404);
        }
        return response()->json($bankAccount);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $bankAccount = BankAccount::find($id);
        if (is_null($bankAccount)) {
            return response()->json(['message' => 'Bank Account not found'], 404);
        }
        $bankAccount->update($request->all());
        return response()->json($bankAccount);
    }

    public function destroy($id)
    {
        $bankAccount = BankAccount::find($id);
        if (is_null($bankAccount)) {
            return response()->json(['message' => 'Bank Account not found'], 404);
        }
        $bankAccount->delete();
        return response()->json(null, 204);
    }
}
