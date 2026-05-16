<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leasing extends Model
{
    protected $fillable = [
        'kode_leasing',
        'nama_leasing',
        'alamat',
        'keterangan_penagihan_1',
        'keterangan_penagihan_2'
    ];
}
