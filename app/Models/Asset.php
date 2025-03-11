<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;
    protected $table = 'assets';
    protected $primaryKey = 'asset_id';
    protected $fillable = [
        'vendor_id',        
        'code',
        'assets_name',
        'price',
        'assets_life', 
        'issue_at',
        'due_at',       
    ];

    public function vendor(){
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
