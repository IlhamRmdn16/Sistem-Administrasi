<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanMutasiKeGpController extends Controller
{
    private function getJudul()
    {
        return Auth::user()->hasRole('Admin GP') ? 'Laporan Motor Masuk (GP)' : 'Laporan Mutasi Ke GP';
    }

    public function global(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $judul = $this->getJudul();

        $query = DB::table('mutasi_details')
            ->join('mutasis', 'mutasi_details.mutasi_id', '=', 'mutasis.id')
            ->join('motor_units', 'mutasi_details.motor_unit_id', '=', 'motor_units.id')
            ->join('motor_types', 'motor_units.motor_type_id', '=', 'motor_types.id')
            ->select('motor_types.kode_tipe', 'motor_types.nama_type', DB::raw('COUNT(mutasi_details.id) as jumlah_unit'))
            ->where('mutasis.lokasi_tujuan', 'Showroom GP')
            ->whereBetween('mutasis.tanggal', [$dari_tanggal, $sampai_tanggal]);

        $data = $query->groupBy('motor_types.kode_tipe', 'motor_types.nama_type')
            ->orderBy('motor_types.nama_type', 'asc')
            ->get();

        $total_unit = $data->sum('jumlah_unit');

        return view('laporan.mutasi-ke-gp.global', compact('data', 'dari_tanggal', 'sampai_tanggal', 'total_unit', 'judul'));
    }

    public function printGlobal(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');
        $judul = strtoupper($this->getJudul());

        $query = DB::table('mutasi_details')
            ->join('mutasis', 'mutasi_details.mutasi_id', '=', 'mutasis.id')
            ->join('motor_units', 'mutasi_details.motor_unit_id', '=', 'motor_units.id')
            ->join('motor_types', 'motor_units.motor_type_id', '=', 'motor_types.id')
            ->select('motor_types.kode_tipe', 'motor_types.nama_type', DB::raw('COUNT(mutasi_details.id) as jumlah_unit'))
            ->where('mutasis.lokasi_tujuan', 'Showroom GP')
            ->whereBetween('mutasis.tanggal', [$dari_tanggal, $sampai_tanggal]);

        $data = $query->groupBy('motor_types.kode_tipe', 'motor_types.nama_type')
            ->orderBy('motor_types.nama_type', 'asc')
            ->get();

        $total_unit = $data->sum('jumlah_unit');

        return view('laporan.mutasi-ke-gp.print-global', compact('data', 'dari_tanggal', 'sampai_tanggal', 'total_unit', 'judul'));
    }

    public function terperinci(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $per_page = $request->input('per_page', 25);
        $judul = $this->getJudul();

        $data = Mutasi::with(['details.motorUnit.type', 'details.motorUnit.color', 'asalPop'])
            ->where('lokasi_tujuan', 'Showroom GP')
            ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal])
            ->orderBy('tanggal', 'desc')
            ->orderBy('no_bukti', 'desc')
            ->paginate($per_page)
            ->withQueryString();

        return view('laporan.mutasi-ke-gp.terperinci', compact('data', 'dari_tanggal', 'sampai_tanggal', 'per_page', 'judul'));
    }

    public function printTerperinci(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');
        $judul = strtoupper($this->getJudul());

        $data = Mutasi::with(['details.motorUnit.type', 'details.motorUnit.color', 'asalPop'])
            ->where('lokasi_tujuan', 'Showroom GP')
            ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal])
            ->orderBy('tanggal', 'asc')
            ->orderBy('no_bukti', 'asc')
            ->get();

        return view('laporan.mutasi-ke-gp.print-terperinci', compact('data', 'dari_tanggal', 'sampai_tanggal', 'judul'));
    }
}
