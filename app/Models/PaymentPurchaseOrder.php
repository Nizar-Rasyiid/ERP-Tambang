<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPurchaseOrder extends Model
{
    use HasFactory;
    protected $table = 'paymentpurchaseorder';
    protected $primaryKey = 'payment_po_id';
    protected $fillable = [
        'id_po',
        'payment_method',
        'code_paymentpo',
        'price',
        'payment_method',
        'issue_at',
    ];
}
