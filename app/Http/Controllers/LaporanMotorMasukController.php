<?php

namespace App\Http\Controllers;

use App\Models\MotorUnit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanMotorMasukController extends Controller
{
    public function global(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $data = MotorUnit::join('penerimaan_units', 'motor_units.penerimaan_unit_id', '=', 'penerimaan_units.id')
            ->join('motor_types', 'motor_units.motor_type_id', '=', 'motor_types.id')
            ->selectRaw('motor_types.kode_tipe, motor_types.nama_type, COUNT(motor_units.id) as jumlah_unit')
            ->whereBetween('penerimaan_units.tanggal', [$dari_tanggal, $sampai_tanggal])
            ->groupBy('motor_types.kode_tipe', 'motor_types.nama_type')
            ->orderBy('motor_types.nama_type', 'asc')
            ->get();

        $total_unit = $data->sum('jumlah_unit');

        return view('laporan.motor-masuk.global', compact('data', 'dari_tanggal', 'sampai_tanggal', 'total_unit'));
    }

    public function printGlobal(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');

        $data = MotorUnit::join('penerimaan_units', 'motor_units.penerimaan_unit_id', '=', 'penerimaan_units.id')
            ->join('motor_types', 'motor_units.motor_type_id', '=', 'motor_types.id')
            ->selectRaw('motor_types.kode_tipe, motor_types.nama_type, COUNT(motor_units.id) as jumlah_unit')
            ->whereBetween('penerimaan_units.tanggal', [$dari_tanggal, $sampai_tanggal])
            ->groupBy('motor_types.kode_tipe', 'motor_types.nama_type')
            ->orderBy('motor_types.nama_type', 'asc')
            ->get();

        $total_unit = $data->sum('jumlah_unit');

        return view('laporan.motor-masuk.print-global', compact('data', 'dari_tanggal', 'sampai_tanggal', 'total_unit'));
    }

    public function terperinci(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $per_page = $request->input('per_page', 25);

        $query = MotorUnit::with(['penerimaanUnit', 'type', 'color'])
            ->whereHas('penerimaanUnit', function($q) use ($dari_tanggal, $sampai_tanggal) {
                $q->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal]);
            })
            ->join('penerimaan_units', 'motor_units.penerimaan_unit_id', '=', 'penerimaan_units.id')
            ->orderBy('penerimaan_units.tanggal', 'desc')
            ->orderBy('penerimaan_units.no_bukti', 'desc')
            ->select('motor_units.*');

        $data = $query->paginate($per_page)->withQueryString();

        return view('laporan.motor-masuk.terperinci', compact('data', 'dari_tanggal', 'sampai_tanggal', 'per_page'));
    }

    public function printTerperinci(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');

        $data = MotorUnit::with(['penerimaanUnit', 'type', 'color'])
            ->whereHas('penerimaanUnit', function($q) use ($dari_tanggal, $sampai_tanggal) {
                $q->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal]);
            })
            ->join('penerimaan_units', 'motor_units.penerimaan_unit_id', '=', 'penerimaan_units.id')
            ->orderBy('penerimaan_units.tanggal', 'asc')
            ->orderBy('penerimaan_units.no_bukti', 'asc')
            ->select('motor_units.*')
            ->get();

        return view('laporan.motor-masuk.print-terperinci', compact('data', 'dari_tanggal', 'sampai_tanggal'));
    }
}
