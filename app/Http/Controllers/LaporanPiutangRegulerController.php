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
        $format_laporan = $request->input('format_laporan', 'standar'); 
        
        $isAdminGp = Auth::user()->hasRole('Admin GP');
        $lokasi_spk = $isAdminGp ? 'gp' : $request->input('lokasi_spk', 'semua');

        $data = $this->getRawDataLaporan($dari_tanggal, $sampai_tanggal, $jenis_laporan, $lokasi_spk);

        return view('laporan.piutang-reguler.index', compact(
            'data', 'dari_tanggal', 'sampai_tanggal', 'jenis_laporan', 'format_laporan', 'isAdminGp', 'lokasi_spk'
        ));
    }

    public function print(Request $request)
    {
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');
        $jenis_laporan = $request->input('jenis_laporan', 'piutang_konsumen');
        $format_laporan = $request->input('format_laporan', 'standar');

        $isAdminGp = Auth::user()->hasRole('Admin GP');
        $lokasi_spk = $isAdminGp ? 'gp' : $request->input('lokasi_spk', 'semua');

        $data = $this->getRawDataLaporan($dari_tanggal, $sampai_tanggal, $jenis_laporan, $lokasi_spk);

        return view('laporan.piutang-reguler.print', compact(
            'data', 'dari_tanggal', 'sampai_tanggal', 'jenis_laporan', 'format_laporan', 'isAdminGp', 'lokasi_spk'
        ));
    }

    private function getRawDataLaporan($dari_tanggal, $sampai_tanggal, $jenis_laporan, $lokasi_spk)
    {
        $processedData = [];

        // BYPASS: LOGIKA KHUSUS UNTUK KWITANSI LAIN-LAIN (TIDAK TERELASI DENGAN SPK)
        if ($jenis_laporan === 'kwitansi_lain') {
            $queryLain = \App\Models\KuitansiLainLain::whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal]);
            
            /* Catatan: Jika ada pemisah antara GP dan Pusat pada nomor buktinya, 
               bisa ditambahkan filter di sini seperti: 
               if ($lokasi_spk === 'pusat') $queryLain->where('no_bukti', 'like', '...%'); 
            */
            
            $kwitansi = $queryLain->orderBy('tanggal', 'asc')->get();
            foreach ($kwitansi as $k) {
                $processedData[] = (object) [
                    'nama'       => $k->nama,
                    'keterangan' => $k->keterangan,
                    'tipe_motor' => $k->tipe_motor,
                    'nilai'      => $k->nilai
                ];
            }
            return $processedData;
        }

        // =========================================================================
        // LOGIKA UNTUK LAPORAN YANG TERELASI DENGAN SPK (Piutang, Pembayaran, dll)
        // =========================================================================
        
        $query = Spk::with(['sales', 'motorUnit.type', 'leasing', 'kontrolHarga', 'suratJalan', 'kuitansiKonsumens.rekening'])
            ->whereHas('suratJalan')
            ->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal]);

        if ($lokasi_spk === 'pusat') {
            $query->where('no_spk', 'like', 'SPK%');
        } elseif ($lokasi_spk === 'gp') {
            $query->where('no_spk', 'like', 'GPK%');
        }

        $spks = $query->orderBy('tanggal', 'asc')->get();

        foreach ($spks as $spk) {
            $discount = $spk->kontrolHarga->discount ?? 0;
            $refund = $spk->kontrolHarga->refund_transfer ?? 0;
            $isKredit = (strtolower($spk->jenis_pembayaran) === 'kredit' || !empty($spk->leasing_id));
            
            $dp_murni = $isKredit ? ((int)$spk->uang_muka - $discount) : ((int)$spk->harga_otr - $discount);
            
            $kontan = $spk->kuitansiKonsumens->sum('bayar_kontan');
            $transfer = $spk->kuitansiKonsumens->sum('bayar_transfer');
            $totalTerbayar = $kontan + $transfer;
            
            $sisa = $dp_murni - $totalTerbayar;

            // 1. LAPORAN PIUTANG KONSUMEN
            if ($jenis_laporan === 'piutang_konsumen') {
                if ($sisa <= 0) continue;

                $latestKuitansi = $spk->kuitansiKonsumens->sortByDesc('tanggal')->first();
                $tanggalAcuan = $latestKuitansi ? Carbon::parse($latestKuitansi->tanggal)->startOfDay() : Carbon::parse($spk->suratJalan->tanggal)->startOfDay();
                $hariIni = Carbon::now()->startOfDay();

                $tenggat_hari = 0;
                if ($hariIni->greaterThan($tanggalAcuan)) {
                    $tenggat_hari = $tanggalAcuan->diffInDays($hariIni);
                }

                $processedData[] = (object) [
                    'nama_konsumen' => $spk->nama_pemohon,
                    'tipe_motor'    => $spk->motorUnit->type->nama_type ?? '-',
                    'alamat_lengkap'=> $spk->alamat . ' ' . $spk->rt_rw,
                    'no_spk'        => $spk->no_spk,
                    'tgl_spk'       => $spk->tanggal,
                    'no_sjk'        => $spk->suratJalan->no_bukti,
                    'tgl_sjk'       => $spk->suratJalan->tanggal,
                    'sales'         => $spk->sales->nama_sales ?? '-',
                    'leasing'       => $isKredit ? ($spk->leasing->nama_leasing ?? 'KREDIT') : 'KONTAN',
                    'uang_muka_netto'=> $dp_murni,
                    'nilai_piutang' => $sisa,
                    'tenggat'       => $tenggat_hari
                ];
            } 
            
            // 2. LAPORAN PEMBAYARAN TRANSFER
            elseif ($jenis_laporan === 'pembayaran_transfer') {
                if ($transfer <= 0) continue; 

                $processedData[] = (object) [
                    'nama_konsumen' => $spk->nama_pemohon,
                    'tipe_motor'    => $spk->motorUnit->type->nama_type ?? '-',
                    'harga_otr'     => $spk->harga_otr,
                    'discount'      => $discount,
                    'dp_murni'      => $dp_murni,
                    'sisa'          => $sisa,
                    'kontan'        => $kontan,
                    'transfer'      => $transfer
                ];
            }

            // 3. LAPORAN REFUND TRANSFER
            elseif ($jenis_laporan === 'refund_transfer') {
                if ($refund <= 0) continue;

                $processedData[] = (object) [
                    'nama_konsumen' => $spk->nama_pemohon,
                    'tipe_motor'    => $spk->motorUnit->type->nama_type ?? '-',
                    'refund_trf'    => $refund,
                    'sales'         => $spk->sales->nama_sales ?? '-',
                ];
            }

            // 4. LAPORAN PEMBAYARAN MUTLAK (STANDAR / LENGKAP)
            elseif ($jenis_laporan === 'pembayaran') {
                $kuitansiTransfer = $spk->kuitansiKonsumens->where('bayar_transfer', '>', 0)->first();
                $nama_rekening = $kuitansiTransfer && $kuitansiTransfer->rekening ? $kuitansiTransfer->rekening->nama_rekening : '-';

                $processedData[] = (object) [
                    'nama_konsumen' => $spk->nama_pemohon,
                    'tipe_motor'    => $spk->motorUnit->type->nama_type ?? '-',
                    'harga_otr'     => $spk->harga_otr,
                    'discount'      => $discount,
                    'refund'        => $refund,
                    'dp_murni'      => $dp_murni,
                    'sisa'          => $sisa,
                    'kontan'        => $kontan,
                    'transfer'      => $transfer,
                    'rekening'      => $nama_rekening,
                    'md_fee'        => $spk->kontrolHarga->mediator_fee ?? 0,
                    'setor'         => $kontan, 
                    'tambahan'      => $spk->kontrolHarga->tambahan ?? 0,
                    
                    'subsidi_leasing_nama' => $isKredit ? ($spk->leasing->nama_leasing ?? 'KREDIT') : 'KONTAN',
                    'subsidi_ahm'     => $spk->kontrolHarga->subsidi_ahm ?? 0,
                    'subsidi_mdealer' => $spk->kontrolHarga->subsidi_main_dealer ?? 0,
                    'subsidi_leasing' => $spk->kontrolHarga->subsidi_leasing_1 ?? 0,
                    'subsidi_dll'     => ((int)($spk->kontrolHarga->dll_1 ?? 0) + (int)($spk->kontrolHarga->dll_2 ?? 0)),
                    'subsidi_dealer'  => $spk->kontrolHarga->subsidi_dealer ?? 0,
                    
                    'tgl_spk'         => $spk->tanggal,
                    'tgl_sjk'         => $spk->suratJalan->tanggal,
                    'no_kunci'        => $spk->motorUnit->no_kunci ?? '-',
                    
                    'sales'           => $spk->sales->nama_sales ?? '-',
                    'mediator'        => $spk->kontrolHarga->nama_mediator ?? '-',
                ];
            }
        }

        return $processedData;
    }
}
