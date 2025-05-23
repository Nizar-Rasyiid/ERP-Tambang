<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    use HasFactory;
    protected $table = 'payment_types';
    protected $primaryKey = 'id_payment_type';
    protected $fillable = [
        'payment_type',
        'status',
    ];

}
