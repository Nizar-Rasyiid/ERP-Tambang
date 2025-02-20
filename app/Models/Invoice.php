<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';

    protected $fillable = [
        'id_do',
        'id_po',
        'id_customer',
        'id_bank_account',
        'id_payment_type',
        'no_invoice',
    ];
}
