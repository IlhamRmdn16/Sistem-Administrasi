<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanStnkDetail extends Model
{
    protected $table = 'pengajuan_stnk_details';
    protected $fillable = ['pengajuan_stnk_id', 'samsat_id', 'notice_pajak', 'adm', 'sub_total'];

    public function samsat()
    {
        return $this->belongsTo(Samsat::class);
    }
}
