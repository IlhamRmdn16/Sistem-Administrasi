<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaAdministrasi extends Model
{
    protected $table = 'biaya_administrasis';
    protected $fillable = ['keterangan', 'nilai'];
}
