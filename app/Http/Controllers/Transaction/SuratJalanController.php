<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\MotorUnit;
use App\Models\PdiMan;
use App\Models\Spk;
use App\Models\SuratJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuratJalanController extends Controller
{
  public function index(Request $request)
    {
        $search = $request->input('search');
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');
        $per_page = $request->input('per_page', 10);

        // Tambahkan relasi motorUnit.lokasiPop agar bisa ditampilkan
        $query = SuratJalan::with(['spk', 'motorUnit.type', 'motorUnit.color', 'motorUnit.lokasiPop', 'pdiMan']);

        if ($dari_tanggal && $sampai_tanggal) {
            $query->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal]);
        }

        if ($search) {
            $query->where('no_bukti', 'like', "%{$search}%")
                  ->orWhere('no_stck', 'like', "%{$search}%")
                  ->orWhereHas('spk', function ($q) use ($search) {
                      $q->where('nama_pemohon', 'like', "%{$search}%")
                        ->orWhere('no_spk', 'like', "%{$search}%");
                  });
        }

        $suratJalans = $query->latest()->paginate($per_page)->withQueryString();

        $now = now();
        $prefix = "SJK{$now->format('Y')}/{$now->format('m')}/";

        $existingNumbers = SuratJalan::where('no_bukti', 'like', "{$prefix}%")
            ->pluck('no_bukti')
            ->map(function ($no_bukti) {
                return (int) preg_replace('/[^0-9]/', '', substr($no_bukti, -4));
            })
            ->toArray();

        $nextNumber = 1;
        while (in_array($nextNumber, $existingNumbers)) {
            $nextNumber++;
        }

        $autoNoBukti = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $usedSpkIds = SuratJalan::pluck('spk_id')->toArray();
        // Tarik data SPK yang motornya juga ditarik info lokasinya
        $availableSpks = Spk::whereNotIn('id', $usedSpkIds)->with(['motorUnit.type', 'motorUnit.color', 'motorUnit.lokasiPop'])->get();

        $pdiMans = PdiMan::orderBy('nama_pdi_man')->get();

        return view('transaction.suratjalan.index', compact('suratJalans', 'autoNoBukti', 'availableSpks', 'pdiMans', 'dari_tanggal', 'sampai_tanggal', 'per_page', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_bukti' => 'required|unique:surat_jalans,no_bukti',
            'tanggal' => 'required|date',
            'spk_id' => 'required',
            'motor_unit_id' => 'required',
            'pdi_man_id' => 'required',
            'no_stck' => 'nullable|string',
            'no_registrasi' => 'nullable|string',
            'berlaku_sd' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat Surat Jalan
            SuratJalan::create($request->all());

            // 2. Ubah Status Motor Menjadi Terjual
            MotorUnit::where('id', $request->motor_unit_id)->update([
                'status_unit' => 'Terjual'
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Surat Jalan berhasil diterbitkan! Stok unit otomatis berkurang.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menerbitkan Surat Jalan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'no_bukti' => 'required|unique:surat_jalans,no_bukti,' . $id,
            'tanggal' => 'required|date',
            'spk_id' => 'required',
            'motor_unit_id' => 'required',
            'pdi_man_id' => 'required',
            'no_stck' => 'nullable|string',
            'no_registrasi' => 'nullable|string',
            'berlaku_sd' => 'nullable|date',
        ]);

        SuratJalan::findOrFail($id)->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Surat Jalan berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $sj = SuratJalan::findOrFail($id);
     
            MotorUnit::where('id', $sj->motor_unit_id)->update([
                'status_unit' => 'Tersedia'
            ]);

            $sj->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Surat Jalan dihapus! Stok unit dikembalikan sebagai Tersedia.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Surat Jalan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function print($id)
    {
        $sj = SuratJalan::with(['spk', 'motorUnit.type', 'motorUnit.color', 'pdiMan'])->findOrFail($id);
        return view('transaction.suratjalan.print', compact('sj'));
    }
}
