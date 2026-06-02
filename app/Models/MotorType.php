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
        'otr' => 'integer',
        'notice_pajak' => 'integer',
    ];

    public function getOtrAttribute($value)
    {
        return (int) round((float) $value);
    }

    public function getNoticePajakAttribute($value)
    {
        return (int) round((float) $value);
    }

    public function colors()
    {
        return $this->hasMany(MotorColor::class);
    }
}
