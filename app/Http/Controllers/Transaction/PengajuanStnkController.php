<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\BiayaAdministrasi;
use App\Models\PengajuanStnk;
use App\Models\PengajuanStnkDetail;
use App\Models\PengajuanStnkTambahan;
use App\Models\SuratJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanStnkController extends Controller
{
   private function getAdmValue()
    {
        $adm = BiayaAdministrasi::where('kode_sistem', 'ADM')->first();
        return $adm ? (int) $adm->nilai : 0;
    }

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

        $admValue = $this->getAdmValue();

        $usedSuratJalanIds = PengajuanStnkDetail::pluck('surat_jalan_id')->toArray();
        $availableSuratJalans = SuratJalan::with(['spk.motorType', 'motorUnit'])
            ->whereNotIn('id', $usedSuratJalanIds)
            ->get();

        return view('transaction.pengajuan-stnk.index', compact('autoNoBukti', 'admValue', 'availableSuratJalans'));
    }

    public function riwayat(Request $request)
    {
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);

        $query = PengajuanStnk::with(['details.suratJalan.spk.motorType', 'tambahans']);

        if ($search) {
            $query->where('no_bukti', 'like', "%{$search}%");
        }

        $pengajuans = $query->latest()->paginate($per_page)->withQueryString();
        $admValue = $this->getAdmValue();

        $usedSuratJalanIds = PengajuanStnkDetail::pluck('surat_jalan_id')->toArray();
        $availableSuratJalans = SuratJalan::with(['spk.motorType', 'motorUnit'])
            ->whereNotIn('id', $usedSuratJalanIds)
            ->get();

        return view('transaction.pengajuan-stnk.riwayat', compact('pengajuans', 'search', 'per_page', 'admValue', 'availableSuratJalans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_bukti' => 'required|unique:pengajuan_stnks,no_bukti',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:surat_jalans,id',
        ]);

        $admValue = $this->getAdmValue();

        DB::transaction(function () use ($request, $admValue) {
            $pengajuan = PengajuanStnk::create([
                'no_bukti' => $request->no_bukti,
                'tanggal' => $request->tanggal,
                'total_pajak' => $request->total_pajak,
                'total_adm' => $request->total_adm,
                'total_tambahan' => $request->total_tambahan,
                'grand_total' => $request->grand_total,
            ]);

            foreach ($request->items as $item) {
                $suratJalan = SuratJalan::with('spk.motorType')->find($item['id']);
                $noticePajak = $suratJalan->spk->motorType->notice_pajak ?? 0;

                PengajuanStnkDetail::create([
                    'pengajuan_stnk_id' => $pengajuan->id,
                    'surat_jalan_id' => $item['id'],
                    'notice_pajak' => (int) $noticePajak,
                    'adm' => $admValue,
                    'sub_total' => (int) $noticePajak + $admValue,
                ]);
            }

            if ($request->has('tambahans')) {
                foreach ($request->tambahans as $t) {
                    if (!empty($t['keterangan']) && $t['nominal'] > 0) {
                        PengajuanStnkTambahan::create([
                            'pengajuan_stnk_id' => $pengajuan->id,
                            'keterangan' => $t['keterangan'],
                            'nominal' => (int) $t['nominal'],
                            'total' => (int) $t['nominal'] * count($request->items),
                        ]);
                    }
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Data Pengajuan STNK berhasil disimpan!']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:surat_jalans,id',
        ]);

        $admValue = $this->getAdmValue();

        DB::transaction(function () use ($request, $id, $admValue) {
            $pengajuan = PengajuanStnk::findOrFail($id);
            $pengajuan->update([
                'tanggal' => $request->tanggal,
                'total_pajak' => $request->total_pajak,
                'total_adm' => $request->total_adm,
                'total_tambahan' => $request->total_tambahan,
                'grand_total' => $request->grand_total,
            ]);

            $pengajuan->details()->delete();
            foreach ($request->items as $item) {
                $suratJalan = SuratJalan::with('spk.motorType')->find($item['id']);
                $noticePajak = $suratJalan->spk->motorType->notice_pajak ?? 0;

                PengajuanStnkDetail::create([
                    'pengajuan_stnk_id' => $pengajuan->id,
                    'surat_jalan_id' => $item['id'],
                    'notice_pajak' => (int) $noticePajak,
                    'adm' => $admValue,
                    'sub_total' => (int) $noticePajak + $admValue,
                ]);
            }

            $pengajuan->tambahans()->delete();
            if ($request->has('tambahans')) {
                foreach ($request->tambahans as $t) {
                    if (!empty($t['keterangan']) && $t['nominal'] > 0) {
                        PengajuanStnkTambahan::create([
                            'pengajuan_stnk_id' => $pengajuan->id,
                            'keterangan' => $t['keterangan'],
                            'nominal' => (int) $t['nominal'],
                            'total' => (int) $t['nominal'] * count($request->items),
                        ]);
                    }
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Data Pengajuan STNK berhasil diperbarui!']);
    }

    public function destroy($id)
    {
        PengajuanStnk::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Pengajuan dibatalkan.']);
    }

    public function print($id)
    {
        $pengajuan = PengajuanStnk::with(['details.suratJalan.spk.motorType', 'details.suratJalan.motorUnit', 'tambahans'])->findOrFail($id);
        return view('transaction.pengajuan-stnk.print', compact('pengajuan'));
    }
}
