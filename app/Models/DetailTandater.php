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
        'id_do'
    ];
}
