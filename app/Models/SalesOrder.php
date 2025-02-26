<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;
    protected $table = 'salesorders';
    protected $primaryKey = 'id_so';

    protected $fillable = [
        'employee_id',
        'employee_id',
        'code_so',
        'termin',
        'total_tax',        
        'status_payment',
        'sub_total',
        'deposit',
        'ppn',
        'grand_total',
        'issue_at',
        'due_at'
    ];
}
