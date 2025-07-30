<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenants extends Model
{
    protected $table = 'tenants';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'token',
        'database',
        'status'
    ];
}
