<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPoint extends Model
{
    use HasFactory;
    protected $table = 'customerpoints';
    protected $primaryKey = 'id_customer_point';

    protected $fillable = [
        'customer_id',
        'point',
        'alamat',
    ];
}
