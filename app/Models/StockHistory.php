<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;
    protected $table = 'stockhistory';
    protected $fillable = [
        'product_id',
        'transaction_id',
        'type',
        'quantity',
        'stock_after'
    ];
}
