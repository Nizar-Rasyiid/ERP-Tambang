<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\DetailQuatation;
use App\Models\Quatation;

class QuatationController extends Controller
{
    public function index()
    {
        $quatation = Quatation::with([
            'customer', 
            'detailQuo',
            'detailQuo.product'            
            ])->get();
        return response()->json($quatation);
    }

    public function show(string $id){
        $quatation = Quatation::with(['customer', 'employee'])
            ->where('id_quatation', $id)
            ->get();

        return response()->json($quatation);
    }

    public function store(Request $request)
    {        
        // Ambil bulan & tahun saat ini
        $currentMonth = date('m'); // 02
        $currentYear  = date('Y'); // 2025
        $monthRoman   = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV',
            '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII',
            '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ];
    
        // Cari invoice terakhir dalam bulan dan tahun yang sama
        $lastQuatation = Quatation::whereYear('created_at', $currentYear)
                              ->whereMonth('created_at', $currentMonth)
                              ->latest('id_quatation')
                              ->first();

        // Ambil ID terakhir & buat ID baru dengan format 2 digit
        $lastIdQuatation = $lastQuatation ? intval(explode('/', $lastQuatation->code_quatation)[0]) : 0;
        $newIdQuatation  = str_pad($lastIdQuatation + 1, 2, '0', STR_PAD_LEFT); // Format 2 digit (00, 01, 02, ...)
        
        // Format kode Invoice: 00ID/INV/II/2025
        $formattedCodeQuatation = "{$newIdQuatation}/QUO/{$monthRoman[$currentMonth]}/{$currentYear}";

        $quatation = Quatation::create([            
            'customer_id' => $request->customer_id,
            'employee_id' => $request->employee_id,
            'termin' => $request->termin,
            'code_quatation' => $formattedCodeQuatation,
            'sub_total' => $request->sub_total,
            'ppn' => $request->ppn,
            'grand_total' => $request->grand_total,            
            'issue_at' => $request->issue_at,
            'due_at' => $request->due_at                        
        ]);

        $product = [];        

        foreach($request->inquiry_details as $key => $pro){              

            $detailso = DetailQuatation::create([
            'id_quatation' => $quatation->id_quatation,                
            'product_id' => $pro['product_id'],
            'quantity' => $pro['quantity'],
            'discount' => $pro['discount'],
            'price' => $pro['price'],
            'amount' => $pro['amount'],
            'created_at' => now(),
            'updated_at' => now(),
            ]);

        } 
        
        return response()->json([
            'message' => 'Successfully to Save Quatation',
            'quatation' => $quatation,
            'code_quatation' => $formattedCodeQuatation,            
        ]);
    }
    public function put(Request $request, $id){
        $quatation = Quatation::findOrFail($id);
        
        $quatation->update([            
            'customer_id' => $request->customer_id,
            'employee_id' => $request->employee_id,
            'termin' => $request->termin,
            'code_quatation' => $request->code_quatation,
            'sub_total' => $request->sub_total,
            'issue_at' => $request->issue_at,
            'due_at' => $request->due_at                        
        ]);
    
        // Get all current detail IDs
        $existingDetailIds = DetailQuatation::where('id_quatation', $id)->pluck('id_detail_quatation')->toArray();
        
        // Track which IDs we've processed 
        $processedIds = [];
        
        foreach($request->inquiry_details as $key => $pro){
            if(isset($pro['id_detail_quatation'])) {
                // Update existing item
                DetailQuatation::findOrFail($pro['id_detail_quatation'])->update([
                    'product_id' => $pro['product_id'],
                    'quantity' => $pro['quantity'],
                    'price' => $pro['price'],
                    'discount' => $pro['discount'],
                    'amount' => $pro['amount'],
                    'updated_at' => now(),
                ]);
                
                $processedIds[] = $pro['id_detail_quatation'];
            } else {
                // Create new item
                DetailQuatation::create([
                    'id_quatation' => $id,
                    'product_id' => $pro['product_id'],
                    'quantity' => $pro['quantity'],
                    'price' => $pro['price'],
                    'discount' => $pro['discount'],
                    'amount' => $pro['amount'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Delete details that weren't in the request (they were removed)
        $detailsToDelete = array_diff($existingDetailIds, $processedIds);
        if (!empty($detailsToDelete)) {
            DetailQuatation::whereIn('id_detail_quatation', $detailsToDelete)->delete();
        }
        
        return response()->json([
            'message' => 'Successfully to Update Quatation',
            'id_quatation' => $quatation->id_quatation,
        ]);
    }

    public function monthlyQuo() {

        $monthlySales = Quatation::select(
            DB::raw('YEAR(issue_at) as year'),
            DB::raw('MONTH(issue_at) as month'),
            DB::raw('SUM(sub_total) as total_sales')
        )
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        // Format data untuk response
        $formattedSales = $monthlySales->map(function ($item) {
            return [
                'year' => $item->year,
                'month' => $item->month,
                'month_name' => date('F', mktime(0, 0, 0, $item->month, 10)), // Nama bulan
                'total_sales' => (float) $item->total_sales, // Pastikan nilai numerik
            ];
        });

        return response()->json($formattedSales);
    }
    public function editPPn(Request $request, string $id){
        $sub_total = $request->sub_total;
        $ppns = $request->ppn;
        
        $ppn = 0;
        $grand_total = 0;
        if ($ppns != 0) {            
            $grand_total = $sub_total;
        }else{
            $ppn = $sub_total * 0.11;
            $grand_total = $sub_total + $ppn;   
        }

        $purchaseOrder = Quatation::findOrFail($id)->update([
            'ppn' => $ppn,
            'grand_total' => $grand_total,
        ]);

        return response()->json($purchaseOrder);
    }
    
    public function destroy($id)
    {
        $quatation = Quatation::findOrFail($id);
        $quatation->delete();

        return response()->json([
            'message' => 'Quatation deleted successfully',
        ]);
    }
}
