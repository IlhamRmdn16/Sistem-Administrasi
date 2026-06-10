<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    protected $fillable = [
        'no_bukti',
        'tanggal',
        'lokasi_asal',
        'lokasi_asal_pop_id',
        'lokasi_tujuan',
        'lokasi_tujuan_pop_id',
        'keterangan'
    ];

    public function asalPop()
    {
        return $this->belongsTo(Sales::class, 'lokasi_asal_pop_id');
    }

    public function tujuanPop()
    {
        return $this->belongsTo(Sales::class, 'lokasi_tujuan_pop_id');
    }

    public function details()
    {
        return $this->hasMany(MutasiDetail::class, 'mutasi_id');
    }
}
