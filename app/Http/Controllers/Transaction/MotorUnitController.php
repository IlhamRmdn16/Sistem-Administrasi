<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\MotorType;
use App\Models\MotorUnit;
use Illuminate\Http\Request;

class MotorUnitController extends Controller
{
    public function create()
    {
        $types = MotorType::with('colors')->get();
        return view('transaction.motor-unit.create', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_do' => 'required',
            'no_sp' => 'required',
            'motor_type_id' => 'required',
            'motor_color_id' => 'required',
            'no_mesin' => 'required|unique:motor_units',
            'no_rangka' => 'required|unique:motor_units',
            'no_seri_kunci' => 'required',
            'no_kunci' => 'required',
            'tahun_pembuatan' => 'required',
            'no_accu' => 'required',
        ]);

        $tanggal = date('d/m/Y');
        $no_sp_full = $request->no_sp . ' / ' . $tanggal;

        MotorUnit::create([
            'no_do' => $request->no_do,
            'no_sp' => $no_sp_full,
            'motor_type_id' => $request->motor_type_id,
            'motor_color_id' => $request->motor_color_id,
            'no_mesin' => $request->no_mesin,
            'no_rangka' => $request->no_rangka,
            'no_seri_kunci' => $request->no_seri_kunci,
            'no_kunci' => $request->no_kunci,
            'tahun_pembuatan' => $request->tahun_pembuatan,
            'no_accu' => $request->no_accu,
            'status' => 'Tersedia'
        ]);

        return back()->with('success', 'Data Kendaraan berhasil diregistrasi!');
    }
}
