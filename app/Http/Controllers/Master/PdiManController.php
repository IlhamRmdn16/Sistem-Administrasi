<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\PdiMan;
use Illuminate\Http\Request;

class PdiManController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = PdiMan::query();
        if ($search) {
            $query->where('kode_pdi_man', 'like', "%{$search}%")
                  ->orWhere('nama_pdi_man', 'like', "%{$search}%");
        }
        $pdiMans = $query->latest()->paginate(10)->withQueryString();

        // Generate Kode Otomatis (Contoh: PDI0001)
        $lastPdi = PdiMan::orderBy('id', 'desc')->first();
        $nextNumber = $lastPdi ? intval(substr($lastPdi->kode_pdi_man, -4)) + 1 : 1;
        $autoKode = 'PDI' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('master.pdi-man.index', compact('pdiMans', 'autoKode', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_pdi_man' => 'required|unique:pdi_mans,kode_pdi_man',
            'nama_pdi_man' => 'required|string|max:255',
        ]);

        PdiMan::create($request->all());

        return back()->with('success', 'Data PDI Man berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_pdi_man' => 'required|unique:pdi_mans,kode_pdi_man,'.$id,
            'nama_pdi_man' => 'required|string|max:255',
        ]);

        PdiMan::findOrFail($id)->update($request->all());

        return back()->with('success', 'Data PDI Man berhasil diperbarui!');
    }

    public function destroy($id)
    {
        PdiMan::findOrFail($id)->delete();
        return back()->with('success', 'Data PDI Man berhasil dihapus!');
    }
}
