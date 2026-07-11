<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanPiutangRegulerController extends Controller
{
    public function index(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $sampai_tanggal = $request->input('sampai_tanggal', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $jenis_laporan = $request->input('jenis_laporan', 'piutang_konsumen');

        $data = $this->getRawDataLaporan($dari_tanggal, $sampai_tanggal);

        return view('laporan.piutang-reguler.index', compact('data', 'dari_tanggal', 'sampai_tanggal', 'jenis_laporan'));
    }

    public function print(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');
        $jenis_laporan = $request->input('jenis_laporan', 'piutang_konsumen');

        $data = $this->getRawDataLaporan($dari_tanggal, $sampai_tanggal);
        $isAdminGp = Auth::user()->hasRole('Admin GP');

        return view('laporan.piutang-reguler.print', compact('data', 'dari_tanggal', 'sampai_tanggal', 'jenis_laporan', 'isAdminGp'));
    }

    private function getRawDataLaporan($dari_tanggal, $sampai_tanggal)
    {
        $isAdminGp = Auth::user()->hasRole('Admin GP');

        // Mengambil data SPK yang wajib memiliki SJK (Surat Jalan Konsumen) sesuai kesepakatan
        $query = Spk::with(['sales', 'motorUnit.type', 'leasing', 'kontrolHarga', 'suratJalan', 'kuitansiKonsumens'])
            ->whereHas('suratJalan')
            ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal]);

        // Proteksi Data Sisi Server berdasarkan Role Pengguna
        if ($isAdminGp) {
            $query->where('no_spk', 'like', 'GPK%');
        } else {
            $query->where('no_spk', 'like', 'SPK%');
        }

        $spks = $query->orderBy('tanggal', 'asc')->get();
        $processedData = [];

        foreach ($spks as $spk) {
            $discount = $spk->kontrolHarga->discount ?? 0;
            $isKredit = (strtolower($spk->jenis_pembayaran) === 'kredit' || !empty($spk->leasing_id));
            
            // 1. Perhitungan Uang Muka Netto / Harga OTR Netto
            $uang_muka_netto = $isKredit ? (int)$spk->uang_muka : ((int)$spk->harga_otr - $discount);

            // 2. Perhitungan Nilai Piutang (Sisa Tagihan)
            $targetTagihan = $isKredit ? ((int)$spk->uang_muka - $discount) : ((int)$spk->harga_otr - $discount);
            $totalTerbayar = $spk->kuitansiKonsumens->sum(function ($k) {
                return (int)$k->bayar_kontan + (int)$k->bayar_transfer;
            });
            $nilai_piutang = $targetTagihan - $totalTerbayar;

            // Kita hanya tampilkan konsumen yang masih memiliki piutang (sisa tagihan > 0)
            if ($nilai_piutang <= 0) {
                continue;
            }

            $latestKuitansi = $spk->kuitansiKonsumens->sortByDesc('tanggal')->first();
            $tanggalAcuan = $latestKuitansi ? Carbon::parse($latestKuitansi->tanggal) : Carbon::parse($spk->suratJalan->tanggal);
            
            // Format waktu di-set ke awal hari (00:00:00) agar hitungan harinya presisi
            $tanggalAcuan = $tanggalAcuan->startOfDay();
            $hariIni = Carbon::now()->startOfDay();

            $tenggat_hari = 0;
            // Jika hari ini sudah melewati tanggal acuan pembayaran/SJK
            if ($hariIni->greaterThan($tanggalAcuan)) {
                // Otomatis menghitung selisih hari (Misal: Acuan tgl 10, Hari ini tgl 11 = 1 Hari)
                $tenggat_hari = $tanggalAcuan->diffInDays($hariIni);
            }

            $processedData[] = (object) [
                'nama_konsumen' => $spk->nama_pemohon,
                'alamat_lengkap'=> $spk->alamat . ' ' . $spk->rt_rw,
                'no_spk'        => $spk->no_spk,
                'tgl_spk'       => $spk->tanggal,
                'no_sjk'        => $spk->suratJalan->no_bukti,
                'tgl_sjk'       => $spk->suratJalan->tanggal,
                'tipe_motor'    => $spk->motorUnit->type->nama_type ?? '-',
                'sales'         => $spk->sales->nama_sales ?? '-',
                'leasing'       => $isKredit ? ($spk->leasing->nama_leasing ?? 'KREDIT') : 'KONTAN',
                'uang_muka_netto'=> $uang_muka_netto,
                'nilai_piutang' => $nilai_piutang,
                'tenggat'       => $tenggat_hari
            ];
        }

        return $processedData;
    }
}
