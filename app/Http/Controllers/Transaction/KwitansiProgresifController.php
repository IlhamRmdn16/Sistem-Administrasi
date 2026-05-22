<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\KwitansiPajakProgresif;
use App\Models\SuratJalan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KwitansiProgresifController extends Controller
{
    public function index()
    {
        // TAB 1: Cari konsumen yang PAJAK > 0 dan BELUM LUNAS (Belum punya kwitansi)
        $belumLunas = SuratJalan::whereHas('samsat', function($q) {
                $q->where('pajak_progresif', '>', 0);
            })
            ->doesntHave('kwitansiProgresif')
            ->with(['spk.motorType', 'spk.motorColor', 'spk.leasing', 'spk.sales', 'motorUnit', 'samsat'])
            ->get();

        // TAB 2: Riwayat Kwitansi
        $riwayat = KwitansiPajakProgresif::with(['suratJalan.spk.motorType', 'suratJalan.samsat'])
            ->latest()
            ->paginate(15);

        // Dummy Daftar Rekening (Ganti dengan Model Rekening jika Anda punya tabelnya)
        $rekenings = [
            'BCA - 1234567890 a/n CV Surya Wijaya',
            'MANDIRI - 0987654321 a/n CV Surya Wijaya'
        ];

        return view('transaction.kwitansi-progresif.index', compact('belumLunas', 'riwayat', 'rekenings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'surat_jalan_id' => 'required|exists:surat_jalans,id',
            'tanggal'        => 'required|date',
            'bayar_kontan'   => 'numeric|min:0',
            'bayar_transfer' => 'numeric|min:0',
            'rekening_tujuan'=> 'nullable|required_if:bayar_transfer,>,0',
        ]);

        try {
            DB::beginTransaction();

            $sjk = SuratJalan::with('samsat')->findOrFail($request->surat_jalan_id);
            $tagihan = $sjk->samsat->pajak_progresif;
            $totalBayar = $request->bayar_kontan + $request->bayar_transfer;

            // Validasi di sisi server (mencegah manipulasi inspect element)
            if ($totalBayar != $tagihan) {
                return back()->with('error', 'Total pembayaran (Kontan + Transfer) tidak sesuai dengan tagihan Pajak!');
            }

            // AUTO GENERATE NO KWITANSI (Anti Tabrakan)
            $now = Carbon::parse($request->tanggal);
            $prefix = 'KNP' . $now->format('Y/m/');
            
            $lastKwitansi = KwitansiPajakProgresif::where('no_kwitansi', 'like', $prefix . '%')
                ->lockForUpdate() // Kunci tabel sesaat untuk generate nomor
                ->orderBy('id', 'desc')
                ->first();

            $urut = 1;
            if ($lastKwitansi) {
                $lastUrut = (int) substr($lastKwitansi->no_kwitansi, -4);
                $urut = $lastUrut + 1;
            }
            $noKwitansiBaru = $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);

            // Simpan Data
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

            // Redirect langsung ke halaman cetak
            return redirect()->route('kwitansi-progresif.print', $kwitansi->id);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $kwitansi = KwitansiPajakProgresif::with(['suratJalan.spk.motorType', 'suratJalan.spk.motorColor', 'suratJalan.spk.leasing', 'suratJalan.samsat', 'suratJalan.motorUnit'])
                    ->findOrFail($id);
                    
        return view('transaction.kwitansi-progresif.print', compact('kwitansi'));
    }
}
