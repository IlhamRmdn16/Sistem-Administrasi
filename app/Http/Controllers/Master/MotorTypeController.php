<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MotorType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MotorTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = MotorType::with('colors');

        if ($search) {
            $query->where('kode_type', 'like', "%{$search}%")
                  ->orWhere('nama_type', 'like', "%{$search}%");
        }

        $motorTypes = $query->latest()->paginate(10)->withQueryString();

        return view('master.motor-type.index', compact('motorTypes'));
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

    public function destroy($id)
    {
        $motorType = MotorType::findOrFail($id);
        $motorType->delete();

        return back()->with('success', 'Data berhasil dihapus!');
    }
}
