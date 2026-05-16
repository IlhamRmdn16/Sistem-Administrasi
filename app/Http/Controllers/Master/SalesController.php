<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Sales;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $sales = Sales::when($search, function($q) use ($search) {
            $q->where('nama_sales', 'like', "%$search%")
              ->orWhere('kode_sales', 'like', "%$search%")
              ->orWhere('nik', 'like', "%$search%");
        })->latest()->paginate(10)->withQueryString();

        $nextId = Sales::max('id') + 1;
        $autoKodeSales = str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return view('master.sales.index', compact('sales', 'autoKodeSales'));
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

        return back()->with('success', 'Data Sales berhasil ditambahkan!');
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

        return back()->with('success', 'Data Sales berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Sales::findOrFail($id)->delete();

        return back()->with('success', 'Data Sales berhasil dihapus!');
    }
}
