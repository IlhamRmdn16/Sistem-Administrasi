<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontrolHargaPenjualan extends Model
{
    protected $fillable = [
        'spk_id', 'discount', 'subsidi_ahm', 'subsidi_dealer',
        'subsidi_main_dealer', 'subsidi_leasing', 'dll', 'ekstra',
        'nama_mediator', 'mediator_fee', 'tambahan', 'refund_transfer'
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class);
    }
}
