<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use HasFactory;
    protected $table = 'deliveryorders';
    protected $primaryKey = 'id_do';

    protected $fillable = [
        'id_customer',
        'id_employee',
        'id_bank_account',
        'id_po',
        'issued_at',
        'due_at',
    ];
}
