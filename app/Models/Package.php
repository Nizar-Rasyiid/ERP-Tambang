<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    protected $table = 'package';
    protected $primaryKey = 'package_id';
    protected $fillable = [
        'code_package',
        'package_desc',
        'package_sn'
    ];

    public function detailpackage(){
        return $this->hasMany(DetailPackage::class, 'package_id');
    }
}
