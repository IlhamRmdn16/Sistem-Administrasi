<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenyerahanBpkbLeasingDetail extends Model
{
    protected $fillable = ['penyerahan_bpkb_leasing_id', 'surat_jalan_id'];

    public function header()
    {
        return $this->belongsTo(PenyerahanBpkbLeasing::class, 'penyerahan_bpkb_leasing_id');
    }

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'surat_jalan_id');
    }
}
