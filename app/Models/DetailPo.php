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
        'id_product'
    ];
}
