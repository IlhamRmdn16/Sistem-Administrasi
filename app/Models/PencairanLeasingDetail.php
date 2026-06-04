<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencairanLeasingDetail extends Model
{
    protected $fillable = [
        'pencairan_leasing_id', 'surat_jalan_id', 'nilai_pencairan',
        'nilai_realisasi', 'estimasi_dll', 'selisih_aktual', 'margin_lebih_kurang'
    ];

    public function pencairanLeasing()
    {
        return $this->belongsTo(PencairanLeasing::class);
    }

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }
}
