<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    protected $table = 'purchaseorders';
    protected $primaryKey = 'id_po';

    protected $fillable = [
        'vendor_id',
        'employee_id',
        'code_po',
        'termin',
        'total_tax',        
        'status_payment',
        'sub_total',
        'deposit',
        'ppn',
        'grand_total',
        'issue_at',
        'due_at',
    ];

        // // Accessor untuk perhitungan PPN
        // public function getCalculatedPpnAttribute()
        // {
        //     return ($this->sub_total * $this->ppn) / 100;
        // }
    
        // // Accessor untuk perhitungan Grand Total
        // public function getCalculatedGrandTotalAttribute()
        // {
        //     return $this->sub_total + $this->total_tax + $this->total_service - $this->deposit + $this->calculated_ppn;
        // }

        public function vendor(){
            return $this->belongsTo(Vendor::class, 'vendor_id');
        }

        public function employee(){
            return $this->belongsTo(Employee::class, 'employee_id');
        }

        public function detailpo(){
            return $this->hasMany(DetailPo::class, 'id_po');
        }
}
