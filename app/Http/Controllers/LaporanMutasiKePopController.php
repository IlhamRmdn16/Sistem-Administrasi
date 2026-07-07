<?php

namespace App\Http\Controllers;

use App\Models\Mutasi;
use App\Models\Sales;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanMutasiKePopController extends Controller
{
    private $kolomFkPop = 'lokasi_tujuan_pop_id'; 

    public function global(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $pop_id = $request->input('pop_id', 'semua');

        $query = DB::table('mutasi_details')
            ->join('mutasis', 'mutasi_details.mutasi_id', '=', 'mutasis.id')
            ->join('motor_units', 'mutasi_details.motor_unit_id', '=', 'motor_units.id')
            ->join('motor_types', 'motor_units.motor_type_id', '=', 'motor_types.id')
            ->leftJoin('sales', 'mutasis.' . $this->kolomFkPop, '=', 'sales.id')
            ->select('sales.nama_sales as nama_pop', 'motor_types.kode_tipe', 'motor_types.nama_type', DB::raw('COUNT(mutasi_details.id) as jumlah_unit'))
            ->where('mutasis.lokasi_tujuan', 'POP')
            ->whereBetween('mutasis.tanggal', [$dari_tanggal, $sampai_tanggal]);

        if ($pop_id !== 'semua') {
            $query->where('mutasis.' . $this->kolomFkPop, $pop_id);
        }

        $data = $query->groupBy('sales.nama_sales', 'motor_types.kode_tipe', 'motor_types.nama_type')
            ->orderBy('sales.nama_sales', 'asc')
            ->orderBy('motor_types.nama_type', 'asc')
            ->get();

        $total_unit = $data->sum('jumlah_unit');
        
        $pops = Sales::orderBy('nama_sales', 'asc')->get();

        return view('laporan.mutasi-ke-pop.global', compact('data', 'dari_tanggal', 'sampai_tanggal', 'total_unit', 'pops', 'pop_id'));
    }

    public function printGlobal(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');
        $pop_id = $request->input('pop_id', 'semua');

        $query = DB::table('mutasi_details')
            ->join('mutasis', 'mutasi_details.mutasi_id', '=', 'mutasis.id')
            ->join('motor_units', 'mutasi_details.motor_unit_id', '=', 'motor_units.id')
            ->join('motor_types', 'motor_units.motor_type_id', '=', 'motor_types.id')
            ->leftJoin('sales', 'mutasis.' . $this->kolomFkPop, '=', 'sales.id')
            ->select('sales.nama_sales as nama_pop', 'motor_types.kode_tipe', 'motor_types.nama_type', DB::raw('COUNT(mutasi_details.id) as jumlah_unit'))
            ->where('mutasis.lokasi_tujuan', 'POP')
            ->whereBetween('mutasis.tanggal', [$dari_tanggal, $sampai_tanggal]);

        if ($pop_id !== 'semua') {
            $query->where('mutasis.' . $this->kolomFkPop, $pop_id);
        }

        $data = $query->groupBy('sales.nama_sales', 'motor_types.kode_tipe', 'motor_types.nama_type')
            ->orderBy('sales.nama_sales', 'asc')
            ->orderBy('motor_types.nama_type', 'asc')
            ->get();

        $total_unit = $data->sum('jumlah_unit');
        $nama_pop_filter = $pop_id !== 'semua' ? Sales::find($pop_id)->nama_sales ?? 'Semua POP' : 'Semua POP';

        return view('laporan.mutasi-ke-pop.print-global', compact('data', 'dari_tanggal', 'sampai_tanggal', 'total_unit', 'nama_pop_filter'));
    }

    public function terperinci(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $pop_id = $request->input('pop_id', 'semua');
        $per_page = $request->input('per_page', 25);

        $query = Mutasi::with(['details.motorUnit.type', 'details.motorUnit.color', 'tujuanPop'])
            ->where('lokasi_tujuan', 'POP')
            ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal]);

        if ($pop_id !== 'semua') {
            $query->where($this->kolomFkPop, $pop_id);
        }

        $data = $query->orderBy('tanggal', 'desc')
            ->orderBy('no_bukti', 'desc')
            ->paginate($per_page)
            ->withQueryString();

        $pops = Sales::orderBy('nama_sales', 'asc')->get();

        return view('laporan.mutasi-ke-pop.terperinci', compact('data', 'dari_tanggal', 'sampai_tanggal', 'per_page', 'pops', 'pop_id'));
    }

    public function printTerperinci(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');
        $pop_id = $request->input('pop_id', 'semua');

        $query = Mutasi::with(['details.motorUnit.type', 'details.motorUnit.color', 'tujuanPop'])
            ->where('lokasi_tujuan', 'POP')
            ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal]);

        if ($pop_id !== 'semua') {
            $query->where($this->kolomFkPop, $pop_id);
        }

        $data = $query->orderBy('tanggal', 'asc')
            ->orderBy('no_bukti', 'asc')
            ->get();
            
        $nama_pop_filter = $pop_id !== 'semua' ? Sales::find($pop_id)->nama_sales ?? 'Semua POP' : 'Semua POP';

        return view('laporan.mutasi-ke-pop.print-terperinci', compact('data', 'dari_tanggal', 'sampai_tanggal', 'nama_pop_filter'));
    }
}
