<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsorbDetail extends Model
{
    use HasFactory;
    protected $table = 'absorbdetail';
    protected $primaryKey = 'id_absorb_detail';
    protected $fillable = [
        'opex_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    
}
