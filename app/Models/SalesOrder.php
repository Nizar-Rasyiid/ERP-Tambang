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
        'customer_id',
        'employee_id',
        'code_so',
        'po_number',
        'termin',        
        'total_tax',            
        'sub_total',
        'deposit',
        'ppn',        
        'grand_total',
        'has_do',
        'has_invoice',
        'has_tandater',
        'issue_at',
        'due_at'
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    
    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function deliveryorder(){
        return $this->belongsTo(DeliveryOrder::class, 'id_so');
    }

    public function detailso(){
        return $this->hasMany(DetailSo::class, 'id_so');
    }
    public function purchaseorder() {
        return $this->belongsTo(PurchaseOrder::class, 'po_number', 'code_po');
    }
    public function salesOrderDetails()
    {
        return $this->hasMany(DetailSo::class, 'id_so', 'id_so');
    }
}
