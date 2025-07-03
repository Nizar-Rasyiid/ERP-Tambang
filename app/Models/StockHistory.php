<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;
    protected $table = 'stockhistory';
    protected $fillable = [        
        'id_po',
        'id_detail_po', 
        'product_id', 
        'quantity', 
        'quantity_left',
        'price',         
    ];
    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
}
