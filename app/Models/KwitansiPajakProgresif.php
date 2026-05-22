<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KwitansiPajakProgresif extends Model
{
    protected $table = 'kwitansi_pajak_progresifs';
    protected $guarded = [];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id');
    }
}
