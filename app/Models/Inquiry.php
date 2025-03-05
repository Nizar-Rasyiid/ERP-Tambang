<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;
    protected $table = 'inquiry';
    protected $primaryKey = 'id_inquiry';

    protected $fillable = [
        'customer_id',
        'employee_id',
        'code_inquiry',
        'total_tax',        
        'sub_total',        
        'ppn',
        'grand_total',
        'issue_at',
        'due_at',        
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
