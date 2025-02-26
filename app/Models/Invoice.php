<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';

    protected $fillable = [
        'id_po',
        'customer_id',
        'employee_id',
        'code_invoice',
        'no_invoice',
        'issue_at',
        'due_at'
    ];
}
