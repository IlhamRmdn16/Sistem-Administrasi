<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenyerahanStnkBpkb extends Model
{
    protected $guarded = [];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id');
    }
}
