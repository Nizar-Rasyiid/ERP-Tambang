<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FakturPajak extends Model
{
    use HasFactory;
    protected $table = 'fakturpajak';
    protected $fillable = [
        'id_invoice',
        'customer_id',
        'code_faktur_pajak'
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'id_invoice', 'id_invoice');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}       
    