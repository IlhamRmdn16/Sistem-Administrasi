<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\KontrolHargaPenjualan;
use App\Models\Spk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KontrolHargaPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-d'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        $spks = Spk::with(['motorUnit.type'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

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
                        'subsidi_leasing' => (int) round($data['subsidi_leasing'] ?? 0),
                        'dll' => (int) round($data['dll'] ?? 0),
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

    public function printOptions($spk_id)
    {
        $spk = Spk::findOrFail($spk_id);
        return view('transaction.kontrol-harga.print-options', compact('spk'));
    }

    public function printOtr($spk_id)
    {
        $spk = Spk::with(['motorUnit.type'])->findOrFail($spk_id);
        $kontrol = KontrolHargaPenjualan::where('spk_id', $spk_id)->first();
        return view('transaction.kontrol-harga.print.otr', compact('spk', 'kontrol'));
    }

    public function printDpPo($spk_id)
    {
        $spk = Spk::with(['motorUnit.type'])->findOrFail($spk_id);
        $kontrol = KontrolHargaPenjualan::where('spk_id', $spk_id)->first();
        return view('transaction.kontrol-harga.print.dp-po', compact('spk', 'kontrol'));
    }

    public function printOtrDpPo($spk_id)
    {
        $spk = Spk::with(['motorUnit.type'])->findOrFail($spk_id);
        $kontrol = KontrolHargaPenjualan::where('spk_id', $spk_id)->first();
        return view('transaction.kontrol-harga.print.otr-dp-po', compact('spk', 'kontrol'));
    }
}
