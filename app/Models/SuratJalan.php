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
        'pdi_man_id',
        'no_stck',
        'no_registrasi',
        'berlaku_sd',
        'is_cetak_samsat'
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

    public function samsat()
    {
        return $this->hasOne(Samsat::class, 'surat_jalan_id');
    }

    public function pengajuanDetail()
    {
        return $this->hasOne(PengajuanStnkDetail::class, 'surat_jalan_id');
    }

    public function penyerahanStnkBpkb()
    {
        return $this->hasOne(PenyerahanStnkBpkb::class, 'surat_jalan_id');
    }

    public function kwitansiProgresif()
    {
        return $this->hasOne(KwitansiPajakProgresif::class, 'surat_jalan_id');
    }
}
