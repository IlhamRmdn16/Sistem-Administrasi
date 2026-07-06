<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use App\Models\MutasiDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanMutasiShowroomController extends Controller
{
    public function global(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $data = MutasiDetail::join('mutasis', 'mutasi_details.mutasi_id', '=', 'mutasis.id')
            ->join('motor_units', 'mutasi_details.motor_unit_id', '=', 'motor_units.id')
            ->join('motor_types', 'motor_units.motor_type_id', '=', 'motor_types.id')
            ->selectRaw('motor_types.kode_tipe, motor_types.nama_type, COUNT(mutasi_details.id) as jumlah_unit')
            ->where('mutasis.lokasi_tujuan', 'Showroom Pusat')
            ->whereBetween('mutasis.tanggal', [$dari_tanggal, $sampai_tanggal])
            ->groupBy('motor_types.kode_tipe', 'motor_types.nama_type')
            ->orderBy('motor_types.nama_type', 'asc')
            ->get();

        $total_unit = $data->sum('jumlah_unit');

        return view('laporan.mutasi-showroom.global', compact('data', 'dari_tanggal', 'sampai_tanggal', 'total_unit'));
    }

    public function printGlobal(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');

        $data = MutasiDetail::join('mutasis', 'mutasi_details.mutasi_id', '=', 'mutasis.id')
            ->join('motor_units', 'mutasi_details.motor_unit_id', '=', 'motor_units.id')
            ->join('motor_types', 'motor_units.motor_type_id', '=', 'motor_types.id')
            ->selectRaw('motor_types.kode_tipe, motor_types.nama_type, COUNT(mutasi_details.id) as jumlah_unit')
            ->where('mutasis.lokasi_tujuan', 'Showroom Pusat')
            ->whereBetween('mutasis.tanggal', [$dari_tanggal, $sampai_tanggal])
            ->groupBy('motor_types.kode_tipe', 'motor_types.nama_type')
            ->orderBy('motor_types.nama_type', 'asc')
            ->get();

        $total_unit = $data->sum('jumlah_unit');

        return view('laporan.mutasi-showroom.print-global', compact('data', 'dari_tanggal', 'sampai_tanggal', 'total_unit'));
    }

    public function terperinci(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $per_page = $request->input('per_page', 25);

        $data = Mutasi::with(['details.motorUnit.type', 'details.motorUnit.color'])
            ->where('lokasi_tujuan', 'Showroom Pusat')
            ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal])
            ->orderBy('tanggal', 'desc')
            ->orderBy('no_bukti', 'desc')
            ->paginate($per_page)
            ->withQueryString();

        return view('laporan.mutasi-showroom.terperinci', compact('data', 'dari_tanggal', 'sampai_tanggal', 'per_page'));
    }

    public function printTerperinci(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');

        $data = Mutasi::with(['details.motorUnit.type', 'details.motorUnit.color'])
            ->where('lokasi_tujuan', 'Showroom Pusat')
            ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal])
            ->orderBy('tanggal', 'asc')
            ->orderBy('no_bukti', 'asc')
            ->get();

        return view('laporan.mutasi-showroom.print-terperinci', compact('data', 'dari_tanggal', 'sampai_tanggal'));
    }
}
