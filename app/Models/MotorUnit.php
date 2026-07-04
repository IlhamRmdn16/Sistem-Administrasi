<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotorUnit extends Model
{
   protected $fillable = [
        'penerimaan_unit_id',
        'motor_type_id',
        'motor_color_id',
        'no_mesin',
        'no_rangka',
        'no_seri_kunci',
        'no_kunci',
        'tahun_pembuatan',
        'no_accu',
        'posisi_stok',
        'lokasi_pop_id',
        'status_unit'
    ];

    public function penerimaanUnit()
    {
        return $this->belongsTo(PenerimaanUnit::class, 'penerimaan_unit_id');
    }

    public function type()
    {
        return $this->belongsTo(MotorType::class, 'motor_type_id');
    }

    public function color()
    {
        return $this->belongsTo(MotorColor::class, 'motor_color_id');
    }

    public function lokasiPop()
    {
        return $this->belongsTo(Sales::class, 'lokasi_pop_id');
    }
}
