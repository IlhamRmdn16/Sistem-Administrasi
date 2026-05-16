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
            $query->where('kode_motor', 'like', "%{$search}%")
                  ->orWhere('kode_tipe', 'like', "%{$search}%")
                  ->orWhere('nama_type', 'like', "%{$search}%");
        }

        $motorTypes = $query->latest()->paginate(10)->withQueryString();

        $nextId = MotorType::max('id') + 1;
        $autoKodeTipe = str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return view('master.motor-type.index', compact('motorTypes', 'autoKodeTipe'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_tipe' => 'required|unique:motor_types,kode_tipe',
            'jenis' => 'required',
            'nama_type' => 'required',
            'tahun_pembuatan' => 'required',
            'kode_motor' => 'required|unique:motor_types,kode_motor',
            'sampul_buku' => 'required|array',
            'otr' => 'required|numeric',
            'notice_pajak' => 'required|numeric',
            'bbn' => 'required|numeric',
            'adm_stnk' => 'required|numeric',
            'colors' => 'required|array',
            'colors.*.warna' => 'required',
            'colors.*.kode_warna' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $motorType = MotorType::create([
                'kode_tipe' => $request->kode_tipe,
                'jenis' => $request->jenis,
                'nama_type' => $request->nama_type,
                'tahun_pembuatan' => $request->tahun_pembuatan,
                'kode_motor' => $request->kode_motor,
                'sampul_buku' => $request->sampul_buku,
                'otr' => $request->otr,
                'notice_pajak' => $request->notice_pajak,
                'bbn' => $request->bbn,
                'adm_stnk' => $request->adm_stnk,
            ]);

            foreach ($request->colors as $color) {
                $motorType->colors()->create([
                    'warna' => $color['warna'],
                    'kode_warna' => $color['kode_warna'],
                ]);
            }
        });

        return back()->with('success', 'Data Motor berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_tipe' => 'required|unique:motor_types,kode_tipe,'.$id,
            'jenis' => 'required',
            'nama_type' => 'required',
            'tahun_pembuatan' => 'required',
            'kode_motor' => 'required|unique:motor_types,kode_motor,'.$id,
            'sampul_buku' => 'required|array',
            'otr' => 'required|numeric',
            'notice_pajak' => 'required|numeric',
            'bbn' => 'required|numeric',
            'adm_stnk' => 'required|numeric',
            'colors' => 'required|array',
            'colors.*.warna' => 'required',
            'colors.*.kode_warna' => 'required',
        ]);

        DB::transaction(function () use ($request, $id) {
            $motorType = MotorType::findOrFail($id);

            $motorType->update([
                'kode_tipe' => $request->kode_tipe,
                'jenis' => $request->jenis,
                'nama_type' => $request->nama_type,
                'tahun_pembuatan' => $request->tahun_pembuatan,
                'kode_motor' => $request->kode_motor,
                'sampul_buku' => $request->sampul_buku,
                'otr' => $request->otr,
                'notice_pajak' => $request->notice_pajak,
                'bbn' => $request->bbn,
                'adm_stnk' => $request->adm_stnk,
            ]);

            $motorType->colors()->delete();

            foreach ($request->colors as $color) {
                $motorType->colors()->create([
                    'warna' => $color['warna'],
                    'kode_warna' => $color['kode_warna'],
                ]);
            }
        });

        return back()->with('success', 'Data Motor berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $motorType = MotorType::findOrFail($id);
        $motorType->delete();

        return back()->with('success', 'Data berhasil dihapus!');
    }
}
