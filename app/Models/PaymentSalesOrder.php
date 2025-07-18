<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSalesOrder extends Model
{
    use HasFactory;
    protected $table = 'paymentsalesorder';
    protected $primaryKey = 'payment_so_id';
    protected $fillable = [
        'id_invoice',
        'payment_method',
        'code_paymentso',
        'price',        
        'issue_at',
        'due_at'
    ];
}
