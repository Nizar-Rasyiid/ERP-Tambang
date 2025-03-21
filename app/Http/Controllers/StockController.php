<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function getStockReport()
    {
        // Ambil semua produk
        $products = Product::with(['stockBatches', 'detailDOs'])->get();

        $report = [];

        foreach ($products as $product) {
            $stockBatches = $product->stockBatches;
            $detailDOs = $product->detailDOs;

            $totalStock = 0;
            $totalValue = 0;

            // Hitung stok dan nilai stok
            foreach ($stockBatches as $batch) {
                $totalStock += $batch->quantity_left;
                $totalValue += $batch->quantity_left * $batch->price;
            }

            // Hitung penjualan
            $totalSales = $detailDOs->sum('quantity');
            $totalSalesValue = $detailDOs->sum(function ($do) {
                return $do->quantity * $do->price;
            });

            // Hitung gross profit
            $grossProfit = $totalSalesValue - $totalValue;

            $report[] = [
                'product_code' => $product->product_code,
                'product_desc' => $product->product_desc,
                'total_stock' => $totalStock,
                'total_value' => $totalValue,
                'total_sales' => $totalSales,
                'total_sales_value' => $totalSalesValue,
                'gross_profit' => $grossProfit,
            ];
        }

        return response()->json($report);
    }
}
