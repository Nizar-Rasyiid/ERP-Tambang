<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\DetailPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * Search for products by brand.
     */
    public function search(Request $request)
    {
        // Validate the search query
        $query = $request->query;

        $product = Product::where('product_sn', 'like', "%{$query}%")->get();
        // Return the results as JSON
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([            
            'product_sn'        => 'required|string',
            'product_desc'      => 'required|string',
            'package_details'   => 'array',                    
        ]);
        try {
            DB::beginTransaction();
            $lastCode = Product::latest()->first();
            $lastCodeProd = $lastCode ? $lastCode->product_code : 1000;
            $newCodeProd = $lastCodeProd + 1;

            $product = Product::create([  
                'product_code'  => $newCodeProd,
                'product_sn'    => $request->product_sn,         
                'product_desc'  => $request->product_desc,
                'product_brand' => $request->product_brand,
                'product_uom'   => $request->product_uom,
                'product_stock' => $request->product_stock,
                'product_image' => $request->product_image,
                'is_package'    => $request->is_package,
                'product_category_id' => $request->product_category_id,
            ]);

            if ($request->is_package = true) {
                foreach ($package_details as $pack) {
                    DetailPackage::create([
                        'product_id'  => $product->product_id,
                        'products'    => $pack['product_id'],
                    ]);
                }            
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message'   => 'Failed to Created Product',
                'error'     => $e->getMessage()
            ], 403);
        }                

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::with('detailPackage.product')
            ->where('product_id', $id)
            ->get();
        return response()->json($product);
    }    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {        
        $product = Product::findOrFail($id);
        $product->update([              
            'product_sn'    => $request->product_sn,         
            'product_desc'  => $request->product_desc,
            'product_brand' => $request->product_brand,
            'product_uom'   => $request->product_uom,
            'product_stock' => $request->product_stock,
            'product_image' => $request->product_image,
            'is_package'    => $request->is_package,
            'product_category_id' => $request->product_category_id,
        ]);

        $existingDetails = DetailPackage::where('product_id', $id)->pluck('id_detail_package')->toArray();
        $processIds = [];

        foreach ($request->package_details as $pack) {
            if (isset($pack['id_detail_package']) && in_array($pack['id_detail_package'], $existingDetails)) {
                DetailPackage::findOrFail($pack('id_detail_package'))->update([
                    'product_id'  => $product->product_id,
                    'products'    => $pack['product_id'],
                ]);
                $processIds[] = $pack['id_detail_package'];
            }else{
                $details = DetailPackage::create([
                    'product_id'  => $product->product_id,
                    'products'    => $pack['product_id'],
                ]);
                $processIds[] = $details->id_detail_package;
            }
        }
        $detailsDelete = array_diff($existingDetails, $processIds);
        if (!empty($detailsDelete)) {
            DetailPackage::whereIn('id_detail_package', $detailsDelete)->delete();
        }

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(null, 204);
    }
}
