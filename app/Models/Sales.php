<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $fillable = [
        'kode_sales',
        'jenis_sales',
        'nama_sales',
        'alamat',
        'nik',
        'telepon',
        'tgl_masuk'
    ];
}
