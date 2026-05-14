<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotorUnit extends Model
{
    protected $fillable = [
        'no_do',
        'no_sp',
        'motor_type_id',
        'motor_color_id',
        'no_mesin',
        'no_rangka',
        'no_seri_kunci',
        'no_kunci',
        'tahun_pembuatan',
        'no_accu',
        'status'
    ];

    public function type()
    {
        return $this->belongsTo(MotorType::class, 'motor_type_id');
    }

    public function color()
    {
        return $this->belongsTo(MotorColor::class, 'motor_color_id');
    }
}
