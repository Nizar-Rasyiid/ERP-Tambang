<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;
    protected $table = 'bank_accounts';
    protected $primaryKey = 'id_bank_account';

    protected $fillable = [
        'account_name',
        'bank_name',
        'location',
        'nama_usaha',
        'kode_cabang',
        'swift_code',
        'currency_code',
    ];
}
