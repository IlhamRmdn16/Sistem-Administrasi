<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenerimaanUnit extends Model
{
    protected $table = 'penerimaan_units';

    protected $fillable = [
        'no_bukti',
        'tanggal',
        'no_kendaraan',
        'ekspedisi',
        'no_sj',
        'no_nd',
        'no_so'
    ];

    public function motorUnits()
    {
        return $this->hasMany(MotorUnit::class, 'penerimaan_unit_id');
    }
}
