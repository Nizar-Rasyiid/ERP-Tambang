<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailInquiry extends Model
{
    use HasFactory;
    protected $table = 'detalinquiry';
    protected $primaryKey = 'id_detail_inquiry';
    protected $fillable = [
        'id_inquiry',
        'product_id',
        'quantity',
        'price'
    ];
}
