<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTandater extends Model
{
    use HasFactory;
    protected $table = 'detailtandater';
    protected $primaryKey = 'id_detail_tandater';

    protected $fillable = [
        'id_invoice',
        'id_so',
        'issue_at',
        'id_tandater'
    ];

    public function so(){
        return $this->belongsTo(SalesOrder::class, 'id_so');
    }
    public function invoice(){
        return $this->belongsTo(Invoice::class, 'id_invoice');
    }
    public function tandater(){
        return $this->belongsTo(TandaTerima::class, 'id_tandater');
    }
}
