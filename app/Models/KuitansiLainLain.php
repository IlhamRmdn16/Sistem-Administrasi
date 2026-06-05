<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KuitansiLainLain extends Model
{
    protected $fillable = [
        'no_bukti',
        'tanggal',
        'nama',
        'alamat',
        'rt_rw',
        'desa',
        'kecamatan',
        'kabupaten_kota',
        'no_telepon',
        'keterangan',
        'tipe_motor',
        'nilai'
    ];
}
