<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\KuitansiLainLain;
use Illuminate\Http\Request;
use Riskihajar\Terbilang\Facades\Terbilang;

class KuitansiLainLainController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $per_page = $request->input('per_page', 10);

        $query = KuitansiLainLain::query();

        // Filter Rentang Tanggal (Periode)
        if ($start_date && $end_date) {
            $query->whereBetween('tanggal', [$start_date, $end_date]);
        } elseif ($start_date) {
            $query->where('tanggal', '>=', $start_date);
        } elseif ($end_date) {
            $query->where('tanggal', '<=', $end_date);
        }

        // Filter Pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_bukti', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $kuitansis = $query->latest()->paginate($per_page)->withQueryString();

        // Logika Penomoran Otomatis - Gap Filling (Mengisi Celah Kosong)
        $prefix = 'KLL' . date('Y/m/');
        $existingNumbers = KuitansiLainLain::where('no_bukti', 'like', $prefix . '%')
            ->pluck('no_bukti')
            ->map(function ($no) {
                return (int) substr($no, -4);
            })
            ->toArray();

        $nextNumber = 1;
        while (in_array($nextNumber, $existingNumbers)) {
            $nextNumber++;
        }
        $autoKode = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('transaction.kuitansi-lain.index', compact(
            'kuitansis', 'autoKode', 'search', 'start_date', 'end_date', 'per_page'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nilai' => 'required|numeric',
            'tanggal' => 'required|date'
        ]);

        KuitansiLainLain::create($request->all());

        return back()->with('success', 'Data Kuitansi berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nilai' => 'required|numeric'
        ]);

        KuitansiLainLain::findOrFail($id)->update($request->except(['no_bukti']));

        return back()->with('success', 'Data Kuitansi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        KuitansiLainLain::findOrFail($id)->delete();

        return back()->with('success', 'Kuitansi dihapus. Nomor urut dikembalikan ke sistem!');
    }

    public function print($id)
    {
        $kuitansi = KuitansiLainLain::findOrFail($id);

        // Memastikan konfigurasi bahasa Indonesia
        config(['terbilang.locale' => 'id']);

        // Memproses terbilang di dalam Controller agar tidak terjadi error merah di Blade
        $terbilang = ucwords(Terbilang::make($kuitansi->nilai, ' rupiah'));

        // Lempar variabel $terbilang yang sudah matang ke View
        return view('transaction.kuitansi-lain.print', compact('kuitansi', 'terbilang'));
    }
}
