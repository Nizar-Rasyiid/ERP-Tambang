<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';
    protected $primaryKey = 'id_invoice';

    protected $fillable = [        
        'customer_id',
        'employee_id',
        'sub_total',
        'total_tax',
        'ppn',
        'code_invoice',                
        'issue_at',
        'due_at'
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
