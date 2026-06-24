<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanAccuController extends Controller
{
    public function mutasiPenjualan(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal', date('Y-m-01'));
        $sampai_tanggal = $request->input('sampai_tanggal', date('Y-m-d'));

        $reports = DB::table('surat_jalans')
            ->join('motor_units', 'surat_jalans.motor_unit_id', '=', 'motor_units.id')
            ->join('motor_types', 'motor_units.motor_type_id', '=', 'motor_types.id')
            ->join('spks', 'surat_jalans.spk_id', '=', 'spks.id')
            ->select(
                'motor_units.no_accu',
                'surat_jalans.tanggal as tanggal_sjk',
                'motor_units.no_kunci',
                'motor_types.nama_type',
                'spks.nama_pemohon'
            )
            ->whereNotNull('motor_units.no_accu')
            ->where('motor_units.no_accu', '!=', '')
            ->whereBetween('surat_jalans.tanggal', [$dari_tanggal, $sampai_tanggal])
            ->orderBy('surat_jalans.tanggal', 'desc')
            ->get();

        return view('laporan.accu.mutasi', compact('reports', 'dari_tanggal', 'sampai_tanggal'));
    }

    public function printMutasiPenjualan(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');

        $reports = DB::table('surat_jalans')
            ->join('motor_units', 'surat_jalans.motor_unit_id', '=', 'motor_units.id')
            ->join('motor_types', 'motor_units.motor_type_id', '=', 'motor_types.id')
            ->join('spks', 'surat_jalans.spk_id', '=', 'spks.id')
            ->select(
                'motor_units.no_accu',
                'surat_jalans.tanggal as tanggal_sjk',
                'motor_units.no_kunci',
                'motor_types.nama_type',
                'spks.nama_pemohon'
            )
            ->whereNotNull('motor_units.no_accu')
            ->where('motor_units.no_accu', '!=', '')
            ->whereBetween('surat_jalans.tanggal', [$dari_tanggal, $sampai_tanggal])
            ->orderBy('surat_jalans.tanggal', 'asc')
            ->get();

        return view('laporan.accu.print-mutasi', compact('reports', 'dari_tanggal', 'sampai_tanggal'));
    }

    public function stok(Request $request)
    {
        $reports = DB::table('motor_units')
            ->join('motor_types', 'motor_units.motor_type_id', '=', 'motor_types.id')
            ->leftJoin('surat_jalans', 'motor_units.id', '=', 'surat_jalans.motor_unit_id')
            ->leftJoin('sales', 'motor_units.lokasi_pop_id', '=', 'sales.id')
            ->select(
                'motor_units.no_accu',
                'motor_units.no_kunci',
                'motor_types.nama_type',
                DB::raw("CASE WHEN motor_units.posisi_stok = 'POP' THEN CONCAT('POP - ', IFNULL(sales.nama_sales, 'Unknown')) ELSE motor_units.posisi_stok END as posisi_display")
            )
            ->whereNotNull('motor_units.no_accu')
            ->where('motor_units.no_accu', '!=', '')
            ->whereNull('surat_jalans.id')
            ->orderBy('motor_units.no_accu', 'asc')
            ->get();

        return view('laporan.accu.stok', compact('reports'));
    }

    public function printStok(Request $request)
    {
        $reports = DB::table('motor_units')
            ->join('motor_types', 'motor_units.motor_type_id', '=', 'motor_types.id')
            ->leftJoin('surat_jalans', 'motor_units.id', '=', 'surat_jalans.motor_unit_id')
            ->leftJoin('sales', 'motor_units.lokasi_pop_id', '=', 'sales.id')
            ->select(
                'motor_units.no_accu',
                'motor_units.no_kunci',
                'motor_types.nama_type',
                DB::raw("CASE WHEN motor_units.posisi_stok = 'POP' THEN CONCAT('POP - ', IFNULL(sales.nama_sales, 'Unknown')) ELSE motor_units.posisi_stok END as posisi_display")
            )
            ->whereNotNull('motor_units.no_accu')
            ->where('motor_units.no_accu', '!=', '')
            ->whereNull('surat_jalans.id')
            ->orderBy('motor_units.no_accu', 'asc')
            ->get();

        return view('laporan.accu.print-stok', compact('reports'));
    }
}
