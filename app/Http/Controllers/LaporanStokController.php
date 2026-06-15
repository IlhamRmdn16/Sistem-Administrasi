<?php

namespace App\Http\Controllers;

use App\Models\MotorType;
use App\Models\MotorUnit;
use Illuminate\Http\Request;

class LaporanStokController extends Controller
{
   private function getBaseQuery($search)
    {
        $query = MotorType::withCount([
            'motorUnits as stok_gudang' => function($q) {
                $q->where('status_unit', 'Tersedia')->whereIn('posisi_stok', ['Gudang 1', 'Gudang 2']);
            },
            'motorUnits as stok_showroom' => function($q) {
                // PERBAIKAN: Disamakan persis dengan data MutasiStokController
                $q->where('status_unit', 'Tersedia')->where('posisi_stok', 'Showroom Pusat');
            },
            'motorUnits as stok_pop' => function($q) {
                $q->where('status_unit', 'Tersedia')->where('posisi_stok', 'POP');
            },
            'motorUnits as stok_gp' => function($q) {
                $q->where('status_unit', 'Tersedia')->where('posisi_stok', 'Showroom GP');
            },
            'motorUnits as stok_total' => function($q) {
                $q->where('status_unit', 'Tersedia');
            }
        ]);

        // PERBAIKAN: Mengganti havingRaw menjadi whereHas agar 100% aman di semua versi database
        // Hanya tampilkan Tipe Motor yang punya minimal 1 unit dengan status 'Tersedia'
        $query->whereHas('motorUnits', function($q) {
            $q->where('status_unit', 'Tersedia');
        });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_tipe', 'like', "%{$search}%")
                  ->orWhere('nama_type', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    // Helper untuk menghitung total akumulasi seluruh baris yang difilter
    private function getGlobalTotals($search)
    {
        $baseUnitQuery = MotorUnit::where('status_unit', 'Tersedia')
            ->whereHas('type', function($q) use ($search) {
                if ($search) {
                    $q->where('kode_tipe', 'like', "%{$search}%")
                      ->orWhere('nama_type', 'like', "%{$search}%");
                }
            });

        return [
            'gudang' => (clone $baseUnitQuery)->whereIn('posisi_stok', ['Gudang 1', 'Gudang 2'])->count(),
            // PERBAIKAN: Disamakan persis dengan data MutasiStokController
            'showroom' => (clone $baseUnitQuery)->where('posisi_stok', 'Showroom Pusat')->count(),
            'pop' => (clone $baseUnitQuery)->where('posisi_stok', 'POP')->count(),
            'gp' => (clone $baseUnitQuery)->where('posisi_stok', 'Showroom GP')->count(),
            'total' => (clone $baseUnitQuery)->count(),
        ];
    }

    public function global(Request $request)
    {
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);

        // Panggil helper getBaseQuery()
        $query = $this->getBaseQuery($search);

        $stokTypes = $query->paginate($per_page)->withQueryString();
        $totals = $this->getGlobalTotals($search);

        return view('laporan.stok.global', compact('stokTypes', 'totals', 'search', 'per_page'));
    }

    public function printGlobal(Request $request)
    {
        $search = $request->input('search');

        // Tarik semua data tanpa paginasi untuk kebutuhan cetak
        $stokTypes = $this->getBaseQuery($search)->get();
        $totals = $this->getGlobalTotals($search);

        return view('laporan.stok.print-global', compact('stokTypes', 'totals', 'search'));
    }
}
