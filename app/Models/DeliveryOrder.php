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
        'code_do',
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
}
