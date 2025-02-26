<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'customer_code',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'customer_npwp',
        'customer_contact',
    ];
}
