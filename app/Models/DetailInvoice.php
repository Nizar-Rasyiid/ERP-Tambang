<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailInvoice extends Model
{
    use HasFactory;
    protected $table = 'detailinvoices';
    protected $primaryKey = 'id_detail_invoice';

    protected $fillable = [
        'id_invoice',
        'id_do',
        'product_id',
        'quantity',
        'price',
        'amount',
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function do(){
        return $this->belongsTo(DeliveryOrder::class, 'id_do');
    }
}
