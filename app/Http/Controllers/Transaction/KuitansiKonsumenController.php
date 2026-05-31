<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\KontrolHargaPenjualan;
use App\Models\KuitansiKonsumen;
use App\Models\Rekening;
use App\Models\Spk;
use App\Models\SuratJalan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KuitansiKonsumenController extends Controller
{
    public function index()
    {
        $rekenings = Rekening::all();
        return view('transaction.kuitansi-konsumen.index', compact('rekenings'));
    }

    public function searchApi(Request $request)
    {
        $keyword = $request->q;

        $spks = Spk::with(['sales', 'motorUnit.type', 'motorUnit.color', 'leasing', 'kuitansiKonsumens', 'kontrolHarga'])
            ->where('no_spk', 'like', "%{$keyword}%")
            ->orWhere('nama_pemohon', 'like', "%{$keyword}%")
            ->orWhere('nama_stnk', 'like', "%{$keyword}%")
            ->limit(10)
            ->get();

        $results = $spks->map(function($spk) {
            $suratJalan = SuratJalan::where('spk_id', $spk->id)->first();
            $discount = $spk->kontrolHarga->discount ?? 0;

            $isKredit = (strtolower($spk->jenis_pembayaran) === 'kredit' || !empty($spk->leasing_id));
            $targetTagihan = $isKredit ? ($spk->uang_muka - $discount) : ($spk->harga_otr - $discount);

            $terbayarSebelumnya = $spk->kuitansiKonsumens->sum(function($k) {
                return $k->bayar_kontan + $k->bayar_transfer;
            });

            return [
                'id' => $spk->id,
                'no_spk' => $spk->no_spk,
                'nama_pemohon' => $spk->nama_pemohon,
                'nama_stnk' => $spk->nama_stnk,
                'alamat' => $spk->alamat,
                'telepon' => $spk->telepon,
                'sales' => $spk->sales->nama_sales ?? '-',
                'motor' => ($spk->motorUnit->type->kode_motor ?? '-') . ' / ' . ($spk->motorUnit->type->nama_type ?? '-'),
                'tahun' => $spk->motorUnit->tahun_pembuatan ?? '-',
                'harga_otr' => $spk->harga_otr,
                'uang_muka' => $spk->uang_muka,
                'tanda_jadi' => $spk->tanda_jadi,
                'leasing' => $spk->leasing->nama_leasing ?? '-',
                'tenor' => $spk->tenor_bulan,
                'discount' => $discount,
                'sjk' => $suratJalan->no_bukti ?? 'Belum Ada',

                // Kalkulasi Realtime
                'target_tagihan' => $targetTagihan,
                'terbayar_sebelumnya' => $terbayarSebelumnya,
                'sisa' => max(0, $targetTagihan - $terbayarSebelumnya),
                'is_lunas' => $terbayarSebelumnya >= $targetTagihan,

                // Histori untuk ditampilkan di tabel bawah
                'history' => $spk->kuitansiKonsumens->map(function($k) {
                    return [
                        'id' => $k->id,
                        'no_kuitansi' => $k->no_kuitansi,
                        'tanggal' => Carbon::parse($k->tanggal)->format('d/m/Y'),
                        'total' => $k->bayar_kontan + $k->bayar_transfer,
                        'url_print' => route('kuitansi-konsumen.print', $k->id)
                    ];
                })
            ];
        });

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $request->validate([
            'spk_id' => 'required|exists:spks,id',
            'tanggal' => 'required|date',
            'bayar_kontan' => 'nullable|numeric|min:0',
            'bayar_transfer' => 'nullable|numeric|min:0',
        ]);

        $bayarKontan = (int) $request->bayar_kontan;
        $bayarTransfer = (int) $request->bayar_transfer;
        $inputTotal = $bayarKontan + $bayarTransfer;

        if ($inputTotal <= 0) {
            return back()->with('error', 'Total pembayaran (Kontan + Transfer) tidak boleh 0.');
        }

        try {
            DB::beginTransaction();

            $spk = Spk::with('kontrolHarga')->findOrFail($request->spk_id);
            $discount = $spk->kontrolHarga->discount ?? 0;

            $isKredit = (strtolower($spk->jenis_pembayaran) === 'kredit' || !empty($spk->leasing_id));
            $targetTagihan = $isKredit ? ($spk->uang_muka - $discount) : ($spk->harga_otr - $discount);

            $terbayarSebelumnya = KuitansiKonsumen::where('spk_id', $spk->id)
                ->get()->sum(function($k) { return $k->bayar_kontan + $k->bayar_transfer; });

            $sisaTagihan = $targetTagihan - $terbayarSebelumnya;
            $kelebihan = $inputTotal - $sisaTagihan;

            // Jika ada kelebihan bayar, dan transaksinya pakai transfer, simpan ke Refund Transfer
            if ($kelebihan > 0 && $bayarTransfer > 0) {
                $tambahanRefund = min($kelebihan, $bayarTransfer);

                $kontrol = KontrolHargaPenjualan::firstOrCreate(['spk_id' => $spk->id]);
                $kontrol->refund_transfer = ($kontrol->refund_transfer ?? 0) + $tambahanRefund;
                $kontrol->save();
            }

            // Generate No. TTK
            $now = Carbon::parse($request->tanggal);
            $prefix = 'TTK' . $now->format('Y/m/');

            $lastDoc = KuitansiKonsumen::where('no_kuitansi', 'like', $prefix . '%')
                ->lockForUpdate()->orderBy('id', 'desc')->first();
            $urut = $lastDoc ? ((int) substr($lastDoc->no_kuitansi, -4)) + 1 : 1;
            $no_kuitansi = $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);

            $kuitansi = KuitansiKonsumen::create([
                'spk_id' => $spk->id,
                'no_kuitansi' => $no_kuitansi,
                'tanggal' => $request->tanggal,
                'bayar_kontan' => $bayarKontan,
                'bayar_transfer' => $bayarTransfer,
                'rekening_id' => $bayarTransfer > 0 ? $request->rekening_id : null,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();
            return back()->with('success', "Kuitansi berhasil disimpan dengan No: $no_kuitansi")->with('print_id', $kuitansi->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $kuitansi = KuitansiKonsumen::with(['spk.motorUnit.type', 'spk.motorUnit.color', 'spk.kontrolHarga'])->findOrFail($id);

        $spk = $kuitansi->spk;
        $discount = $spk->kontrolHarga->discount ?? 0;
        $isKredit = (strtolower($spk->jenis_pembayaran) === 'kredit' || !empty($spk->leasing_id));
        $targetTagihan = $isKredit ? ($spk->uang_muka - $discount) : ($spk->harga_otr - $discount);

        $terbayarHinggaKuitansiIni = KuitansiKonsumen::where('spk_id', $spk->id)
            ->where('id', '<=', $kuitansi->id)
            ->get()->sum(function($k) { return $k->bayar_kontan + $k->bayar_transfer; });

        $sisa = $targetTagihan - $terbayarHinggaKuitansiIni;

        $suratJalan = SuratJalan::where('spk_id', $spk->id)->first();

        return view('transaction.kuitansi-konsumen.print', compact('kuitansi', 'spk', 'targetTagihan', 'sisa', 'suratJalan'));
    }
}
