<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';
    protected $primaryKey = 'employee_id';
    protected $fillable = [
        'employee_code',
        'employee_name',
        'employee_phone',
        'employee_email',
        'employee_address',
        'employee_salary',
        'employee_end_contract',
        'employee_nik',
        'employee_position'
    ];

    public function salesOrder(){
        return $this->belongsTo(SalesOrder::class, 'employee_id');
    }

    public function deliveryOrder(){
        return $this->belongsTo(DeliveryOrder::class, 'employee_id');
    }

    public function purchaseorder(){
        return $this->belongsTo(PurchaseOrder::class, 'employee_id');
    }

    public function invoice(){
        return $this->belongsTo(Invoice::class, 'employee_id', 'employee_id');
    }
}
