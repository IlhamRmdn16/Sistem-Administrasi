<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencairanLeasing extends Model
{
    protected $fillable = ['no_bukti', 'tanggal', 'leasing_id'];

    public function leasing()
    {
        return $this->belongsTo(Leasing::class);
    }

    public function details()
    {
        return $this->hasMany(PencairanLeasingDetail::class);
    }
}
