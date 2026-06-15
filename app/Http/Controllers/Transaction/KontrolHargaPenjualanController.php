<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\KontrolHargaPenjualan;
use App\Models\KuitansiKonsumen;
use App\Models\Spk;
use App\Models\SuratJalan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KontrolHargaPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-d'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        $isAdminGp = auth()->user()->hasRole('Admin GP');

        $query = Spk::with(['motorUnit.type'])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        // Isolasi Data: Admin GP hanya melihat GPK, Selain GP hanya melihat SPK
        if ($isAdminGp) {
            $query->where('no_spk', 'like', 'GPK%');
        } else {
            $query->where('no_spk', 'like', 'SPK%');
        }

        $spks = $query->get();

        $kontrolHargas = KontrolHargaPenjualan::whereIn('spk_id', $spks->pluck('id'))
            ->get()
            ->keyBy('spk_id');

        return view('transaction.kontrol-harga.index', compact('spks', 'kontrolHargas', 'startDate', 'endDate'));
    }

    public function store(Request $request)
    {
        $dataKontrol = $request->input('kontrol', []);

        try {
            DB::beginTransaction();

            foreach ($dataKontrol as $spk_id => $data) {
                KontrolHargaPenjualan::updateOrCreate(
                    ['spk_id' => $spk_id],
                    [
                        'discount' => (int) round($data['discount'] ?? 0),
                        'subsidi_ahm' => (int) round($data['subsidi_ahm'] ?? 0),
                        'subsidi_dealer' => (int) round($data['subsidi_dealer'] ?? 0),
                        'subsidi_main_dealer' => (int) round($data['subsidi_main_dealer'] ?? 0),
                        'subsidi_leasing_1' => (int) round($data['subsidi_leasing_1'] ?? 0),
                        'subsidi_leasing_2' => (int) round($data['subsidi_leasing_2'] ?? 0),
                        'dll_1' => (int) round($data['dll_1'] ?? 0),
                        'dll_2' => (int) round($data['dll_2'] ?? 0),
                        'ekstra' => (int) round($data['ekstra'] ?? 0),
                        'nama_mediator' => $data['nama_mediator'] ?? null,
                        'mediator_fee' => (int) round($data['mediator_fee'] ?? 0),
                        'tambahan' => (int) round($data['tambahan'] ?? 0),
                        'refund_transfer' => (int) round($data['refund_transfer'] ?? 0),
                    ]
                );
            }

            DB::commit();
            return back()->with('success', 'Data kontrol harga berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    // Helper method untuk memvalidasi akses user terhadap dokumen
    private function validateAccess($spk)
    {
        $isAdminGp = auth()->user()->hasRole('Admin GP');

        if ($isAdminGp && !str_starts_with($spk->no_spk, 'GPK')) {
            abort(403, 'Anda tidak memiliki hak akses untuk dokumen ini.');
        }
        if (!$isAdminGp && !str_starts_with($spk->no_spk, 'SPK')) {
            abort(403, 'Anda tidak memiliki hak akses untuk dokumen ini.');
        }
    }

    public function printOptions($spk_id)
    {
        $spk = Spk::findOrFail($spk_id);
        $this->validateAccess($spk);

        $kontrol = KontrolHargaPenjualan::where('spk_id', $spk_id)->first();
        return view('transaction.kontrol-harga.print-options', compact('spk', 'kontrol'));
    }

    public function printOtr($spk_id)
    {
        $spk = Spk::with(['motorUnit.type', 'motorUnit.color'])->findOrFail($spk_id);
        $this->validateAccess($spk);

        $suratJalan = SuratJalan::where('spk_id', $spk_id)->first();

        if (!$suratJalan) {
            return redirect()->route('kontrol-harga.print-options', $spk_id)
                             ->with('error', 'Kuitansi OTR tidak dapat dicetak karena Surat Jalan belum dibuat untuk dokumen ini.');
        }

        $kontrol = KontrolHargaPenjualan::firstOrCreate(['spk_id' => $spk_id]);

        if (empty($kontrol->no_kwitansi_otr)) {
            DB::transaction(function () use ($kontrol) {
                $now = Carbon::now();
                $prefix = 'KWO' . $now->format('Y/m/');

                $lastDoc = KontrolHargaPenjualan::where('no_kwitansi_otr', 'like', $prefix . '%')
                    ->lockForUpdate()
                    ->orderBy('id', 'desc')
                    ->first();

                $urut = 1;
                if ($lastDoc) {
                    $lastUrut = (int) substr($lastDoc->no_kwitansi_otr, -4);
                    $urut = $lastUrut + 1;
                }

                $kontrol->no_kwitansi_otr = $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
                $kontrol->tgl_kwitansi_otr = $now->format('Y-m-d');
                $kontrol->save();
            });
        }

        return view('transaction.kontrol-harga.print.otr', compact('spk', 'kontrol', 'suratJalan'));
    }

    public function printDpPo($spk_id)
    {
        $spk = Spk::with(['motorUnit.type', 'motorUnit.color'])->findOrFail($spk_id);
        $this->validateAccess($spk);

        $suratJalan = SuratJalan::where('spk_id', $spk_id)->first();

        if (!$suratJalan) {
            return redirect()->route('kontrol-harga.print-options', $spk_id)
                             ->with('error', 'Kuitansi DP PO tidak dapat dicetak karena Surat Jalan belum dibuat untuk dokumen ini.');
        }

        $kontrol = KontrolHargaPenjualan::firstOrCreate(['spk_id' => $spk_id]);

        if (empty($kontrol->no_kwitansi_dp)) {
            DB::transaction(function () use ($kontrol) {
                $now = Carbon::now();
                $prefix = 'KWD' . $now->format('Y/m/');

                $lastDoc = KontrolHargaPenjualan::where('no_kwitansi_dp', 'like', $prefix . '%')
                    ->lockForUpdate()
                    ->orderBy('id', 'desc')
                    ->first();

                $urut = 1;
                if ($lastDoc) {
                    $lastUrut = (int) substr($lastDoc->no_kwitansi_dp, -4);
                    $urut = $lastUrut + 1;
                }

                $kontrol->no_kwitansi_dp = $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
                $kontrol->tgl_kwitansi_dp = $now->format('Y-m-d');
                $kontrol->save();
            });
        }

        return view('transaction.kontrol-harga.print.dp-po', compact('spk', 'kontrol', 'suratJalan'));
    }

    public function printOtrDpPo($spk_id)
    {
        $spk = Spk::with(['motorUnit.type', 'motorUnit.color', 'leasing'])->findOrFail($spk_id);
        $this->validateAccess($spk);

        $suratJalan = SuratJalan::where('spk_id', $spk_id)->first();

        if (!$suratJalan) {
            return redirect()->route('kontrol-harga.print-options', $spk_id)
                             ->with('error', 'Kuitansi Penagihan Leasing (KWM) tidak dapat dicetak karena Surat Jalan belum dibuat.');
        }

        $kontrol = KontrolHargaPenjualan::firstOrCreate(['spk_id' => $spk_id]);

        if (empty($kontrol->no_kwitansi_kwm)) {
            DB::transaction(function () use ($kontrol) {
                $now = Carbon::now();
                $prefix = 'KWM' . $now->format('Y/m/');

                $lastDoc = KontrolHargaPenjualan::where('no_kwitansi_kwm', 'like', $prefix . '%')
                    ->lockForUpdate()
                    ->orderBy('id', 'desc')
                    ->first();

                $urut = 1;
                if ($lastDoc) {
                    $lastUrut = (int) substr($lastDoc->no_kwitansi_kwm, -4);
                    $urut = $lastUrut + 1;
                }

                $kontrol->no_kwitansi_kwm = $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
                $kontrol->tgl_kwitansi_kwm = $now->format('Y-m-d');
                $kontrol->save();
            });
        }

        return view('transaction.kontrol-harga.print.otr-dp-po', compact('spk', 'kontrol', 'suratJalan'));
    }

    public function printKw1($spk_id)
    {
        $spk = Spk::with(['motorUnit.type', 'motorUnit.color', 'leasing'])->findOrFail($spk_id);
        $this->validateAccess($spk);

        $suratJalan = SuratJalan::where('spk_id', $spk_id)->first();

        if (!$suratJalan) {
            return redirect()->route('kontrol-harga.print-options', $spk_id)
                             ->with('error', 'Kuitansi Subsidi (KW1) tidak dapat dicetak karena Surat Jalan belum dibuat.');
        }

        $kontrol = KontrolHargaPenjualan::firstOrCreate(['spk_id' => $spk_id]);

        if (empty($kontrol->no_kwitansi_kw1)) {
            DB::transaction(function () use ($kontrol) {
                $now = Carbon::now();
                $prefix = 'KW1' . $now->format('Y/m/');

                $lastDoc = KontrolHargaPenjualan::where('no_kwitansi_kw1', 'like', $prefix . '%')
                    ->lockForUpdate()->orderBy('id', 'desc')->first();

                $urut = $lastDoc ? ((int) substr($lastDoc->no_kwitansi_kw1, -4)) + 1 : 1;

                $kontrol->no_kwitansi_kw1 = $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
                $kontrol->tgl_kwitansi_kw1 = $now->format('Y-m-d');
                $kontrol->save();
            });
        }

        return view('transaction.kontrol-harga.print.kw1', compact('spk', 'kontrol', 'suratJalan'));
    }

    public function printKw2($spk_id)
    {
        $spk = Spk::with(['motorUnit.type', 'motorUnit.color', 'leasing'])->findOrFail($spk_id);
        $this->validateAccess($spk);

        $suratJalan = SuratJalan::where('spk_id', $spk_id)->first();

        if (!$suratJalan) {
            return redirect()->route('kontrol-harga.print-options', $spk_id)
                             ->with('error', 'Kuitansi Subsidi (KW2) tidak dapat dicetak karena Surat Jalan belum dibuat.');
        }

        $kontrol = KontrolHargaPenjualan::firstOrCreate(['spk_id' => $spk_id]);

        if (empty($kontrol->no_kwitansi_kw2)) {
            DB::transaction(function () use ($kontrol) {
                $now = Carbon::now();
                $prefix = 'KW2' . $now->format('Y/m/');

                $lastDoc = KontrolHargaPenjualan::where('no_kwitansi_kw2', 'like', $prefix . '%')
                    ->lockForUpdate()->orderBy('id', 'desc')->first();

                $urut = $lastDoc ? ((int) substr($lastDoc->no_kwitansi_kw2, -4)) + 1 : 1;

                $kontrol->no_kwitansi_kw2 = $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
                $kontrol->tgl_kwitansi_kw2 = $now->format('Y-m-d');
                $kontrol->save();
            });
        }

        return view('transaction.kontrol-harga.print.kw2', compact('spk', 'kontrol', 'suratJalan'));
    }

    public function printSetoranSpk($spk_id)
    {
        $spk = Spk::with(['motorUnit.type', 'leasing'])->findOrFail($spk_id);
        $this->validateAccess($spk);

        $kontrol = KontrolHargaPenjualan::where('spk_id', $spk_id)->first();

        if (!$kontrol) {
            return redirect()->route('kontrol-harga.print-options', $spk_id)
                             ->with('error', 'Silakan simpan data Kontrol Harga terlebih dahulu sebelum mencetak Setoran.');
        }

        $kuitansis = KuitansiKonsumen::with('rekening')->where('spk_id', $spk_id)->get();

        $setor = $kuitansis->sum('bayar_kontan');
        $nilaiTransfer = $kuitansis->sum('bayar_transfer');
        $bayar = $setor + $nilaiTransfer;

        $rekeningList = $kuitansis->where('bayar_transfer', '>', 0)
                                  ->map(function($k) {
                                      return $k->rekening ? $k->rekening->nama_rekening . ' ' . $k->rekening->nomor_rekening : null;
                                  })
                                  ->filter()
                                  ->unique()
                                  ->implode(', ');

        return view('transaction.kontrol-harga.print.setoran-spk', compact(
            'spk', 'kontrol', 'setor', 'nilaiTransfer', 'bayar', 'rekeningList'
        ));
    }

    public function printSuratPernyataanBpkb($spk_id)
    {
        $spk = Spk::with(['motorUnit.type', 'motorUnit.color', 'leasing'])->findOrFail($spk_id);
        $this->validateAccess($spk);

        return view('transaction.kontrol-harga.print.surat-pernyataan-bpkb', compact('spk'));
    }
}
