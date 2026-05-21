<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\PenyerahanStnkBpkb;
use App\Models\SuratJalan;
use Illuminate\Http\Request;

class PenyerahanStnkBpkbController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);
        $status = $request->input('status');

        // GATEKEEPER: Hanya muncul jika No. STNK sudah diinput oleh admin
        $query = SuratJalan::whereHas('samsat', function($q) {
                $q->whereNotNull('no_stnk')->where('no_stnk', '!=', '');
            })
            ->with(['samsat', 'spk.motorType', 'motorUnit', 'penyerahanStnkBpkb']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_bukti', 'like', "%{$search}%")
                  ->orWhereHas('spk', function($qSpk) use ($search) {
                      $qSpk->where('nama_stnk', 'like', "%{$search}%")
                           ->orWhere('no_spk', 'like', "%{$search}%");
                  });
            });
        }

        if ($status == 'belum') {
            $query->doesntHave('penyerahanStnkBpkb');
        } elseif ($status == 'stnk') {
            $query->whereHas('penyerahanStnkBpkb', function($q) {
                $q->whereNotNull('tgl_serah_stnk')->whereNull('tgl_serah_bpkb');
            });
        } elseif ($status == 'lengkap') {
            $query->whereHas('penyerahanStnkBpkb', function($q) {
                $q->whereNotNull('tgl_serah_stnk')->whereNotNull('tgl_serah_bpkb');
            });
        }

        $dokumens = $query->latest()->paginate($per_page)->withQueryString();

        return view('transaction.penyerahan-stnk-bpkb.index', compact('dokumens', 'search', 'per_page', 'status'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_serah_stnk' => 'nullable|date',
            'tgl_serah_bpkb' => 'nullable|date',
            'foto_stnk' => 'nullable|image|max:2048',
            'foto_bpkb' => 'nullable|image|max:2048',
        ]);

        $penyerahan = PenyerahanStnkBpkb::firstOrNew(['surat_jalan_id' => $id]);

        // Menyimpan data STNK
        $penyerahan->tgl_serah_stnk = $request->tgl_serah_stnk;
        $penyerahan->penerima_stnk = $request->penerima_stnk;
        $penyerahan->hubungan_stnk = $request->hubungan_stnk;
        $penyerahan->alamat_penerima_stnk = $request->alamat_penerima_stnk;
        $penyerahan->keterangan_stnk = $request->keterangan_stnk;

        if ($request->hasFile('foto_stnk')) {
            $penyerahan->foto_serah_stnk = $request->file('foto_stnk')->store('penyerahan', 'public');
        }

        // Menyimpan data BPKB
        $penyerahan->tgl_serah_bpkb = $request->tgl_serah_bpkb;
        $penyerahan->penerima_bpkb = $request->penerima_bpkb;
        $penyerahan->hubungan_bpkb = $request->hubungan_bpkb;
        $penyerahan->alamat_penerima_bpkb = $request->alamat_penerima_bpkb;
        $penyerahan->keterangan_bpkb = $request->keterangan_bpkb;

        if ($request->hasFile('foto_bpkb')) {
            $penyerahan->foto_serah_bpkb = $request->file('foto_bpkb')->store('penyerahan', 'public');
        }

        $penyerahan->save();

        return response()->json(['success' => true, 'message' => 'Data Penyerahan berhasil disimpan!']);
    }

    public function print($id)
    {
        $sjk = SuratJalan::with(['spk.motorType', 'motorUnit', 'samsat', 'penyerahanStnkBpkb'])->findOrFail($id);
        return view('transaction.penyerahan-stnk-bpkb.print', compact('sjk'));
    }
}
