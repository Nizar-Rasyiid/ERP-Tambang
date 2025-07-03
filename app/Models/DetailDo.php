<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailDo extends Model
{
    use HasFactory;
    protected $table = 'detail_do';
    protected $primaryKey = 'id_detail_do';
    protected $fillable = [
        'id_do',
        'id_po',
        'id_detail_po',
        'id_detail_so',
        'product_id',                           
        'quantity',
        'quantity_left',
        'price'
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function detailso(){
        return $this->belongsTo(DetailSo::class, 'product_id');
    }

    public function do(){
        return $this->belongsTo(DeliveryOrder::class, 'id_do');
    }
}
