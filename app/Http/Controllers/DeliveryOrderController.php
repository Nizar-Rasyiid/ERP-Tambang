<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryOrder;
use App\Models\SalesOrder;
use App\Models\DetailDo;
use App\Models\StockHistory;
use App\Models\DetailSo;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;

class DeliveryOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deliveryOrder = DeliveryOrder::with([
            'customer',
            'salesorder',
            'point',
            'detailDo',
            'detailDo.product',            
            ])->get();
        return response()->json($deliveryOrder);
    }

    /**
     * Search for customers by name.
     */

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'delivery_order_details' => 'required|array',
        ]);
    
        // Ambil bulan & tahun saat ini
        $currentMonth = date('m'); // 02
        $currentYear  = date('Y'); // 2025
        $monthRoman   = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV',
            '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII',
            '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ];
    
        $lastDo = DeliveryOrder::latest()->first();
    
        $lastIdDo = $lastDo ? intval(explode('/', $lastDo->code_do)[0]) : 0;
        $newIdDo  = str_pad($lastIdDo + 1, 2, '0', STR_PAD_LEFT); // Format 2 digit (00, 01, 02, ...)
    
        // Format kode DO: 00ID/DO/II/2025
        $formattedCodeDo = "{$newIdDo}/DO/{$monthRoman[$currentMonth]}/{$currentYear}";

        // buat data untuk table deliveryorder 
        $deliveryOrder = DeliveryOrder::create([
            'customer_id'     => $request->customer_id,
            'employee_id'     => $request->employee_id,
            'id_so'           => $request->id_so,
            'id_customer_point' => $request->id_customer_point,
            'code_do'         => $formattedCodeDo,
            'sub_total'       => $request->sub_total,          
            'ppn'             => $request->ppn,
            'issue_at'        => $request->issue_at,
            'due_at'          => $request->due_at,
        ]);

        $allDelivered = true;

        foreach ($request->delivery_order_details as $do) {
            $stock = StockHistory::with('product')
                    ->where('product_id', $do['product_id'])
                    ->where('quantity_left', '>=', $do['quantity_left'])
                    ->first();

            if ($stock) {
                $detailSo = DetailSo::findOrFail($do['id_detail_so']);
                $product = Product::findOrFail($do['product_id']);

                if ($do['quantity_left'] > 0) {
                    $detailSo->increment('quantity_left', $do['quantity_left']);
                    $product->decrement('product_stock', $do['quantity_left']);
                    $stock->decrement('quantity_left', $do['quantity_left']);

                    DetailDo::create([
                        'id_do'         => $deliveryOrder->id_do,
                        'id_po'         => $stock->id_po,
                        'id_detail_po'  => $stock->id_detail_po,
                        'id_detail_so'  => $do['id_detail_so'],
                        'product_id'    => $do['product_id'],                        
                        'quantity'      => $do['quantity_left'],
                        'price'         => $do['price'],                                                
                    ]);
                    if ($detailSo->quantity == $detailSo->quantity_left) {
                        $detailSo->update([
                            'has_do'    => 1,
                        ]);
                    }
                }                  
            }                          
        }    
        
        $detail = DetailSo::where('id_so', $request->id_so)->get();
        $allDelivered = $detail->every(function($item) {
            return $item->has_do == 1;
        });        

        if ($allDelivered) {
            $has_do = SalesOrder::findOrFail($request->id_so)->update([
                'has_do' => 1,
            ]);
        }        

        return response()->json([
            'message'       => 'Delivery Order berhasil dibuat!',
            'delivery_order'=> $deliveryOrder,                          
        ], 201);

    }    

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'delivery_order_details' => 'required|array',
    //     ]);
    
    //     // Ambil bulan & tahun saat ini
    //     $currentMonth = date('m'); // 02
    //     $currentYear  = date('Y'); // 2025
    //     $monthRoman   = [
    //         '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV',
    //         '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII',
    //         '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
    //     ];
    
    //     $lastDo = DeliveryOrder::whereYear('created_at', $currentYear)
    //                            ->whereMonth('created_at', $currentMonth)
    //                            ->latest('id_do')
    //                            ->first();
    
    //     $lastIdDo = $lastDo ? intval(explode('/', $lastDo->code_do)[0]) : 0;
    //     $newIdDo  = str_pad($lastIdDo + 1, 2, '0', STR_PAD_LEFT); // Format 2 digit (00, 01, 02, ...)
    
    //     // Format kode DO: 00ID/DO/II/2025
    //     $formattedCodeDo = "{$newIdDo}/DO/{$monthRoman[$currentMonth]}/{$currentYear}";
    
    //     // Buat Delivery Order
    //     $deliveryOrder = DeliveryOrder::create([
    //         'customer_id'     => $request->customer_id,
    //         'employee_id'     => $request->employee_id,
    //         'id_so'           => $request->id_so,
    //         'id_customer_point' => $request->id_customer_point,
    //         'code_do'         => $formattedCodeDo,
    //         'sub_total'       => 0,          
    //         'issue_at'        => $request->issue_at,
    //         'due_at'          => $request->due_at,
    //     ]);
    
    //     $sub_total = 0;
    //     $allDelivered = true;
    //     foreach ($request->delivery_order_details as $pro) { 

    //         $detailSo = DetailSo::findOrFail($pro['id_detail_so']);
    //         $product = Product::findOrFail($pro['product_id']);

    //         if ($pro['quantity_left'] > $product->product_stock) {
    //             $pro['quantity_left'] = $product->product_stock;
    //         } elseif ($pro['quantity_left'] > $pro['quantity'] && $product->product_stock >= $pro['quantity']) {
    //             $pro['quantity_left'] = $pro['quantity'];
    //         }

    //         if ($pro['quantity_left'] > 0) {
    //             $detailSo->increment('quantity_left', $pro['quantity_left']);

    //             // Kurangi stok produk
    //             $product->decrement('product_stock', $pro['quantity_left']);

    //             // Simpan data ke DetailDo
    //             DetailDo::create([
    //                 'id_do'         => $deliveryOrder->id_do,
    //                 'code_do'       => $deliveryOrder->code_do,
    //                 'product_id'    => $pro['product_id'],
    //                 'quantity'      => $pro['quantity_left'],
    //                 'price'         => $pro['price'],
    //                 'created_at'    => now(),
    //                 'updated_at'    => now(),
    //             ]);

    //             // Hitung sub total
    //             $sub_total += $pro['price'] * $pro['quantity_left'];
    //         }                    
    //         //bikin total price untuk sales-ordernya
    //         $line_total = $pro['price'] * $pro['quantity_left'];
    //         $sub_total += $line_total;            
    //     }        

    //     foreach($request->delivery_order_details as $det)
    //     {
    //         $detailSo = DetailSo::findOrFail($det['id_detail_so']);
    //         if ($pro['quantity'] == $detailSo->quantity_left) {
    //             DetailSo::findOrFail($det['id_detail_so'])->update([
    //                 'has_do' => 1,
    //             ]);
    //         }            
    //     } 

    //     $detailSoList = DetailSo::where('id_so', $request->id_so)->get();

    //     foreach ($detailSoList as $pro) {
    //         if ($pro->quantity != $pro->quantity_left) {
    //             $allDelivered = false;
    //             break;
    //         }
    //     }

    //     if ($allDelivered) {
    //         DetailSo::findOrFail($det['id_detail_so'])->update([
    //             'has_do' => 1,
    //         ]);
    //     }               
    //     $detailSo = DetailSo::where('id_so', $request->id_so)->get();              

    //     foreach($detailSo as $pro)
    //     {
    //         if ($pro->quantity != $pro->quantity_left) {
    //             $allDelivered = false; 
    //             break;                       
    //         }
    //     }

    //     if ($allDelivered) {
    //         $id_so = $request->id_so;
    //         $has_do = SalesOrder::findOrFail($id_so)->update([
    //             'has_do' => 1,
    //         ]);
    //     }
    //     $deliveryOrder->update(['sub_total' => $sub_total]);
        
    //     return response()->json([
    //         'message'       => 'Delivery Order berhasil dibuat!',
    //         'delivery_order'=> $deliveryOrder,                 
    //     ], 201);
    // }    
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $deliveryOrder = DeliveryOrder::with(
            [
                'customer',
                'employee',
                'point',                                
            ]
            )->find($id);
        return response()->json($deliveryOrder);
    }

    public function SoShow(string $id)
    {
        $salesOrder = DeliveryOrder::with('customer', 'employee')->where('id_so', $id)->get();
        return response()->json($salesOrder);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'id_customer'     => 'required|exists:customers,id_customer',
            'id_employee'     => 'required|exists:employees,id_employee',
            'id_bank_account' => 'required|exists:bank_accounts,id_bank_account',
            'id_po'           => 'required|exists:purchaseorders,id_po',
            'issued_at'       => 'required|date',
            'due_at'          => 'required|date',
        ]);

        $deliveryOrder = DeliveryOrder::findOrFail($id);
        $deliveryOrder->update($request->all());

        return response()->json([
            'message'       => 'Delivery Order berhasil diperbarui!',
            'delivery_order'=> $deliveryOrder
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deliveryOrder = DeliveryOrder::findOrFail($id);
        $deliveryOrder->delete();

        return response()->json([
            'message' => 'Delivery Order berhasil dihapus!'
        ]);
    }
}
