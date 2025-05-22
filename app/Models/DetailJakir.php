<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailJakir extends Model
{
    use HasFactory;
    protected $table = 'detailjakir';
    protected $primaryKey = 'id_detail_jakir';
    protected $fillable = [
        'id_jasakirim',
        'product_id',
        'quantity',
        'price',
        'amount',
    ];

    public function jasakirim(){
        return $this->belongsTo(PoJasaKirim::class, 'id_jasakirim');
    }
    public function vendor(){
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
