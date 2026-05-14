<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotorColor extends Model
{
    protected $fillable = ['motor_type_id', 'warna', 'kode_warna'];

    public function type()
    {
        return $this->belongsTo(MotorType::class, 'motor_type_id');
    }
}
