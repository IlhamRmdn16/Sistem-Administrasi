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
        $isAdminGp = auth()->user()->hasRole('Admin GP');

        $config = [
            'ke-showroom' => ['kunci' => 'tujuan', 'val' => 'Showroom Pusat', 'judul' => 'Mutasi Ke Showroom', 'prefix' => 'MKS'],
            'dari-showroom' => ['kunci' => 'asal', 'val' => 'Showroom Pusat', 'judul' => 'Mutasi Dari Showroom', 'prefix' => 'MDS'],
            'ke-pop' => ['kunci' => 'tujuan', 'val' => 'POP', 'judul' => 'Mutasi Ke POP', 'prefix' => 'MKP'],
            'dari-pop' => ['kunci' => 'asal', 'val' => 'POP', 'judul' => 'Mutasi Dari POP', 'prefix' => 'MDP'],
            'ke-gp' => ['kunci' => 'tujuan', 'val' => 'Showroom GP', 'judul' => $isAdminGp ? 'Motor Masuk' : 'Mutasi Ke GP', 'prefix' => 'MKG'],
            'dari-gp' => ['kunci' => 'asal', 'val' => 'Showroom GP', 'judul' => $isAdminGp ? 'Motor Keluar' : 'Mutasi Dari GP', 'prefix' => 'MDG'],
            'antar-gudang' => ['kunci' => 'antar-gudang', 'val' => 'Gudang', 'judul' => 'Mutasi Antar Gudang', 'prefix' => 'MAG'],
        ];

        return $config[$jenis] ?? abort(404);
    }

    public function index(Request $request, $jenis)
    {
        $config = $this->getMutasiConfig($jenis);

        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);

        $query = Mutasi::with(['details.motorUnit', 'asalPop', 'tujuanPop']);

        if ($config['kunci'] === 'antar-gudang') {
            $query->whereIn('lokasi_asal', ['Gudang 1', 'Gudang 2'])
                  ->whereIn('lokasi_tujuan', ['Gudang 1', 'Gudang 2']);
        } else {
            $kolom = $config['kunci'] === 'asal' ? 'lokasi_asal' : 'lokasi_tujuan';
            $query->where($kolom, $config['val']);
        }

        if ($dari_tanggal && $sampai_tanggal) {
            $query->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal]);
        }

        if ($search) {
            $query->where('no_bukti', 'like', "%{$search}%");
        }

        $mutasis = $query->latest()->paginate($per_page)->withQueryString();

        $judul = $config['judul'];

        return view('transaction.mutasi.index', compact('mutasis', 'judul', 'jenis', 'dari_tanggal', 'sampai_tanggal', 'search', 'per_page'));
    }

    public function create($jenis)
    {
        $config = $this->getMutasiConfig($jenis);
        $pops = Sales::where('jenis_sales', 'pop')->get();

        $opsiAsal = $this->lokasiStatis;
        $opsiTujuan = $this->lokasiStatis;

        if ($jenis === 'antar-gudang') {
            $opsiAsal = ['Gudang 1', 'Gudang 2'];
            $opsiTujuan = ['Gudang 1', 'Gudang 2'];
        } elseif ($jenis === 'dari-gp') {
            $opsiTujuan = ['Gudang 1', 'Gudang 2', 'Showroom Pusat'];
        } elseif ($jenis === 'ke-gp') {
            $opsiAsal = ['Gudang 1', 'Gudang 2', 'Showroom Pusat', 'POP'];
        }

        $prefixLengkap = $config['prefix'] . date('Y/m/');
        $lastMutasi = Mutasi::where('no_bukti', 'like', $prefixLengkap . '%')
                            ->orderBy('id', 'desc')
                            ->first();

        $urut = $lastMutasi ? ((int) substr($lastMutasi->no_bukti, -4)) + 1 : 1;
        $noBukti = $prefixLengkap . str_pad($urut, 4, '0', STR_PAD_LEFT);

        $judul = $config['judul'];
        $kunciAsal = $config['kunci'] === 'asal' ? $config['val'] : '';
        $kunciTujuan = $config['kunci'] === 'tujuan' ? $config['val'] : '';

        return view('transaction.mutasi.create', compact('opsiAsal', 'opsiTujuan', 'pops', 'noBukti', 'judul', 'jenis', 'kunciAsal', 'kunciTujuan'));
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
            'lokasi_tujuan' => 'required|string|different:lokasi_asal',
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

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $mutasi = Mutasi::with('details')->findOrFail($id);

            foreach ($mutasi->details as $detail) {
                MotorUnit::where('id', $detail->motor_unit_id)->update([
                    'posisi_stok' => $mutasi->lokasi_asal,
                    'lokasi_pop_id' => $mutasi->lokasi_asal_pop_id,
                ]);
            }

            $mutasi->details()->delete();
            $mutasi->delete();

            DB::commit();
            return back()->with('success', 'Dokumen mutasi berhasil dibatalkan. Stok unit telah dikembalikan ke lokasi semula.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membatalkan mutasi: ' . $e->getMessage()]);
        }
    }
}
