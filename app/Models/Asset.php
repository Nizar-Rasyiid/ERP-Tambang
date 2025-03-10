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
        'id_asset_type',
        'code',
        'name',
        'qty',
        'status',
    ];
}
