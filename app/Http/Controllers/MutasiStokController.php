<?php

namespace App\Http\Controllers;

use App\Models\MotorUnit;
use App\Models\Mutasi;
use App\Models\MutasiDetail;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasiStokController extends Controller
{
    private $lokasiStatis = ['Gudang 1', 'Gudang 2', 'Showroom Pusat', 'Showroom GP', 'POP'];

    public function index()
    {
        $mutasis = Mutasi::with(['details.motorUnit', 'asalPop', 'tujuanPop'])->latest()->paginate(15);
        return view('transaction.mutasi.index', compact('mutasis'));
    }

    public function create()
    {
        $lokasiStatis = $this->lokasiStatis;
        $pops = Sales::where('jenis_sales', 'pop')->get();

        $lastMutasi = Mutasi::whereMonth('tanggal', date('m'))->whereYear('tanggal', date('Y'))->count() + 1;
        $noBukti = 'MKG' . date('Y/m/') . str_pad($lastMutasi, 4, '0', STR_PAD_LEFT);

        return view('transaction.mutasi.create', compact('lokasiStatis', 'pops', 'noBukti'));
    }

    public function getAvailableUnits(Request $request)
    {
        $query = MotorUnit::with(['type', 'color'])
                    ->where('status_unit', 'Tersedia')
                    ->where('posisi_stok', $request->posisi_stok);

        if ($request->posisi_stok === 'POP') {
            $query->where('lokasi_pop_id', $request->lokasi_pop_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_bukti' => 'required|unique:mutasis',
            'tanggal' => 'required|date',
            'lokasi_asal' => 'required|string',
            'lokasi_tujuan' => 'required|string',
            'motor_unit_ids' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $mutasi = Mutasi::create([
                'no_bukti' => $request->no_bukti,
                'tanggal' => $request->tanggal,
                'lokasi_asal' => $request->lokasi_asal,
                'lokasi_asal_pop_id' => $request->lokasi_asal === 'POP' ? $request->lokasi_asal_pop_id : null,
                'lokasi_tujuan' => $request->lokasi_tujuan,
                'lokasi_tujuan_pop_id' => $request->lokasi_tujuan === 'POP' ? $request->lokasi_tujuan_pop_id : null,
                'keterangan' => $request->keterangan,
            ]);

            foreach ($request->motor_unit_ids as $unitId) {
                MutasiDetail::create([
                    'mutasi_id' => $mutasi->id,
                    'motor_unit_id' => $unitId
                ]);

                MotorUnit::where('id', $unitId)->update([
                    'posisi_stok' => $request->lokasi_tujuan,
                    'lokasi_pop_id' => $request->lokasi_tujuan === 'POP' ? $request->lokasi_tujuan_pop_id : null,
                ]);
            }

            DB::commit();
            return redirect()->route('mutasi-stok.index')->with('success', 'Mutasi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses mutasi: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $mutasi = Mutasi::with(['details.motorUnit.type', 'details.motorUnit.color', 'asalPop', 'tujuanPop'])->findOrFail($id);
        return view('transaction.mutasi.show', compact('mutasi'));
    }
}
