<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
   protected $table = 'surat_jalans';

    protected $fillable = [
        'no_bukti',
        'tanggal',
        'spk_id',
        'motor_unit_id',
        'pdi_man_id'
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class, 'spk_id');
    }

    public function motorUnit()
    {
        return $this->belongsTo(MotorUnit::class, 'motor_unit_id');
    }

    public function pdiMan()
    {
        return $this->belongsTo(PdiMan::class, 'pdi_man_id');
    }
}
