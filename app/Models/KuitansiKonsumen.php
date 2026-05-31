<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KuitansiKonsumen extends Model
{
    protected $fillable = [
        'spk_id', 'no_kuitansi', 'tanggal',
        'bayar_kontan', 'bayar_transfer', 'rekening_id', 'keterangan'
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class);
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class);
    }

    // Accessor untuk mendapatkan total pembayaran di kuitansi ini
    public function getTotalAttribute()
    {
        return $this->bayar_kontan + $this->bayar_transfer;
    }
}
