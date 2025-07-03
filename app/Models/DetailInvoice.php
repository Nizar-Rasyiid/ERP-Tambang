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
        'id_so',
        'id_detail_so',
        'id_detail_do',
        'id_detail_po',
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

    public function so(){
        return $this->belongsTo(SalesOrder::class, 'id_so');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'id_invoice');
    }
}
