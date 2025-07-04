<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Customer;
use App\Models\CustomerPoint;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer = Customer::all();
        return response()->json($customer);
    }

    public function searchCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $customers = Customer::where('customer_name', 'like', '%' . $request->name . '%')->get();

        return response()->json($customers);
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
        $lastCode = Customer::latest()->first();
        $lastCode = $lastCode ? $lastCode->customer_code : 1000;
        $newCode = $lastCode + 1;

        $customer = Customer::create([
            'customer_code'    => $newCode,            
            'customer_name'    => $request->customer_name,
            'customer_phone'   => $request->customer_phone,            
            'customer_singkatan' => $request->customer_singkatan,
            'customer_email'   => $request->customer_email,
            'customer_address' => $request->customer_address,
            'customer_npwp' => $request->customer_npwp,
            'customer_contact' => $request->customer_contact,
        ]);

        foreach ($request->customer_details as $pro) {                                                                
            CustomerPoint::create([
                'customer_id'=> $customer->customer_id,                
                'point' => $pro['point'],
                'alamat'   => $pro['alamat'],                
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }  

        return response()->json($customer, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::where('customer_id', $id)->first();
        return response()->json($customer);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function cusPoint(string $id)
    {
        $cuspoint = CustomerPoint::where('customer_id', $id)->get();
        return response()->json($cuspoint);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {        

        $customer = Customer::findOrFail($id);
        $customer->update([            
            'customer_name'    => $request->customer_name,
            'customer_phone'   => $request->customer_phone,            
            'customer_singkatan' => $request->customer_singkatan,
            'customer_email'   => $request->customer_email,
            'customer_address' => $request->customer_address,
            'customer_npwp' => $request->customer_npwp,
            'customer_contact' => $request->customer_contact,
        ]);

        $existingDetails = CustomerPoint::where('customer_id', $id)->pluck('id_customer_point')->toArray();
        $ids = [];
        
        foreach ($request->customer_details as $pro) {                                                                
            if (isset($pro['id_customer_point'])) {
                CustomerPoint::findOrFail($pro['id_customer_point'])->update([
                    'customer_id'=> $customer->customer_id,                
                    'point' => $pro['point'],
                    'alamat'   => $pro['alamat'],                
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $ids[] = $pro['id_customer_point'];
            }else{
                CustomerPoint::create([
                    'customer_id'=> $customer->customer_id,                
                    'point' => $pro['point'],
                    'alamat'   => $pro['alamat'],                
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } 
        $detailsDelete = array_diff($existingDetails, $ids);
        if (!empty($detailsDelete)) {
            CustomerPoint::whereIn('id_customer_point', $detailsDelete)->delete();
        } 

        return response()->json($customer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(null, 204);
    }
}
