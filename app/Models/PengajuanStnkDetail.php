<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanStnkDetail extends Model
{
    protected $guarded = [];

    public function pengajuanStnk()
    {
        return $this->belongsTo(PengajuanStnk::class);
    }

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id');
    }
}
