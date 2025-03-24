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
    public function detailinvoice(){
        return $this->hasMany(DetailInvoice::class, 'id_invoice');
    }
    //Delivery order
    public function deliveryorder()  {
        return $this->belongsTo(DeliveryOrder::class, 'id_do');
    }
    public function SalesOrder()  {
        return $this->belongsTo(SalesOrder::class, 'id_so' , 'id_so');
    }
    public function PurchaseOrder(){
        return $this->belongsTo(PurchaseOrder::class,'id_po','id_po');
    }
}
