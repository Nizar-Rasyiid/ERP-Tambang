<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoJasaKirim extends Model
{
    use HasFactory;
    protected $table = 'po_jasakirim';
    protected $primaryKey = 'id_jasakirim';
    protected $fillable = [
        'vendor_id',
        'employee_id',
        'code_jasakirim',
        'termin',
        'deposit',
        'sub_total',
        'ppn',
        'grand_total',
        'approved',
        'issue_at',
        'due_at',
    ];
    public function vendor(){
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function detailjakir(){
        return $this->hasMany(DetailJakir::class, 'id_jasakirim');
    }
}
