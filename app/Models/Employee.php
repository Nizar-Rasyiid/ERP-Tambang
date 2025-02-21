<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';
    protected $primaryKey = 'id_employee';
    protected $fillable = [
        'employee_name',
        'employee_phone',
        'employee_email',
        'employee_address',
        'status',
    ];
}
