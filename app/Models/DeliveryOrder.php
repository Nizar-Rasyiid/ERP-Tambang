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
        'customer_id',
        'employee_id',        
        'id_po',
        'code_do',
        'issue_at',
        'due_at',
    ];
}
