<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TandaTerima extends Model
{
    use HasFactory;
    protected $table = 'tandaterima';    
    protected $primaryKey = 'id_tandater';

    protected $fillable = [
        'customer_id',
        'employee_id',        
        'code_tandater',
        'resi',
        'issue_at',
        'due_at',
    ];
    public function so(){
        return $this->belongsTo(SalesOrder::class, 'id_so');
    }
    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function detailtandater(){
        return $this->hasMany(DetailTandater::class, 'id_tandater');
    }
}
