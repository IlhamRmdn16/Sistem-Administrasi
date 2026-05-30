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
        'no_kwitansi_kw1',
        'tgl_kwitansi_kw1',
        'no_kwitansi_kw2',
        'tgl_kwitansi_kw2',
        'discount',
        'subsidi_ahm',
        'subsidi_dealer',
        'subsidi_main_dealer',
        'subsidi_leasing_1',
        'subsidi_leasing_2',
        'dll_1',
        'dll_2',
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
