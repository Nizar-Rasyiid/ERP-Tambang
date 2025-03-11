<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSo extends Model
{
    use HasFactory;
    protected $table = 'detailso';
    protected $primaryKey = 'id_detail_so';

    protected $fillable = [
        'id_so',
        'product_id',
        'quantity',
        'quantity_left',
        'has_do',
        'price',
        'amount'
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function detailpo(){
        return $this->belongsTo(DetailPo::class, 'product_id');
    }

    public function salesorders(){
        return $this->belongsTo(SalesOrder::class, 'id_so');
    }
}
