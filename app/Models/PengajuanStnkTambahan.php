<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanStnkTambahan extends Model
{
    protected $table = 'pengajuan_stnk_tambahans';
    protected $fillable = ['pengajuan_stnk_id', 'keterangan', 'nominal', 'total'];
}
