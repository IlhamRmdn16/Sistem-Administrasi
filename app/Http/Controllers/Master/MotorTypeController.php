<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MotorType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MotorTypeController extends Controller
{
    public function create()
    {
        return view('master.motor-type.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_type' => 'required|unique:motor_types,kode_type',
            'nama_type' => 'required',
            'otr' => 'required|numeric',
            'colors' => 'required|array',
            'colors.*.warna' => 'required',
            'colors.*.kode_warna' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $motorType = MotorType::create([
                'kode_type' => $request->kode_type,
                'nama_type' => $request->nama_type,
                'otr' => $request->otr,
            ]);

            foreach ($request->colors as $color) {
                $motorType->colors()->create([
                    'warna' => $color['warna'],
                    'kode_warna' => $color['kode_warna'],
                ]);
            }
        });

        return back()->with('success', 'Data Tipe Motor & Warna berhasil disimpan!');
    }
}
