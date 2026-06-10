<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Sales;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);

        $query = Sales::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_sales', 'like', "%{$search}%")
                  ->orWhere('kode_sales', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $sales = $query->latest()->paginate($per_page)->withQueryString();

        $existingNumbers = Sales::pluck('kode_sales')
            ->map(function ($kode_sales) {
                return (int) preg_replace('/[^0-9]/', '', $kode_sales);
            })
            ->toArray();

        $nextNumber = 1;
        while (in_array($nextNumber, $existingNumbers)) {
            $nextNumber++;
        }

        $autoKodeSales = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return view('master.sales.index', compact('sales', 'autoKodeSales', 'search', 'per_page'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_sales' => 'required|unique:sales',
            'jenis_sales' => 'required',
            'nama_sales' => 'required',
            'nik' => 'nullable|unique:sales',
            'telepon' => 'nullable',
            'alamat' => 'nullable',
            'tgl_masuk' => 'nullable|date'
        ]);

        Sales::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data Sales berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_sales' => 'required|unique:sales,kode_sales,'.$id,
            'jenis_sales' => 'required',
            'nama_sales' => 'required',
            'nik' => 'nullable|unique:sales,nik,'.$id,
            'telepon' => 'nullable',
            'alamat' => 'nullable',
            'tgl_masuk' => 'nullable|date'
        ]);

        Sales::findOrFail($id)->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data Sales berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        Sales::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Sales berhasil dihapus!'
        ]);
    }
}
