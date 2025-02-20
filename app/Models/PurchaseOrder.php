<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    protected $table = 'purchase_orders';
    protected $primaryKey = 'id_po';

    protected $fillable = [
        'id_customer',
        'id_payment_type',
        'id_bank_account',
        'po_type',
        'status_payment',
        'sub_total',
        'total_tax',
        'total_service',
        'deposit',
        'ppn',
        'grand_total',
        'issue_at',
        'due_at'
    ];

        // Accessor untuk perhitungan PPN
        public function getCalculatedPpnAttribute()
        {
            return ($this->sub_total * $this->ppn) / 100;
        }
    
        // Accessor untuk perhitungan Grand Total
        public function getCalculatedGrandTotalAttribute()
        {
            return $this->sub_total + $this->total_tax + $this->total_service - $this->deposit + $this->calculated_ppn;
        }
}
