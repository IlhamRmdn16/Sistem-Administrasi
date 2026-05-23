<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotorType extends Model
{
    protected $fillable = [
        'kode_tipe',
        'jenis',
        'nama_type',
        'otr',
        'notice_pajak',
        'kode_motor',
        'sampul_buku',
        'tahun_pembuatan'
    ];

    protected $casts = [
        'sampul_buku' => 'array',
    ];

    public function colors()
    {
        return $this->hasMany(MotorColor::class);
    }
}
