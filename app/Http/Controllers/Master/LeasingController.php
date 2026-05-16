<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Leasing;
use Illuminate\Http\Request;

class LeasingController extends Controller
{
   public function index(Request $request)
    {
        $search = $request->input('search');

        $leasings = Leasing::when($search, function($query) use ($search) {
            $query->where('nama_leasing', 'like', "%{$search}%")
                  ->orWhere('kode_leasing', 'like', "%{$search}%");
        })->latest()->paginate(10)->withQueryString();

        return view('master.leasing.index', compact('leasings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_leasing' => 'required|unique:leasings,kode_leasing',
            'nama_leasing' => 'required|unique:leasings,nama_leasing',
            'alamat' => 'nullable|string',
            'keterangan_penagihan_1' => 'nullable|string',
            'keterangan_penagihan_2' => 'nullable|string',
        ]);

        Leasing::create($request->all());

        return back()->with('success', 'Data Leasing berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_leasing' => 'required|unique:leasings,kode_leasing,'.$id,
            'nama_leasing' => 'required|unique:leasings,nama_leasing,'.$id,
            'alamat' => 'nullable|string',
            'keterangan_penagihan_1' => 'nullable|string',
            'keterangan_penagihan_2' => 'nullable|string',
        ]);

        $leasing = Leasing::findOrFail($id);
        $leasing->update($request->all());

        return back()->with('success', 'Data Leasing berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $leasing = Leasing::findOrFail($id);
        $leasing->delete();

        return back()->with('success', 'Data Leasing berhasil dihapus!');
    }
}
