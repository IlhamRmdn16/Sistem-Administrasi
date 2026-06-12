<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mutasi;
use App\Models\MutasiDetail;
use App\Models\MotorUnit;
use App\Models\Sales;
use Illuminate\Support\Facades\DB;

class MutasiStokController extends Controller
{
    private $lokasiStatis = ['Gudang 1', 'Gudang 2', 'Showroom Pusat', 'Showroom GP', 'POP'];

    private function getMutasiConfig($jenis)
    {
        $config = [
            'ke-showroom' => ['kunci' => 'tujuan', 'val' => 'Showroom Pusat', 'judul' => 'Mutasi Ke Showroom', 'prefix' => 'MKS'],
            'dari-showroom' => ['kunci' => 'asal', 'val' => 'Showroom Pusat', 'judul' => 'Mutasi Dari Showroom', 'prefix' => 'MDS'],
            'ke-pop' => ['kunci' => 'tujuan', 'val' => 'POP', 'judul' => 'Mutasi Ke POP', 'prefix' => 'MKP'],
            'dari-pop' => ['kunci' => 'asal', 'val' => 'POP', 'judul' => 'Mutasi Dari POP', 'prefix' => 'MDP'],
            'ke-gp' => ['kunci' => 'tujuan', 'val' => 'Showroom GP', 'judul' => 'Mutasi Ke GP', 'prefix' => 'MKG'],
            'dari-gp' => ['kunci' => 'asal', 'val' => 'Showroom GP', 'judul' => 'Mutasi Dari GP', 'prefix' => 'MDG'],
        ];

        return $config[$jenis] ?? abort(404);
    }

    public function index($jenis)
    {
        $config = $this->getMutasiConfig($jenis);
        $kolom = $config['kunci'] === 'asal' ? 'lokasi_asal' : 'lokasi_tujuan';

        $mutasis = Mutasi::with(['details.motorUnit', 'asalPop', 'tujuanPop'])
            ->where($kolom, $config['val'])
            ->latest()
            ->paginate(15);

        $judul = $config['judul'];

        return view('transaction.mutasi.index', compact('mutasis', 'judul', 'jenis'));
    }

    public function create($jenis)
    {
        $config = $this->getMutasiConfig($jenis);
        $lokasiStatis = $this->lokasiStatis;
        $pops = Sales::where('jenis_sales', 'pop')->get();

        $prefixLengkap = $config['prefix'] . date('Y/m/');
        $lastMutasi = Mutasi::where('no_bukti', 'like', $prefixLengkap . '%')
                            ->orderBy('id', 'desc')
                            ->first();

        $urut = $lastMutasi ? ((int) substr($lastMutasi->no_bukti, -4)) + 1 : 1;
        $noBukti = $prefixLengkap . str_pad($urut, 4, '0', STR_PAD_LEFT);

        $judul = $config['judul'];
        $kunciAsal = $config['kunci'] === 'asal' ? $config['val'] : '';
        $kunciTujuan = $config['kunci'] === 'tujuan' ? $config['val'] : '';

        return view('transaction.mutasi.create', compact('lokasiStatis', 'pops', 'noBukti', 'judul', 'jenis', 'kunciAsal', 'kunciTujuan'));
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

    public function store(Request $request, $jenis)
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
            return redirect()->route('mutasi.index', $jenis)->with('success', 'Mutasi berhasil disimpan!');
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
