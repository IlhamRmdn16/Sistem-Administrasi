<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Samsat;
use App\Models\SuratJalan;
use Illuminate\Http\Request;

class SamsatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);
        $status_dokumen = $request->input('status_dokumen');

        $query = SuratJalan::with(['samsat', 'spk.motorType', 'spk.motorColor', 'spk.leasing', 'motorUnit']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_bukti', 'like', "%{$search}%")
                  ->orWhereHas('spk', function($qSpk) use ($search) {
                      $qSpk->where('nama_stnk', 'like', "%{$search}%")
                           ->orWhere('no_spk', 'like', "%{$search}%");
                  });
            });
        }

        if ($status_dokumen) {
            if ($status_dokumen == 'belum') {
                $query->where(function($q) {
                    $q->doesntHave('samsat')
                      ->orWhereHas('samsat', function($qSamsat) {
                          $qSamsat->whereNull('tgl_terima_stnk')->whereNull('tgl_terima_bpkb');
                      });
                });
            } elseif ($status_dokumen == 'stnk_saja') {
                $query->whereHas('samsat', function($q) {
                    $q->whereNotNull('tgl_terima_stnk')->whereNull('tgl_terima_bpkb');
                });
            } elseif ($status_dokumen == 'bpkb_saja') {
                $query->whereHas('samsat', function($q) {
                    $q->whereNull('tgl_terima_stnk')->whereNotNull('tgl_terima_bpkb');
                });
            } elseif ($status_dokumen == 'selesai') {
                $query->whereHas('samsat', function($q) {
                    $q->whereNotNull('tgl_terima_stnk')->whereNotNull('tgl_terima_bpkb');
                });
            }
        }

        $dokumens = $query->latest()->paginate($per_page)->withQueryString();

        return view('transaction.samsat.index', compact('dokumens', 'search', 'per_page', 'status_dokumen'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'jumlah_motor' => 'required|integer|min:1',
        'pajak_progresif' => 'nullable|integer|min:0', // Validasi sebagai integer
    ]);

    Samsat::updateOrCreate(
        ['surat_jalan_id' => $id],
        [
            'no_polisi' => $request->no_polisi,
            'no_stnk' => $request->no_stnk,
            'tgl_stnk' => $request->tgl_stnk,
            'tgl_terima_stnk' => $request->tgl_terima_stnk,
            'jumlah_motor' => (int) $request->jumlah_motor,
            'pajak_progresif' => $request->filled('pajak_progresif') ? (int) $request->pajak_progresif : 0,
            'no_bpkb' => $request->no_bpkb,
            'tgl_bpkb' => $request->tgl_bpkb,
            'tgl_terima_bpkb' => $request->tgl_terima_bpkb,
        ]
    );

    return response()->json([
        'success' => true,
        'message' => 'Dokumen Kendaraan berhasil diperbarui!'
    ]);
}
}
