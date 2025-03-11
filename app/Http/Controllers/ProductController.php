<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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
            'product_sn'    => 'required|string',
            'product_desc'  => 'required|string',
            'product_brand' => 'required|string',
            'product_uom'   => 'required|string',
            'product_stock' => 'required|integer',
            'product_image' => 'string',            
        ]);

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
            'product_category_id' => $request->product_category_id,
        ]);
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'product_image' => 'string',
            'product_name' => 'string',
            'product_qty' => 'integer',
            'stock' => 'integer',
            'status' => 'string',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());
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
