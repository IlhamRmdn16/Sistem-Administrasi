<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdiMan extends Model
{
    protected $table = 'pdi_mans';

    protected $fillable = [
        'kode_pdi_man',
        'nama_pdi_man'
    ];
}
