<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanStnk extends Model
{
    protected $table = 'pengajuan_stnks';
    protected $fillable = ['no_bukti', 'tanggal', 'total_pajak', 'total_adm', 'total_tambahan', 'grand_total'];

    public function details()
    {
        return $this->hasMany(PengajuanStnkDetail::class);
    }

    public function tambahans()
    {
        return $this->hasMany(PengajuanStnkTambahan::class);
    }
}
