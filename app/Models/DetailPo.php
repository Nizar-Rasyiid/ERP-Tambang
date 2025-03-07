<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPo extends Model
{
    use HasFactory;
    protected $table = 'detailpo';
    protected $primaryKey = 'id_detail_po';
    protected $fillable = [
        'id_po',
        'product_id',
        'quantity',
        'price',      
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
}
