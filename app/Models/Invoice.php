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
        'id_so',
        'customer_id',        
        'employee_id',
        'sub_total',
        'total_tax',
        'ppn',
        'grand_total',
        'approved',
        'has_tandater',
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
    
    public function salesorder(){
        return $this->belongsTo(SalesOrder::class, 'id_so');
    }

    public function detailInv(){
        return $this->hasMany(DetailInvoice::class, 'id_invoice');
    }
}
