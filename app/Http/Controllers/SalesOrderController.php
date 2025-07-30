<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\PaymentSalesOrder;
use App\Models\DetailSo;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;

class SalesOrderController extends Controller
{
    public function index()
    {
        $salesOrders = SalesOrder::with(['customer', 'employee'])->get();
        return response()->json($salesOrders);
    }

    public function show($id)
    {
        $salesOrder = SalesOrder::with(['customer', 'employee'])->find($id);
        if (is_null($salesOrder)) {
            return response()->json(['message' => 'Sales Order not found'], 404);
        }
        return response()->json($salesOrder);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sales_order_details.*.product_id' => 'required_if:product_type,product|exists:products,product_id',
            'sales_order_details.*.package_id' => 'required_if:product_type,package|exists:package,package_id',
        ]);
    
        // Ambil bulan & tahun saat ini
        $currentMonth = date('m'); // 02
        $currentYear  = date('Y'); // 2025
        $monthRoman   = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV',
            '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII',
            '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ];
    
        // Cari SO terakhir dalam bulan dan tahun yang sama
        $lastSo = SalesOrder::latest()->first();
    
        // Ambil ID terakhir & buat ID baru dengan format 2 digit
        $lastIdSo  = $lastSo ? intval(explode('/', $lastSo->code_so)[0]) : 0;
        $newIdSo   = str_pad($lastIdSo + 1, 2, '0', STR_PAD_LEFT); // Format 2 digit (00, 01, 02, ...)
    
        // Ambil Nama Customer dari tabel `customers` berdasarkan `customer_id`
        $customer = Customer::where('customer_id', $request->customer_id)->value('customer_singkatan') ?? 'Unknown';
    
        // Format kode SO: 00(ID_SO)/SO/NamaCustomer/II/2025
        $formattedCodeSo = "{$newIdSo}/SO/{$customer}/{$monthRoman[$currentMonth]}/{$currentYear}";
    
        // Buat Sales Order
        $salesOrder = SalesOrder::create([
            'customer_id'    => $request->customer_id,
            'employee_id'    => $request->employee_id,
            'code_so'        => $formattedCodeSo,
            'po_number'      => $request->po_number,
            'termin'         => $request->termin,                                    
            'sub_total'      => $request->sub_total,
            'deposit'        => $request->deposit,
            'ppn'            => $request->ppn,
            'grand_total'    => $request->grand_total,                                    
            'issue_at'       => $request->issue_at,
            'due_at'         => $request->due_at,
        ]);
    
        foreach ($request->sales_order_details as $pro) {                                                                         
            $detailData = [
                'id_so'         => $salesOrder->id_so,  
                'product_id'    => $pro['product_id'],
                'package_id'    => null,
                'product_type'  => $pro['product_type'],                                     
                'quantity'      => $pro['quantity'],                
                'quantity_left' => 0,         
                'discount'      => $pro['discount'],    
                'price'         => $pro['price'],                                
                'amount'        => $pro['amount'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ];            

            DetailSo::create($detailData);                          
        }            
    
        return response()->json([
            'message'      => 'Sales Order berhasil dibuat!',
            'sales_order'  => $detailData,            
        ], 201);
    }
    

    public function update(Request $request, $id)
    {
        try {
            // Proses update dasar
            $salesOrder = SalesOrder::find($id);
            if (is_null($salesOrder)) {
            return response()->json(['message' => 'Sales Order not found'], 404);
            }

            $salesOrder->update([
                'customer_id'    => $request->customer_id,
                'employee_id'    => $request->employee_id,                
                'po_number'      => $request->po_number,
                'termin'         => $request->termin,                                    
                'sub_total'      => $request->sub_total,
                'deposit'        => $request->deposit,
                'ppn'            => $request->ppn,
                'grand_total'    => $request->grand_total,                                    
                'issue_at'       => $request->issue_at,
                'due_at'         => $request->due_at,
            ]);

            // Proses detail
            if ($request->has('sales_order_details')) {            
            $existingDetailIds = DetailSo::where('id_so', $id)
                ->pluck('id_detail_so')->toArray();
            $processedIds = [];

            foreach ($request->sales_order_details as $detail) {                

                if (isset($detail['id_detail_so']) && in_array($detail['id_detail_so'], $existingDetailIds)) {
                    DetailSo::where('id_detail_so', $detail['id_detail_so'])->update([                        
                        'product_id'    => $detail['product_id'],
                        'package_id'    => null,
                        'product_type'  => $detail['product_type'],                                     
                        'quantity'      => $detail['quantity'],                
                        'quantity_left' => 0,         
                        'discount'      => $detail['discount'],    
                        'price'         => $detail['price'],                                
                        'amount'        => $detail['amount'],
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                    $processedIds[] = $detail['id_detail_so'];
                } else {
                    $newDetail = DetailSo::create([                        
                        'product_id'    => $detail['product_id'],
                        'package_id'    => null,
                        'product_type'  => $detail['product_type'],                                     
                        'quantity'      => $detail['quantity'],                
                        'quantity_left' => 0,         
                        'discount'      => $detail['discount'],    
                        'price'         => $detail['price'],                                
                        'amount'        => $detail['amount'],
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);           
                    if ($newDetail) {
                        $processedIds[] = $newDetail->id_detail_so;
                    }
                }
            }

            $detailsToDelete = array_diff($existingDetailIds, $processedIds);
            if (!empty($detailsToDelete)) {
                DetailSo::whereIn('id_detail_so', $detailsToDelete)->delete();
            }            
            }                        
        } catch (\Exception $e) {
            \Log::error('Sales Order Update Error: ' . $e->getMessage());
            return response()->json([
            'message' => 'Error updating Sales Order',
            'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $salesOrder = SalesOrder::find($id);
        if (is_null($salesOrder)) {
            return response()->json(['message' => 'Sales Order not found'], 404);
        }

        $salesOrder->delete();
        return response()->json(['message' => 'Sales Order deleted successfully']);
    }


    public function getAR(){                   
        $salesOrder = Invoice::with(['customer', 'paymentsales'])            
            ->get();
        
        return response()->json($salesOrder);
    }


    public function updateDeposit(Request $request, $id)
    {
        $salesOrder = Invoice::findOrFail($request->id_invoice);
        if (is_null($salesOrder)) {
            return response()->json(['message' => 'Sales Order not found'], 404);
        }
        $currentMonth = date('m'); // 02
        $currentYear  = date('Y'); // 2025
        $monthRoman   = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV',
            '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII',
            '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ];
    
        // Cari invoice terakhir dalam bulan dan tahun yang sama
        $lastInvoice = Invoice::latest()->first();
    
        // Ambil ID terakhir & buat ID baru dengan format 2 digit
        $lastIdInvoice = $lastInvoice ? intval(explode('/', $lastInvoice->code_invoice)[0]) : 0;
        $newIdInvoice  = str_pad($lastIdInvoice + 1, 2, '0', STR_PAD_LEFT); // Format 2 digit (00, 01, 02, ...)
    
        // Format kode Invoice: 00ID/INV/II/2025
        $formattedCodeInvoice = "{$newIdInvoice}/PAY/{$monthRoman[$currentMonth]}/{$currentYear}"; 

        $salesOrder->update([
            'deposit' => $request->deposit,
            'status_payment'    => $request->status_payment,
        ]);

        $payment = PaymentSalesOrder::create([
            'id_invoice'         => $request->id_invoice,
            'payment_method'=> $request->payment_method,
            'code_paymentso'=> $formattedCodeInvoice,
            'price'         => $request->deposit,
            'issue_at'      => $request->issue_at,
            'due_at'        => $request->due_at,   
        ]);

        return response()->json(['message' => 'Deposit updated successfully']);
    }

    public function resetPrice(Request $request, $id){
        $purchaseOrder = Invoice::findOrFail($id);
        $purchaseOrder->update([
            'deposit'        => $request->deposit,
            'status_payment' => 'unpaid',
        ]);

        $payment = PaymentSalesOrder::where('id_invoice', $id)->delete();
        return response()->json($purchaseOrder);
    }

    public function monthlySales()
    {
        // Ambil data penjualan per bulan
        $monthlySales = SalesOrder::select(
            DB::raw('YEAR(issue_at) as year'),
            DB::raw('MONTH(issue_at) as month'),
            DB::raw('SUM(grand_total) as total_sales')
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
}
