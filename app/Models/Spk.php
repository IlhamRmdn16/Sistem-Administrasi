<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spk extends Model
{
    protected $fillable = [
        'no_spk', 'tanggal', 'sales_id',
        'nama_pemohon', 'nama_stnk', 'alamat', 'rt_rw', 'desa_kelurahan', 'kecamatan', 'kota_kabupaten', 'telepon', 'nik', 'email',
        'jenis_pembayaran', 'motor_type_id', 'motor_color_id', 'harga_otr',
        'uang_muka', 'tanda_jadi', 'leasing_id', 'tenor_bulan', 'cicilan'
    ];

    public function sales() {
        return $this->belongsTo(Sales::class);
    }
    public function motorType() {
        return $this->belongsTo(MotorType::class, 'motor_type_id');
    }
    public function motorColor() {
        return $this->belongsTo(MotorColor::class, 'motor_color_id');
    }
    public function leasing() {
        return $this->belongsTo(Leasing::class);
    }
}
