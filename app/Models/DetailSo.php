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
        'price'
    ];
}
