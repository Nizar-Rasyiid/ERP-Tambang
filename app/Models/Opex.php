<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opex extends Model
{
    use HasFactory;
    protected $table = 'opex';
    protected $primaryKey = 'opex_id';
    protected $fillable = [
        'opex_code',
        'opex_name',
        'opex_type',
        'opex_price',
        'customer_id',
        'approved',
        'issue_at',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function absorbDetail(){
        return $this->hasMany(AbsorbDetail::class, 'opex_id');
    }
}
    
