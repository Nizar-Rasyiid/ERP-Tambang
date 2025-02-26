<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailInvoice extends Model
{
    use HasFactory;
    protected $table = 'detailinvoices';
    protected $primaryKey = 'id_detail_invoice';

    protected $fillable = [
        'id_invoice',
        'id_do',
    ];
}
