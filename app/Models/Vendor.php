<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $primaryKey = 'vendor_id';
    protected $table = 'vendors';
    protected $fillable = [        
        'account_name',
        'vendor_name',
        'vendor_type',
        'vendor_email',
        'vendor_phone',
        'vendor_address',
        'tax_number',
    ];

    public function purchaseorder(){
        return $this->belongsTo(PurchaseOrder::class, 'vendor_id');
    }
}
