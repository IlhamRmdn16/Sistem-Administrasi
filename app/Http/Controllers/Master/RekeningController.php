<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Rekening;
use Illuminate\Http\Request;

class RekeningController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Rekening::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_rekening', 'like', "%{$search}%")
                  ->orWhere('kode_rekening', 'like', "%{$search}%")
                  ->orWhere('nomor_rekening', 'like', "%{$search}%");
            });
        }

        // Paginasi di-set statis 10 baris
        $rekenings = $query->latest()->paginate(10)->withQueryString();

        // LOGIKA PENOMORAN MENGISI CELAH KOSONG YANG DIPERKUAT
        $existingNumbers = Rekening::pluck('kode_rekening')
            ->map(function ($kode_rekening) {
                return (int) preg_replace('/[^0-9]/', '', $kode_rekening);
            })
            ->toArray();

        $nextNumber = 1;
        while (in_array($nextNumber, $existingNumbers)) {
            $nextNumber++;
        }

        $autoKodeRekening = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return view('master.rekening.index', compact('rekenings', 'autoKodeRekening', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_rekening' => 'required|unique:rekenings,kode_rekening',
            'nama_rekening' => 'required',
            'nomor_rekening' => 'required|numeric'
        ]);

        Rekening::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data Rekening berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_rekening' => 'required|unique:rekenings,kode_rekening,'.$id,
            'nama_rekening' => 'required',
            'nomor_rekening' => 'required|numeric'
        ]);

        $rekening = Rekening::findOrFail($id);
        $rekening->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data Rekening berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $rekening = Rekening::findOrFail($id);
        $rekening->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Rekening berhasil dihapus!'
        ]);
    }
}
