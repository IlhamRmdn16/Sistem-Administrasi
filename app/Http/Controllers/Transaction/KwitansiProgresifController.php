<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\KwitansiPajakProgresif;
use App\Models\Rekening;
use App\Models\SuratJalan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KwitansiProgresifController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'buat');

        $belumLunas = SuratJalan::whereHas('samsat', function($q) {
                $q->where('pajak_progresif', '>', 0);
            })
            ->doesntHave('kwitansiProgresif')
            ->with(['spk.motorType', 'spk.motorColor', 'spk.leasing', 'spk.sales', 'motorUnit', 'samsat'])
            ->get();

        $rekenings = Rekening::all();

        $query = KwitansiPajakProgresif::with(['suratJalan.spk.motorType', 'suratJalan.samsat']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_kwitansi', 'like', "%{$search}%")
                  ->orWhereHas('suratJalan.spk', function($qSpk) use ($search) {
                      $qSpk->where('nama_stnk', 'like', "%{$search}%")
                           ->orWhere('nama_pemohon', 'like', "%{$search}%");
                  })
                  ->orWhereHas('suratJalan.spk.motorType', function($qType) use ($search) {
                      $qType->where('nama_type', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        $perPage = $request->input('per_page', 10);
        $riwayat = $query->latest()->paginate($perPage)->withQueryString();

        return view('transaction.kwitansi-progresif.index', compact('belumLunas', 'riwayat', 'rekenings', 'tab'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'surat_jalan_id' => 'required|exists:surat_jalans,id',
            'tanggal'        => 'required|date',
            'bayar_kontan'   => 'numeric|min:0',
            'bayar_transfer' => 'numeric|min:0',
        ]);

        if ($request->bayar_transfer > 0 && empty($request->rekening_tujuan)) {
            return back()->with('error', 'Rekening tujuan wajib dipilih karena ada nominal pembayaran Transfer!');
        }

        try {
            DB::beginTransaction();

            $sjk = SuratJalan::with('samsat')->findOrFail($request->surat_jalan_id);
            $tagihan = $sjk->samsat->pajak_progresif;
            $totalBayar = $request->bayar_kontan + $request->bayar_transfer;

            if ($totalBayar != $tagihan) {
                return back()->with('error', 'Total pembayaran (Kontan + Transfer) tidak sesuai dengan tagihan Pajak!');
            }

            $now = Carbon::parse($request->tanggal);
            $prefix = 'KNP' . $now->format('Y/m/');

            $lastKwitansi = KwitansiPajakProgresif::where('no_kwitansi', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $urut = 1;
            if ($lastKwitansi) {
                $lastUrut = (int) substr($lastKwitansi->no_kwitansi, -4);
                $urut = $lastUrut + 1;
            }
            $noKwitansiBaru = $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);

            $kwitansi = KwitansiPajakProgresif::create([
                'surat_jalan_id' => $request->surat_jalan_id,
                'no_kwitansi'    => $noKwitansiBaru,
                'tanggal'        => $request->tanggal,
                'bayar_kontan'   => $request->bayar_kontan,
                'bayar_transfer' => $request->bayar_transfer,
                'rekening_tujuan'=> $request->bayar_transfer > 0 ? $request->rekening_tujuan : null,
                'no_po_leasing'  => $request->no_po_leasing,
            ]);

            DB::commit();

            // Redirect langsung ke print (menghapus show/preview)
            return redirect()->route('kwitansi-progresif.print', $kwitansi->id);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $kwitansi = KwitansiPajakProgresif::with(['suratJalan.spk.motorType', 'suratJalan.spk.motorColor', 'suratJalan.spk.leasing', 'suratJalan.samsat', 'suratJalan.motorUnit', 'suratJalan.spk.sales'])
                    ->findOrFail($id);

        return view('transaction.kwitansi-progresif.print', compact('kwitansi'));
    }
}
