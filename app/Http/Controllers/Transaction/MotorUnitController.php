<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\MotorType;
use App\Models\MotorUnit;
use App\Models\PenerimaanUnit;
use App\Models\Sales;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MotorUnitController extends Controller
{
   public function index(Request $request)
    {
        $search = $request->input('search');
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');
        $per_page = $request->input('per_page', 10);

        $query = PenerimaanUnit::with(['motorUnits.type', 'motorUnits.color'])->withCount('motorUnits');

        if ($dari_tanggal && $sampai_tanggal) {
            $query->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal]);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_bukti', 'like', "%{$search}%")
                  ->orWhere('no_sj', 'like', "%{$search}%")
                  ->orWhereHas('motorUnits', function($sub) use ($search) {
                      $sub->where('no_mesin', 'like', "%{$search}%")
                          ->orWhere('no_rangka', 'like', "%{$search}%");
                  });
            });
        }

        $penerimaanData = $query->latest()->paginate($per_page)->withQueryString();

        return view('transaction.motor-unit.index', compact('penerimaanData', 'search', 'dari_tanggal', 'sampai_tanggal', 'per_page'));
    }

    public function create()
    {
        $types = MotorType::with('colors')->get();
        $lokasiStatis = ['Showroom Pusat', 'Showroom GP', 'Gudang 1', 'Gudang 2'];
        $pops = Sales::where('jenis_sales', 'pop')->get();

        return view('transaction.motor-unit.create', compact('types', 'lokasiStatis', 'pops'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'no_kendaraan' => 'required',
            'ekspedisi' => 'required',
            'no_sj' => 'required',
            'details' => 'required|array|min:1',
            'details.*.motor_type_id' => 'required',
            'details.*.motor_color_id' => 'required',
            'details.*.no_mesin' => 'required|unique:motor_units,no_mesin',
            'details.*.no_rangka' => 'required|unique:motor_units,no_rangka',
            'details.*.no_seri_kunci' => 'required',
            'details.*.no_kunci' => 'required',
            'details.*.tahun_pembuatan' => 'required',
            'details.*.no_accu' => 'required',
            'details.*.posisi_stok' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $date = Carbon::parse($request->tanggal);
            $year = $date->format('Y');
            $month = $date->format('m');
            $prefix = "BLI{$year}/{$month}/";

            $lastRecord = PenerimaanUnit::where('no_bukti', 'like', "{$prefix}%")
                ->orderBy('no_bukti', 'desc')
                ->lockForUpdate()
                ->first();

            $nextSequence = 1;
            if ($lastRecord) {
                $lastSequenceStr = substr($lastRecord->no_bukti, -4);
                $nextSequence = (int)$lastSequenceStr + 1;
            }
            $no_bukti = $prefix . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);

            $penerimaan = PenerimaanUnit::create([
                'no_bukti' => $no_bukti,
                'tanggal' => $request->tanggal,
                'no_kendaraan' => $request->no_kendaraan,
                'ekspedisi' => $request->ekspedisi,
                'no_sj' => $request->no_sj,
                'no_nd' => $request->no_nd,
                'no_so' => $request->no_so,
            ]);

            foreach ($request->details as $detail) {
                MotorUnit::create([
                    'penerimaan_unit_id' => $penerimaan->id,
                    'motor_type_id' => $detail['motor_type_id'],
                    'motor_color_id' => $detail['motor_color_id'],
                    'no_mesin' => strtoupper($detail['no_mesin']),
                    'no_rangka' => strtoupper($detail['no_rangka']),
                    'no_seri_kunci' => strtoupper($detail['no_seri_kunci']),
                    'no_kunci' => strtoupper($detail['no_kunci']),
                    'tahun_pembuatan' => $detail['tahun_pembuatan'],
                    'no_accu' => strtoupper($detail['no_accu']),
                    'posisi_stok' => $detail['posisi_stok'],
                    'lokasi_pop_id' => $detail['posisi_stok'] === 'POP' ? $detail['lokasi_pop_id'] : null,
                    'status_unit' => 'Tersedia'
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Registrasi grup unit masuk berhasil disimpan! No Bukti: ' . $no_bukti]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $penerimaan = PenerimaanUnit::with('motorUnits.type', 'motorUnits.color')->findOrFail($id);
        $types = MotorType::with('colors')->get();
        $lokasiStatis = ['Showroom Pusat', 'Showroom GP', 'Gudang 1', 'Gudang 2'];
        $pops = Sales::where('jenis_sales', 'pop')->get();

        return view('transaction.motor-unit.edit', compact('penerimaan', 'types', 'lokasiStatis', 'pops'));
    }

    public function update(Request $request, $id)
    {
        $penerimaan = PenerimaanUnit::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'no_kendaraan' => 'required',
            'ekspedisi' => 'required',
            'no_sj' => 'required',
            'details' => 'required|array|min:1',
            'details.*.motor_type_id' => 'required',
            'details.*.motor_color_id' => 'required',
            'details.*.no_mesin' => 'required',
            'details.*.no_rangka' => 'required',
            'details.*.no_seri_kunci' => 'required',
            'details.*.no_kunci' => 'required',
            'details.*.tahun_pembuatan' => 'required',
            'details.*.no_accu' => 'required',
            'details.*.posisi_stok' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $penerimaan->update([
                'tanggal' => $request->tanggal,
                'no_kendaraan' => $request->no_kendaraan,
                'ekspedisi' => $request->ekspedisi,
                'no_sj' => $request->no_sj,
                'no_nd' => $request->no_nd,
                'no_so' => $request->no_so,
            ]);

            $existingIds = [];
            foreach ($request->details as $detail) {
                if (isset($detail['id']) && !empty($detail['id'])) {
                    $unit = MotorUnit::findOrFail($detail['id']);
                    $unit->update([
                        'motor_type_id' => $detail['motor_type_id'],
                        'motor_color_id' => $detail['motor_color_id'],
                        'no_mesin' => strtoupper($detail['no_mesin']),
                        'no_rangka' => strtoupper($detail['no_rangka']),
                        'no_seri_kunci' => strtoupper($detail['no_seri_kunci']),
                        'no_kunci' => strtoupper($detail['no_kunci']),
                        'tahun_pembuatan' => $detail['tahun_pembuatan'],
                        'no_accu' => strtoupper($detail['no_accu']),
                        'posisi_stok' => $detail['posisi_stok'],
                        'lokasi_pop_id' => $detail['posisi_stok'] === 'POP' ? $detail['lokasi_pop_id'] : null,
                    ]);
                    $existingIds[] = $unit->id;
                } else {
                    $newUnit = MotorUnit::create([
                        'penerimaan_unit_id' => $penerimaan->id,
                        'motor_type_id' => $detail['motor_type_id'],
                        'motor_color_id' => $detail['motor_color_id'],
                        'no_mesin' => strtoupper($detail['no_mesin']),
                        'no_rangka' => strtoupper($detail['no_rangka']),
                        'no_seri_kunci' => strtoupper($detail['no_seri_kunci']),
                        'no_kunci' => strtoupper($detail['no_kunci']),
                        'tahun_pembuatan' => $detail['tahun_pembuatan'],
                        'no_accu' => strtoupper($detail['no_accu']),
                        'posisi_stok' => $detail['posisi_stok'],
                        'lokasi_pop_id' => $detail['posisi_stok'] === 'POP' ? $detail['lokasi_pop_id'] : null,
                        'status_unit' => 'Tersedia'
                    ]);
                    $existingIds[] = $newUnit->id;
                }
            }

            MotorUnit::where('penerimaan_unit_id', $penerimaan->id)
                ->whereNotIn('id', $existingIds)
                ->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data penerimaan unit berhasil diperbarui!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $penerimaan = PenerimaanUnit::findOrFail($id);
        $penerimaan->delete();

        return response()->json(['success' => true, 'message' => 'Seluruh grup data penerimaan unit berhasil dihapus!']);
    }

    public function print($id)
    {
        $unit = MotorUnit::with(['type', 'color', 'penerimaanUnit'])->findOrFail($id);
        return view('transaction.motor-unit.print', compact('unit'));
    }
}
