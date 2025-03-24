<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use HasFactory;
    protected $table = 'deliveryorders';
    protected $primaryKey = 'id_do';

    protected $fillable = [
        'customer_id',
        'employee_id',        
        'id_so',  
        'id_customer_point',      
        'code_do',
        'has_inv',
        'sub_total',
        'issue_at',
        'due_at',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }    
    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function salesorder(){
        return $this->belongsTo(SalesOrder::class, 'id_so');
    }

    public function detailinvoice(){
        return $this->belongsTo(DetailInvoice::class, 'id_do');
    }
    public function point(){
        return $this->belongsTo(CustomerPoint::class, 'id_customer_point');
    }

    public function detailDo(){
        return $this->hasMany(DetailDo::class, 'id_do');
    }
}
