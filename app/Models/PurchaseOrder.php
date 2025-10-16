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
        'status_payment',
        'sub_total',
        'deposit',
        'ppn',
        'grand_total',
        'has_gr',
        'is_deleted',
        'approved',
        'desc',
        'issue_at',
        'due_at',
    ];

        public function vendor(){
            return $this->belongsTo(Vendor::class, 'vendor_id');
        }

        public function employee(){
            return $this->belongsTo(Employee::class, 'employee_id');
        }

        public function detailpo(){
            return $this->hasMany(DetailPo::class, 'id_po');
        }    
        
        public function salesorder() {
            return $this->hasOne(SalesOrder::class, 'po_number', 'code_po');
        }

        public function payment(){
            return $this->hasOne(PaymentPurchaseOrder::class, 'id_po', 'id_po');
        }
        
}
