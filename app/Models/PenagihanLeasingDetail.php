<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenagihanLeasingDetail extends Model
{
    protected $fillable = ['penagihan_leasing_id', 'surat_jalan_id', 'otr', 'dp_po', 'sisa'];

    public function penagihanLeasing()
    {
        return $this->belongsTo(PenagihanLeasing::class);
    }

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }
}
