<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Leasing;
use App\Models\PenagihanLeasing;
use App\Models\PenagihanLeasingDetail;
use App\Models\SuratJalan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenagihanLeasingController extends Controller
{
    public function index(Request $request)
    {
        $leasings = Leasing::all();

        $historyQuery = PenagihanLeasing::with(['leasing', 'details']);
        if ($request->has('filter_leasing') && $request->filter_leasing != '') {
            $historyQuery->where('leasing_id', $request->filter_leasing);
        }
        $histories = $historyQuery->latest()->paginate(15)->withQueryString();

        return view('transaction.penagihan-leasing.index', compact('leasings', 'histories'));
    }

    // Endpoint API untuk menarik SJK yang belum ditagih berdasarkan Leasing terpilih
    public function getPending($leasing_id)
    {
        // Cari ID Surat Jalan yang sudah masuk ke tabel penagihan detail
        $billedSjIds = PenagihanLeasingDetail::pluck('surat_jalan_id')->toArray();

        // Tarik Surat Jalan yang pakai leasing tersebut, dan belum ada di $billedSjIds
        $pending = SuratJalan::with(['spk', 'motorUnit.type'])
            ->whereHas('spk', function($q) use ($leasing_id) {
                $q->where('leasing_id', $leasing_id);
            })
            ->whereNotIn('id', $billedSjIds)
            ->get()
            ->map(function($sj) {
                $otr = $sj->spk->harga_otr ?? 0;
                $dp = $sj->spk->uang_muka ?? 0;
                $sisa = $otr - $dp;

                return [
                    'id' => $sj->id,
                    'no_sj' => $sj->no_bukti,
                    'tanggal' => Carbon::parse($sj->tanggal)->format('d/m/Y'),
                    'no_kunci' => $sj->motorUnit->no_kunci ?? '-',
                    'nama_stnk' => $sj->spk->nama_stnk,
                    'alamat' => $sj->spk->alamat,
                    'tipe' => $sj->motorUnit->type->nama_type ?? '-',
                    'no_mesin' => $sj->motorUnit->no_mesin ?? '-',
                    'no_rangka' => $sj->motorUnit->no_rangka ?? '-',
                    'otr' => $otr,
                    'dp_po' => $dp,
                    'sisa' => $sisa > 0 ? $sisa : 0
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
        ]);

        try {
            DB::beginTransaction();

            $now = Carbon::parse($request->tanggal);
            $prefix = 'BTL' . $now->format('Y/m/');

            $lastDoc = PenagihanLeasing::where('no_bukti', 'like', $prefix . '%')
                ->lockForUpdate()->orderBy('id', 'desc')->first();
            $urut = $lastDoc ? ((int) substr($lastDoc->no_bukti, -4)) + 1 : 1;
            $no_bukti = $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);

            $penagihan = PenagihanLeasing::create([
                'no_bukti' => $no_bukti,
                'tanggal' => $request->tanggal,
                'leasing_id' => $request->leasing_id,
            ]);

            foreach ($request->sj_ids as $sj_id) {
                $sj = SuratJalan::with('spk')->find($sj_id);
                if ($sj) {
                    $otr = $sj->spk->harga_otr ?? 0;
                    $dp = $sj->spk->uang_muka ?? 0;

                    PenagihanLeasingDetail::create([
                        'penagihan_leasing_id' => $penagihan->id,
                        'surat_jalan_id' => $sj->id,
                        'otr' => $otr,
                        'dp_po' => $dp,
                        'sisa' => ($otr - $dp) > 0 ? ($otr - $dp) : 0,
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', "Penagihan berhasil dibuat dengan No Bukti: $no_bukti")->with('print_id', $penagihan->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan penagihan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        PenagihanLeasing::findOrFail($id)->delete();
        return back()->with('success', 'Data Penagihan berhasil dihapus. Surat Jalan dikembalikan ke daftar tunggu.');
    }

    public function print($id)
    {
        $penagihan = PenagihanLeasing::with(['leasing', 'details.suratJalan.spk', 'details.suratJalan.motorUnit.type'])->findOrFail($id);
        return view('transaction.penagihan-leasing.print', compact('penagihan'));
    }
}
