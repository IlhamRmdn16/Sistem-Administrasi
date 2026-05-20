<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Samsat extends Model
{
   protected $table = 'samsats';

    protected $fillable = [
        'surat_jalan_id', 'no_polisi', 'no_stnk', 'tgl_stnk', 'tgl_terima_stnk',
        'jumlah_motor', 'pajak_progresif', 'no_bpkb', 'tgl_bpkb', 'tgl_terima_bpkb'
    ];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id');
    }
}
