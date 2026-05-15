<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Sales;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request) {
        $search = $request->search;
        $sales = Sales::when($search, function($q) use ($search) {
            $q->where('nama_sales', 'like', "%$search%")->orWhere('nik', 'like', "%$search%");
        })->latest()->paginate(10);
        return view('master.sales.index', compact('sales'));
    }

    public function store(Request $request) {
        $request->validate(['nama_sales' => 'required', 'nik' => 'required|unique:sales']);
        Sales::create($request->all());
        return back()->with('success', 'Data Sales berhasil ditambahkan');
    }

    public function update(Request $request, $id) {
        Sales::findOrFail($id)->update($request->all());
        return back()->with('success', 'Data Sales berhasil diperbarui');
    }

    public function destroy($id) {
        Sales::findOrFail($id)->delete();
        return back()->with('success', 'Data Sales berhasil dihapus');
    }
}
