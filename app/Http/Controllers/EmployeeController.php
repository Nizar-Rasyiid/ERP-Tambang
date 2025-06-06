<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // This method is not needed for API
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([            
            'employee_name'     => 'required|string|max:255',
            'employee_phone'    => 'required|integer',
            'employee_email'    => 'required|string|email|max:255|unique:employees',
            'employee_address'  => 'required|string|max:255',
            'employee_nik'      => 'required|integer',
            'employee_end_contract' => 'date',
            'employee_position' => 'required|string|max:255',
        ]);
        $lastCode = Employee::latest()->first();
        $lastCode = $lastCode ? $lastCode->employee_code : 1000;
        $newCode = $lastCode + 1;

        $employee = Employee::create([
            'employee_code'     => $newCode,
            'employee_name'     => $request->employee_name,
            'employee_phone'    => $request->employee_phone,
            'employee_email'    => $request->employee_email,
            'employee_address'  => $request->employee_address,
            'employee_salary'   => $request->employee_salary,
            'employee_end_contract' => now(),
            'employee_nik'      => $request->employee_nik,
            'employee_position' => $request->employee_position,
            'bpjs_kesehatan'    => $request->bpjs_kesehatan,
            'bpjs_ketenagakerjaan' => $request->bpjs_ketenagakerjaan,
        ]);

        $user = User::create([
            'employee_id'       => $employee->employee_id,
            'name'              => $employee->employee_name,
            'email'             => $employee->employee_email,
            'password'          => $request->password,
        ]);

        return response()->json([
            'message' => 'Employee Berhasil Dibuat',
            'Employee' => $employee
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::findOrFail($id);
        return response()->json($employee);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // This method is not needed for API
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->update([            
            'employee_name'     => $request->employee_name,
            'employee_phone'    => $request->employee_phone,
            'employee_email'    => $request->employee_email,
            'employee_address'  => $request->employee_address,
            'employee_salary'   => $request->employee_salary,
            'employee_end_contract' => now(),
            'employee_nik'      => $request->employee_nik,
            'employee_position' => $request->employee_position,
            'bpjs_kesehatan'    => $request->bpjs_kesehatan,
            'bpjs_ketenagakerjaan' => $request->bpjs_ketenagakerjaan,
        ]);



        return response()->json($employee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json(null, 204);
    }
}
