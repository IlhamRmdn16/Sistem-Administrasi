<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\BiayaAdministrasi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BiayaAdministrasiController extends Controller
{
    public function index()
    {
        $biayas = BiayaAdministrasi::latest()->get();

        return view('master.biaya-administrasi.index', compact('biayas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_sistem' => [
                'nullable',
                'string',
                'in:ADM,TB_STNK,TB_BPKB',
                Rule::unique('biaya_administrasis', 'kode_sistem')
            ],
            'nilai' => 'required|numeric|min:0'
        ], [
            'kode_sistem.unique' => 'Kategori biaya ini sudah digunakan. Silakan edit data yang ada.'
        ]);

        $keterangan = $request->kode_sistem;

        if (!$keterangan) {
            $request->validate([
                'keterangan' => 'required|string|max:255'
            ]);

            $keterangan = $request->keterangan;
        }

        BiayaAdministrasi::create([
            'kode_sistem' => $request->kode_sistem,
            'keterangan' => $keterangan,
            'nilai' => $request->nilai,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Biaya Administrasi berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_sistem' => [
                'nullable',
                'string',
                'in:ADM,TB_STNK,TB_BPKB',
                Rule::unique('biaya_administrasis', 'kode_sistem')->ignore($id)
            ],
            'nilai' => 'required|numeric|min:0'
        ], [
            'kode_sistem.unique' => 'Kategori biaya ini sudah digunakan. Silakan edit data yang ada.'
        ]);

        $keterangan = $request->kode_sistem;

        if (!$keterangan) {
            $request->validate([
                'keterangan' => 'required|string|max:255'
            ]);

            $keterangan = $request->keterangan;
        }

        $biaya = BiayaAdministrasi::findOrFail($id);

        $biaya->update([
            'kode_sistem' => $request->kode_sistem,
            'keterangan' => $keterangan,
            'nilai' => $request->nilai,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Biaya Administrasi berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        BiayaAdministrasi::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Biaya Administrasi berhasil dihapus!'
        ]);
    }
}
