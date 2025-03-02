<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'customer_code',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'customer_npwp',
        'customer_contact',
    ];

    public function saledorder(){
        return $this->belongsTo(SalesOrder::class, 'customer_id');
    }
    public function deliveryorder(){
        return $this->belongsTo(DeliveryOrder::class, 'customer_id');
    }
    public function purchaseorder(){
        return $this0>belongsTo(PurchaseOrder::class, 'customer_id');
    }
}
