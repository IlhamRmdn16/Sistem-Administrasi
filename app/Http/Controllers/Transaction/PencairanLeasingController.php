<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Leasing;
use App\Models\PencairanLeasing;
use App\Models\PencairanLeasingDetail;
use App\Models\SuratJalan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PencairanLeasingController extends Controller
{
    public function index(Request $request)
    {
        $leasings = Leasing::all();

        // Parameter Filter Riwayat
        $search = $request->input('search');
        $filter_leasing = $request->input('filter_leasing');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $per_page = $request->input('per_page', 10);

        $historyQuery = PencairanLeasing::with(['leasing', 'details.suratJalan.spk']);

        if ($filter_leasing) $historyQuery->where('leasing_id', $filter_leasing);
        if ($start_date && $end_date) $historyQuery->whereBetween('tanggal', [$start_date, $end_date]);
        elseif ($start_date) $historyQuery->where('tanggal', '>=', $start_date);
        elseif ($end_date) $historyQuery->where('tanggal', '<=', $end_date);

        if ($search) {
            $historyQuery->where(function($q) use ($search) {
                $q->where('no_bukti', 'like', "%{$search}%")
                  ->orWhereHas('leasing', function($qLeasing) use ($search) {
                      $qLeasing->where('nama_leasing', 'like', "%{$search}%");
                  });
            });
        }

        $histories = $historyQuery->latest()->paginate($per_page)->withQueryString();

        return view('transaction.pencairan-leasing.index', compact(
            'leasings', 'histories', 'search', 'filter_leasing', 'start_date', 'end_date', 'per_page'
        ));
    }

    public function getPending($leasing_id)
    {
        // Cari ID Surat Jalan yang sudah dicairkan sebelumnya
        $disbursedSjIds = PencairanLeasingDetail::pluck('surat_jalan_id')->toArray();

        // Tarik Surat Jalan Kredit milik Leasing ini yang BELUM dicairkan
        $pending = SuratJalan::with(['spk.kontrolHarga', 'motorUnit.type'])
            ->whereHas('spk', function($q) use ($leasing_id) {
                // Pastikan jenisnya kredit dan sesuai leasing
                $q->where('leasing_id', $leasing_id);
            })
            ->whereNotIn('id', $disbursedSjIds)
            ->get()
            ->map(function($sj) {
                $otr = $sj->spk->harga_otr ?? 0;
                $dp = $sj->spk->uang_muka ?? 0;
                $realisasi = $otr - $dp;

                // Ambil data DLL dari Kontrol Harga
                $dll_1 = $sj->spk->kontrolHarga->dll_1 ?? 0;
                $dll_2 = $sj->spk->kontrolHarga->dll_2 ?? 0;
                $total_dll = $dll_1 + $dll_2;

                return [
                    'id' => $sj->id,
                    'no_sj' => $sj->no_bukti,
                    'tanggal' => Carbon::parse($sj->tanggal)->format('d/m/Y'),
                    'nama_stnk' => $sj->spk->nama_stnk,
                    'tipe' => $sj->motorUnit->type->nama_type ?? '-',
                    'realisasi' => $realisasi > 0 ? $realisasi : 0,
                    'total_dll' => $total_dll,
                    'nilai_pencairan' => '', // Dikosongkan agar kasir input manual
                ];
            });

        return response()->json($pending);
    }

    public function store(Request $request)
    {
        $request->validate([
            'leasing_id' => 'required|exists:leasings,id',
            'tanggal' => 'required|date',
            'sj_ids' => 'required|array|min:1',
            'pencairan' => 'required|array', // Array input nilai pencairan dari form
        ]);

        try {
            DB::beginTransaction();

            // Generate No. Bukti PLP
            $now = Carbon::parse($request->tanggal);
            $prefix = 'PLP' . $now->format('Y/m/');

            $lastDoc = PencairanLeasing::where('no_bukti', 'like', $prefix . '%')
                ->lockForUpdate()->orderBy('id', 'desc')->first();
            $urut = $lastDoc ? ((int) substr($lastDoc->no_bukti, -4)) + 1 : 1;
            $no_bukti = $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);

            $pencairanInduk = PencairanLeasing::create([
                'no_bukti' => $no_bukti,
                'tanggal' => $request->tanggal,
                'leasing_id' => $request->leasing_id,
            ]);

            // Looping data yang diceklis
            foreach ($request->sj_ids as $sj_id) {
                $sj = SuratJalan::with('spk.kontrolHarga')->find($sj_id);
                if ($sj) {
                    $otr = $sj->spk->harga_otr ?? 0;
                    $dp = $sj->spk->uang_muka ?? 0;
                    $realisasi = ($otr - $dp) > 0 ? ($otr - $dp) : 0;

                    $dll_1 = $sj->spk->kontrolHarga->dll_1 ?? 0;
                    $dll_2 = $sj->spk->kontrolHarga->dll_2 ?? 0;
                    $total_dll = $dll_1 + $dll_2;

                    // Tarik nilai pencairan yang diinput kasir untuk baris sj_id ini
                    $nilai_pencairan = isset($request->pencairan[$sj_id]) ? (int) $request->pencairan[$sj_id] : 0;

                    // Kalkulasi Aktual
                    $selisih = $nilai_pencairan - $realisasi;
                    $margin = $selisih - $total_dll;

                    PencairanLeasingDetail::create([
                        'pencairan_leasing_id' => $pencairanInduk->id,
                        'surat_jalan_id' => $sj->id,
                        'nilai_pencairan' => $nilai_pencairan,
                        'nilai_realisasi' => $realisasi,
                        'estimasi_dll' => $total_dll,
                        'selisih_aktual' => $selisih,
                        'margin_lebih_kurang' => $margin,
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', "Pencairan berhasil disimpan dengan No Bukti: $no_bukti");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan pencairan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        PencairanLeasing::findOrFail($id)->delete();
        return back()->with('success', 'Data Pencairan berhasil dihapus. Unit kembali berstatus belum cair.');
    }
}
