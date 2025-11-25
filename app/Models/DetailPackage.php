<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPackage extends Model
{
    use HasFactory;
    protected $table = 'detailpackage';
    protected $primaryKey = 'id_detail_package';
    protected $fillable = [        
        'product_id',
        'products',
        'quantity',
        'used_for_stock',
        'has_used'
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'products');        
    }

    public function package(){
        return $this->belongsTo(Package::class, 'package_id');        
    }
}
