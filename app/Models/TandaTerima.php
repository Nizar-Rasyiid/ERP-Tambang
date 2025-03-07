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
        'code_tandater',
        'resi',
    ];
}
