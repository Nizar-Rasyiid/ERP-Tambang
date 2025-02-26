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
        'id_do',
        'customer_id',
        'employee_id',
        'code_quatation',
        'no_quatation',
        'issue_at',
        'due_at',
    ];
}
