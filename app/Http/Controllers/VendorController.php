<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::all();
        return response()->json($vendors);
    }

    public function create()
    {
        // This method is not typically used in an API context
        return response()->json(['message' => 'Method not allowed'], 405);
    }

    public function search(Request $request)
    {
        $request->validate([
            'vendor_name' => 'required'
        ]);

        $vendors = Vendor::where('vendor_name', 'like', '%' . $request->vendor_name . '%')->get();

        if ($vendors->isEmpty()) {
            return response()->json(['error' => 'No vendors found'], 404);
        }

        return response()->json($vendors);
    }
    public function store(Request $request)
    {        
        $vendor = Vendor::create([
            'vendor_name' => $request->vendor_name,            
            'vendor_email' => $request->vendor_email,
            'vendor_phone' => $request->vendor_phone,
            'vendor_address' => $request->vendor_address,
            'vendor_singkatan' => $request->vendor_singkatan,              
        ]);        

        return response()->json(['success' => 'Vendor created successfully.', 'vendor' => $vendor], 201);
    }

    public function show($id)
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['error' => 'Vendor not found'], 404);
        }

        return response()->json($vendor);
    }

    public function edit($id)
    {
        // This method is not typically used in an API context
        return response()->json(['message' => 'Method not allowed'], 405);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'account_name' => 'required',
            'vendor_name' => 'required',
            'vendor_type' => 'required',
            'vendor_email' => 'required|email',
            'vendor_phone' => 'required',
            'vendor_address' => 'required',
            'tax_number' => 'required',
        ]);

        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['error' => 'Vendor not found'], 404);
        }

        $vendor->update($request->all());

        return response()->json(['success' => 'Vendor updated successfully.', 'vendor' => $vendor]);
    }

    public function destroy($id)
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['error' => 'Vendor not found'], 404);
        }

        $vendor->delete();

        return response()->json(['success' => 'Vendor deleted successfully.']);
    }
}
