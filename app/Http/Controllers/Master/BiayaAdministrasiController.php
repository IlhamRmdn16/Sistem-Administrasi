<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\BiayaAdministrasi;
use Illuminate\Http\Request;

class BiayaAdministrasiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = BiayaAdministrasi::query();

        if ($search) {
            $query->where('keterangan', 'like', "%{$search}%");
        }

        $biayas = $query->latest()->paginate(10)->withQueryString();

        return view('master.biaya-administrasi.index', compact('biayas', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'nilai' => 'required|numeric|min:0'
        ]);

        BiayaAdministrasi::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Biaya Administrasi berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'nilai' => 'required|numeric|min:0'
        ]);

        BiayaAdministrasi::findOrFail($id)->update($request->all());

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
