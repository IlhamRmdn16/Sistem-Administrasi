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
        $per_page = $request->input('per_page', 10);

        $query = Leasing::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_leasing', 'like', "%{$search}%")
                  ->orWhere('kode_leasing', 'like', "%{$search}%");
            });
        }

        $leasings = $query->latest()->paginate($per_page)->withQueryString();

        return view('master.leasing.index', compact('leasings', 'search', 'per_page'));
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

        return response()->json([
            'success' => true,
            'message' => 'Data Leasing berhasil ditambahkan!'
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Data Leasing berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $leasing = Leasing::findOrFail($id);
        $leasing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Leasing berhasil dihapus!'
        ]);
    }
}
