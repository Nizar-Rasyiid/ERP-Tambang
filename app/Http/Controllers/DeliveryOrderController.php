<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\DeliveryOrder;
use App\Models\SalesOrder;
use App\Models\DetailDo;
use App\Models\StockHistory;
use App\Models\DetailSo;
use App\Models\DetailPo;
use App\Models\Customer;
use App\Models\DetailPackage;
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
        try {
            DB::beginTransaction();
            //Ambil bulan & tahun saat ini
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

            foreach ( $request->delivery_order_details as $do)
            {
                $product = Product::findOrFail($do['product_id']);
                $detailSo = DetailSo::findOrFail($do['id_detail_so']);                

                if ($do['is_package'] == 1) {                                        
                    $detailsPackages = DetailPackage::where('product_id', $do['product_id'])->get();                    
                    $packageStockProduct = $detailsPackages->firstWhere('used_for_stock', true);
                    $product_package = Product::findOrFail($packageStockProduct->products);
                    $totalQuantity = $detailsPackages->sum('quantity') * $do['quantity'];
                    //variable for check is this product used for stock;
                    $check_product_for_package = false; 

                    // decrement the stock
                    $stock = StockHistory::where('product_id', $packageStockProduct->products)
                        ->where('quantity_left', '>=', $totalQuantity)
                        ->first();
                    
                    if ($stock && $do['quantity_left'] > 0) {
                        $detailSo->increment('quantity_left', $do['quantity_left']);
                        $product_package->decrement('product_stock', $totalQuantity);
                        $stock->decrement('quantity_left', $totalQuantity);
                        $check_product_for_package = true;
                        $product->update([
                            'has_used' => true,
                        ]);
                    }else {
                        return response()->json([
                            'message' => 'quantity tidak mencukupi'
                        ]);
                        return;
                    }

                    if ($check_product_for_package) {
                        DetailDo::create([
                            'id_do'         => $deliveryOrder->id_do,
                            'id_po'         => $stock->id_po,
                            'id_detail_po'  => $stock->id_detail_po,
                            'id_detail_so'  => $do['id_detail_so'],
                            'product_id'    => $do['product_id'],
                            'quantity'      => $do['quantity'],
                            'quantity_left' => $detailSo->quantity - $do['quantity_left'],
                            'price'         => $do['price'], // atau harga per item package
                        ]);
                        
                        $detailSo->update([
                            'has_do'    => 1,
                        ]); 
                    }                   
                }else {
                    $stock = StockHistory::where('product_id', $do['product_id'])
                        ->where('quantity_left', '>=', $do['quantity_left'])
                        ->first();
                    
                    if (!$stock) {
                        $stock = DetailPo::where('product_id', $do['product_id'])
                        ->where('quantity_left', '>=', $do['quantity_left'])
                        ->first();
                    }
                    
                    if ($stock && $do['quantity_left'] > 0) {
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
                            'quantity_left' => $detailSo->quantity - $do['quantity_left'],
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
            DB::commit();
            return response()->json([
                'message'       => 'Delivery Order berhasil dibuat!',
                'delivery_order'=> $deliveryOrder,                          
            ], 201);
        } catch (\Excaption $e) {
            DB::rollback();
            return response()->json([
                'message'       => 'Delivery Order berhasil dibuat!',
                'error'         => $e->getMessage()                          
            ], 403);
        }                        

    }    
    

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
