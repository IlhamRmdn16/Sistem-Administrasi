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

        $rekenings = Rekening::when($search, function($query) use ($search) {
            $query->where('nama_rekening', 'like', "%{$search}%")
                  ->orWhere('kode_rekening', 'like', "%{$search}%")
                  ->orWhere('nomor_rekening', 'like', "%{$search}%");
        })->latest()->paginate(10)->withQueryString();

        // Generate Kode Rekening Otomatis (001, 002, dst)
        $nextId = Rekening::max('id') + 1;
        $autoKodeRekening = str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return view('master.rekening.index', compact('rekenings', 'autoKodeRekening'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_rekening' => 'required|unique:rekenings,kode_rekening',
            'nama_rekening' => 'required',
            'nomor_rekening' => 'required|numeric'
        ]);

        Rekening::create($request->all());

        return back()->with('success', 'Data Rekening berhasil ditambahkan!');
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

        return back()->with('success', 'Data Rekening berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $rekening = Rekening::findOrFail($id);
        $rekening->delete();

        return back()->with('success', 'Data Rekening berhasil dihapus!');
    }
}
