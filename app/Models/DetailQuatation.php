<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailQuatation extends Model
{
    use HasFactory;
    protected $table = "detail_quatation";
    protected $primaryKey = 'id_detail_quatation';

    Protected $fillable = [
        'id_quatation',
        'product_id',
        'quantity',
        'price',
        'amount',
    ];
    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }  
    
    public function quo(){
        return $this->belongsTo(Quatation::class, 'id_quatation');
    }
}
