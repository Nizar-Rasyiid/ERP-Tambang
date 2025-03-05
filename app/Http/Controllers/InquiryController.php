<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\DetailInquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index(){
        $inquiry = Inquiry::with(['customer', 'employee'])->get();
        return response()->json($inquiry);
    }

    public function store(Request $request)
    {
        $request->validate([
            'inquiry_details' => 'required|array'
        ]);

        $last = Inquiry::latest()->first();
        $lastId = $last ? $last->code_inquiry : 1000;
        $newId = $lastId + 1;   

        $inquiry = Inquiry::create([
            'customer_id' => $request->customer_id,
            'employee_id' => $request->employee_id,
            'total_tax' => $request->total_tax,
            'code_inquiry' => $newId,
            'sub_total' => 0,
            'ppn' => 0,
            'grand_total' => 0,
            'issue_at' => $request->issue_at,
            'due_at' => $request->due_at,            
        ]);

        $product = [];
        $sub_total = 0;

        foreach($request->inquiry_details as $key => $pro){  
            $line_total = $pro['price'] * $pro['quantity'];

            $sub_total += $line_total;

            $detailso = DetailInquiry::create([
            'id_inquiry' => $inquiry->id_inquiry,                
            'product_id' => $pro['product_id'],
            'quantity' => $pro['quantity'],
            'price' => $pro['price'],
            'created_at' => now(),
            'updated_at' => now(),
            ]);
        }             
    
        // ✅ Hitung PPN (11% dari sub_total)
        $ppn = $sub_total * 0.11;
    
        // ✅ Hitung Grand Total
        $grand_total = $sub_total  + $ppn;        

        // Buat Sales Order dengan PPN & Grand Total
        $newSalesOrder = Inquiry::where('id_inquiry', $inquiry->id_inquiry)->update([                        
            'sub_total'       => $sub_total,
            'ppn'             => $ppn, // ✅ PPN otomatis dihitung
            'grand_total'     => $grand_total, // ✅ Grand Total otomatis dihitung
        ]);  

        return response()->json([
            'message' => 'Success to Save Inquiry',
            'inquiry' => $inquiry
        ]);
    }
}
