<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotorType extends Model
{
    protected $fillable = ['kode_type', 'nama_type', 'otr'];

    public function colors()
    {
        return $this->hasMany(MotorColor::class);
    }
}
