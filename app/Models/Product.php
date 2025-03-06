<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $fillable = [
        'product_code',
        'product_sn',
        'product_desc',
        'product_brand',
        'product_uom',
        'product_stock',
        'product_image',
    ];

    public function detailSo(){
        return $this->belongsTo(DetailSo::class, 'product_id', 'product_id');
    }

    public function detailDo(){
        return $this->belongsTo(DetailDo::class, 'product_id');
    }
    public function detailQuatation(){
        return $this->belongsTo(DetailQuatation::class, 'product_id');
    }
}
