<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanPenjualanController extends Controller
{
    private function applyFilters($query, $dari_tanggal, $sampai_tanggal, $jenis_dokumen)
    {
        if ($dari_tanggal && $sampai_tanggal) {
            $query->whereBetween('spks.tanggal', [$dari_tanggal, $sampai_tanggal]);
        }

        if ($jenis_dokumen === 'spk') {
            $query->where('spks.no_spk', 'like', 'SPK%');
        } elseif ($jenis_dokumen === 'gpk') {
            $query->where('spks.no_spk', 'like', 'GPK%');
        }

        return $query;
    }

    public function globalUnit(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal', date('Y-m-01'));
        $sampai_tanggal = $request->input('sampai_tanggal', date('Y-m-d'));
        $jenis_dokumen = $request->input('jenis_dokumen', 'all');
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);

        // Query dengan pemisahan Cash & Kredit serta penggabungan Uang Muka + Tanda Jadi
        $query = DB::table('spks')
            ->join('motor_units', 'spks.motor_unit_id', '=', 'motor_units.id')
            ->join('motor_types', 'motor_units.motor_type_id', '=', 'motor_types.id')
            ->select(
                'motor_types.kode_tipe',
                'motor_types.nama_type',
                DB::raw("SUM(CASE WHEN spks.jenis_pembayaran IN ('Cash', 'Tunai') THEN 1 ELSE 0 END) as unit_cash"),
                DB::raw("SUM(CASE WHEN spks.jenis_pembayaran = 'Kredit' THEN 1 ELSE 0 END) as unit_kredit"),
                DB::raw("COUNT(spks.id) as total_unit"),
                DB::raw("SUM(spks.harga_otr) as total_otr"),
                DB::raw("SUM(CASE WHEN spks.jenis_pembayaran IN ('Cash', 'Tunai') THEN spks.harga_otr ELSE 0 END) as total_tunai"),
                DB::raw("SUM(CASE WHEN spks.jenis_pembayaran = 'Kredit' THEN spks.uang_muka + spks.tanda_jadi ELSE 0 END) as total_dp_tanda_jadi")
            )
            ->groupBy('motor_types.id', 'motor_types.kode_tipe', 'motor_types.nama_type');

        $query = $this->applyFilters($query, $dari_tanggal, $sampai_tanggal, $jenis_dokumen);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('motor_types.kode_tipe', 'like', "%{$search}%")
                  ->orWhere('motor_types.nama_type', 'like', "%{$search}%");
            });
        }

        $salesData = $query->paginate($per_page)->withQueryString();

        // Grand Total
        $totalQuery = DB::table('spks')
            ->join('motor_units', 'spks.motor_unit_id', '=', 'motor_units.id')
            ->join('motor_types', 'motor_units.motor_type_id', '=', 'motor_types.id')
            ->select(
                DB::raw("SUM(CASE WHEN spks.jenis_pembayaran IN ('Cash', 'Tunai') THEN 1 ELSE 0 END) as t_cash"),
                DB::raw("SUM(CASE WHEN spks.jenis_pembayaran = 'Kredit' THEN 1 ELSE 0 END) as t_kredit"),
                DB::raw("COUNT(spks.id) as t_unit"),
                DB::raw("SUM(spks.harga_otr) as t_otr"),
                DB::raw("SUM(CASE WHEN spks.jenis_pembayaran IN ('Cash', 'Tunai') THEN spks.harga_otr ELSE 0 END) as t_tunai"),
                DB::raw("SUM(CASE WHEN spks.jenis_pembayaran = 'Kredit' THEN spks.uang_muka + spks.tanda_jadi ELSE 0 END) as t_dp")
            );
        $totalQuery = $this->applyFilters($totalQuery, $dari_tanggal, $sampai_tanggal, $jenis_dokumen);
        if ($search) {
            $totalQuery->where(function($q) use ($search) {
                $q->where('motor_types.kode_tipe', 'like', "%{$search}%")->orWhere('motor_types.nama_type', 'like', "%{$search}%");
            });
        }
        $grandTotal = $totalQuery->first();

        return view('laporan.penjualan.global-unit', compact('salesData', 'grandTotal', 'dari_tanggal', 'sampai_tanggal', 'jenis_dokumen', 'search', 'per_page'));
    }

    public function printGlobalUnit(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');
        $jenis_dokumen = $request->input('jenis_dokumen', 'all');
        $search = $request->input('search');

        $query = DB::table('spks')
            ->join('motor_units', 'spks.motor_unit_id', '=', 'motor_units.id')
            ->join('motor_types', 'motor_units.motor_type_id', '=', 'motor_types.id')
            ->select(
                'motor_types.kode_tipe',
                'motor_types.nama_type',
                DB::raw("SUM(CASE WHEN spks.jenis_pembayaran IN ('Cash', 'Tunai') THEN 1 ELSE 0 END) as unit_cash"),
                DB::raw("SUM(CASE WHEN spks.jenis_pembayaran = 'Kredit' THEN 1 ELSE 0 END) as unit_kredit"),
                DB::raw("COUNT(spks.id) as total_unit"),
                DB::raw("SUM(spks.harga_otr) as total_otr"),
                DB::raw("SUM(CASE WHEN spks.jenis_pembayaran IN ('Cash', 'Tunai') THEN spks.harga_otr ELSE 0 END) as total_tunai"),
                DB::raw("SUM(CASE WHEN spks.jenis_pembayaran = 'Kredit' THEN spks.uang_muka + spks.tanda_jadi ELSE 0 END) as total_dp_tanda_jadi")
            )
            ->groupBy('motor_types.id', 'motor_types.kode_tipe', 'motor_types.nama_type');

        $query = $this->applyFilters($query, $dari_tanggal, $sampai_tanggal, $jenis_dokumen);
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('motor_types.kode_tipe', 'like', "%{$search}%")->orWhere('motor_types.nama_type', 'like', "%{$search}%");
            });
        }

        $salesData = $query->get();
        return view('laporan.penjualan.print-global-unit', compact('salesData', 'dari_tanggal', 'sampai_tanggal', 'jenis_dokumen', 'search'));
    }

    public function terperinci(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal', date('Y-m-01'));
        $sampai_tanggal = $request->input('sampai_tanggal', date('Y-m-d'));
        $jenis_dokumen = $request->input('jenis_dokumen', 'all');
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);

        $query = Spk::with(['motorUnit.type', 'motorUnit.color']);
        $query = $this->applyFilters($query, $dari_tanggal, $sampai_tanggal, $jenis_dokumen);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_spk', 'like', "%{$search}%")
                  ->orWhere('nama_pemohon', 'like', "%{$search}%")
                  ->orWhere('nama_stnk', 'like', "%{$search}%")
                  ->orWhereHas('motorUnit', function($u) use ($search) {
                      $u->where('no_mesin', 'like', "%{$search}%")
                        ->orWhere('no_kunci', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->latest('tanggal')->paginate($per_page)->withQueryString();

        // Hitung Grand Total Akumulasi (Gunakan clone agar query tidak saling menimpa)
        $totalQuery = Spk::query();
        $totalQuery = $this->applyFilters($totalQuery, $dari_tanggal, $sampai_tanggal, $jenis_dokumen);
        if ($search) {
            $totalQuery->where(function($q) use ($search) {
                $q->where('no_spk', 'like', "%{$search}%")->orWhere('nama_pemohon', 'like', "%{$search}%")->orWhere('nama_stnk', 'like', "%{$search}%");
            });
        }

        $totals = [
            'otr' => (clone $totalQuery)->sum('harga_otr'),
            'tunai' => (clone $totalQuery)->whereIn('jenis_pembayaran', ['Cash', 'Tunai'])->sum('harga_otr'),
            'dp' => (clone $totalQuery)->where('jenis_pembayaran', 'Kredit')->sum(DB::raw('uang_muka + tanda_jadi'))
        ];

        return view('laporan.penjualan.terperinci', compact('transactions', 'totals', 'dari_tanggal', 'sampai_tanggal', 'jenis_dokumen', 'search', 'per_page'));
    }

    public function printTerperinci(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');
        $jenis_dokumen = $request->input('jenis_dokumen', 'all');
        $search = $request->input('search');

        $query = Spk::with(['motorUnit.type', 'motorUnit.color'])->latest('tanggal');
        $query = $this->applyFilters($query, $dari_tanggal, $sampai_tanggal, $jenis_dokumen);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_spk', 'like', "%{$search}%")->orWhere('nama_pemohon', 'like', "%{$search}%")->orWhere('nama_stnk', 'like', "%{$search}%");
            });
        }

        $transactions = $query->get();
        return view('laporan.penjualan.print-terperinci', compact('transactions', 'dari_tanggal', 'sampai_tanggal', 'jenis_dokumen', 'search'));
    }
}
