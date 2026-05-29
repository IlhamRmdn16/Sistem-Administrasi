<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontrolHargaPenjualan extends Model
{
    protected $fillable = [
        'spk_id',
        'no_kwitansi_otr',
        'tgl_kwitansi_otr',
        'no_kwitansi_dp',
        'tgl_kwitansi_dp',
        'no_kwitansi_kwm',
        'tgl_kwitansi_kwm',
        'discount',
        'subsidi_ahm',
        'subsidi_dealer',
        'subsidi_main_dealer',
        'subsidi_leasing',
        'dll',
        'ekstra',
        'nama_mediator',
        'mediator_fee',
        'tambahan',
        'refund_transfer'
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class);
    }
}
