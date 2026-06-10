<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiDetail extends Model
{
    protected $fillable = [
        'mutasi_id',
        'motor_unit_id'
    ];

    public function mutasi()
    {
        return $this->belongsTo(Mutasi::class, 'mutasi_id');
    }

    public function motorUnit()
    {
        return $this->belongsTo(MotorUnit::class, 'motor_unit_id');
    }
}
