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
}
