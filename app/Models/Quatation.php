<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quatation extends Model
{
    use HasFactory;
    protected $table = 'quatations';
    protected $primaryKey = 'id_quatation';

    protected $fillable = [        
        'customer_id',
        'employee_id',
        'termin',
        'code_quatation',
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

    public function detailQuo(){
        return $this->hasMany(DetailQuatation::class, 'id_quatation');
    }
}
