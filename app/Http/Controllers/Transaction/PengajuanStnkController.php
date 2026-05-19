<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\BiayaAdministrasi;
use App\Models\PengajuanStnk;
use App\Models\PengajuanStnkDetail;
use App\Models\PengajuanStnkTambahan;
use App\Models\Samsat;
use Illuminate\Http\Request;

class PengajuanStnkController extends Controller
{
    public function index()
    {
        $now = now();
        $prefix = "PPN{$now->format('Y')}/{$now->format('m')}/";
        $existing = PengajuanStnk::where('no_bukti', 'like', "{$prefix}%")
            ->pluck('no_bukti')
            ->map(fn($n) => (int) substr($n, -4))
            ->toArray();

        $next = 1;
        while (in_array($next, $existing)) {
            $next++;
        }
        $autoNoBukti = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);

        $adm = BiayaAdministrasi::where('keterangan', 'like', '%ADM%')->first();
        $admValue = $adm ? $adm->nilai : 0;

        $usedSamsatIds = PengajuanStnkDetail::pluck('samsat_id')->toArray();
        $availableSamsats = Samsat::with(['suratJalan.spk.motorType', 'suratJalan.motorUnit'])
            ->whereNotNull('no_stnk')
            ->where('no_stnk', '!=', '')
            ->whereNotIn('id', $usedSamsatIds)
            ->get();

        return view('transaction.pengajuan-stnk.index', compact('autoNoBukti', 'admValue', 'availableSamsats'));
    }

    public function riwayat(Request $request)
    {
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);

        $query = PengajuanStnk::with(['details.samsat.suratJalan.spk.motorType', 'tambahans']);

        if ($search) {
            $query->where('no_bukti', 'like', "%{$search}%");
        }

        $pengajuans = $query->latest()->paginate($per_page)->withQueryString();

        $adm = BiayaAdministrasi::where('keterangan', 'like', '%ADM%')->first();
        $admValue = $adm ? $adm->nilai : 0;

        $usedSamsatIds = PengajuanStnkDetail::pluck('samsat_id')->toArray();
        $availableSamsats = Samsat::with(['suratJalan.spk.motorType', 'suratJalan.motorUnit'])
            ->whereNotNull('no_stnk')
            ->where('no_stnk', '!=', '')
            ->whereNotIn('id', $usedSamsatIds)
            ->get();

        return view('transaction.pengajuan-stnk.riwayat', compact('pengajuans', 'search', 'per_page', 'admValue', 'availableSamsats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_bukti' => 'required|unique:pengajuan_stnks,no_bukti',
            'tanggal' => 'required|date',
            'samsat_ids' => 'required|array|min:1',
        ]);

        $pengajuan = PengajuanStnk::create([
            'no_bukti' => $request->no_bukti,
            'tanggal' => $request->tanggal,
            'total_pajak' => $request->total_pajak,
            'total_adm' => $request->total_adm,
            'total_tambahan' => $request->total_tambahan,
            'grand_total' => $request->grand_total,
        ]);

        foreach ($request->samsat_ids as $samsat_id) {
            $samsat = Samsat::find($samsat_id);
            if ($samsat) {
                PengajuanStnkDetail::create([
                    'pengajuan_stnk_id' => $pengajuan->id,
                    'samsat_id' => $samsat->id,
                    'notice_pajak' => $samsat->piutang_notice_pajak,
                    'adm' => $request->adm_value,
                    'sub_total' => $samsat->piutang_notice_pajak + $request->adm_value,
                ]);
            }
        }

        if ($request->has('tambahans')) {
            foreach ($request->tambahans as $t) {
                if (!empty($t['keterangan']) && $t['nominal'] > 0) {
                    PengajuanStnkTambahan::create([
                        'pengajuan_stnk_id' => $pengajuan->id,
                        'keterangan' => $t['keterangan'],
                        'nominal' => $t['nominal'],
                        'total' => $t['nominal'] * count($request->samsat_ids),
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Data Pengajuan STNK berhasil disimpan!']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'samsat_ids' => 'required|array|min:1',
        ]);

        $pengajuan = PengajuanStnk::findOrFail($id);
        $pengajuan->update([
            'tanggal' => $request->tanggal,
            'total_pajak' => $request->total_pajak,
            'total_adm' => $request->total_adm,
            'total_tambahan' => $request->total_tambahan,
            'grand_total' => $request->grand_total,
        ]);

        $pengajuan->details()->delete();
        foreach ($request->samsat_ids as $samsat_id) {
            $samsat = Samsat::find($samsat_id);
            if ($samsat) {
                PengajuanStnkDetail::create([
                    'pengajuan_stnk_id' => $pengajuan->id,
                    'samsat_id' => $samsat->id,
                    'notice_pajak' => $samsat->piutang_notice_pajak,
                    'adm' => $request->adm_value,
                    'sub_total' => $samsat->piutang_notice_pajak + $request->adm_value,
                ]);
            }
        }

        $pengajuan->tambahans()->delete();
        if ($request->has('tambahans')) {
            foreach ($request->tambahans as $t) {
                if (!empty($t['keterangan']) && $t['nominal'] > 0) {
                    PengajuanStnkTambahan::create([
                        'pengajuan_stnk_id' => $pengajuan->id,
                        'keterangan' => $t['keterangan'],
                        'nominal' => $t['nominal'],
                        'total' => $t['nominal'] * count($request->samsat_ids),
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Data Pengajuan STNK berhasil diperbarui!']);
    }

    public function destroy($id)
    {
        PengajuanStnk::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Pengajuan dibatalkan, data dikembalikan dan No Bukti di-reset.']);
    }

    public function print($id)
    {
        $pengajuan = PengajuanStnk::with(['details.samsat.suratJalan.spk.motorType', 'details.samsat.suratJalan.motorUnit', 'tambahans'])->findOrFail($id);
        return view('transaction.pengajuan-stnk.print', compact('pengajuan'));
    }
}
