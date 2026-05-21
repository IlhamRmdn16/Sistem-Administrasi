<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\PengajuanStnk;
use Illuminate\Http\Request;

class RealisasiPajakController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data untuk dropdown (hanya pengajuan yang punya detail)
        $pengajuans = PengajuanStnk::orderBy('tanggal', 'desc')->get();

        $selectedPengajuan = null;
        $groupedData = [];
        $grandTotal = 0;

        $tanggal_realisasi = $request->input('tanggal_realisasi', date('Y-m-d'));

        if ($request->filled('pengajuan_id')) {
            $selectedPengajuan = PengajuanStnk::with([
                'details.suratJalan.samsat',
                'details.suratJalan.spk.motorType'
            ])->find($request->pengajuan_id);

            if ($selectedPengajuan) {
                // Filter hanya yang kepemilikan >= 2 DAN pajak_progresif > 0
                $filteredDetails = $selectedPengajuan->details->filter(function($detail) {
                    $samsat = $detail->suratJalan->samsat ?? null;
                    return $samsat && $samsat->jumlah_motor >= 2 && $samsat->pajak_progresif > 0;
                });

                // Algoritma Grouping (Tipe Motor + Kepemilikan + Pajak)
                $tempGroup = [];
                foreach ($filteredDetails as $detail) {
                    $sjk = $detail->suratJalan;
                    $tipe = strtoupper($sjk->spk->motorType->nama_type ?? '-');
                    $milik = $sjk->samsat->jumlah_motor;
                    $pajak = $sjk->samsat->pajak_progresif;
                    $nama = strtoupper($sjk->spk->nama_stnk ?? '-');

                    $key = $tipe . '_' . $milik . '_' . $pajak;

                    if (!isset($tempGroup[$key])) {
                        $tempGroup[$key] = [
                            'tipe_motor' => $tipe,
                            'milik' => $milik,
                            'pajak_progresif' => $pajak,
                            'names' => [],
                            'unit' => 0,
                            'sub_total' => 0
                        ];
                    }

                    $tempGroup[$key]['names'][] = $nama;
                    $tempGroup[$key]['unit'] += 1;
                    $tempGroup[$key]['sub_total'] += $pajak;

                    $grandTotal += $pajak;
                }

                // Re-index array agar rapi
                $groupedData = array_values($tempGroup);
            }
        }

        return view('transaction.realisasi-pajak.index', compact('pengajuans', 'selectedPengajuan', 'groupedData', 'grandTotal', 'tanggal_realisasi'));
    }

    public function print(Request $request)
    {
        $pengajuan = PengajuanStnk::with([
            'details.suratJalan.samsat',
            'details.suratJalan.spk.motorType'
        ])->findOrFail($request->pengajuan_id);

        $tanggal_realisasi = $request->tanggal_realisasi ?? date('Y-m-d');

        $filteredDetails = $pengajuan->details->filter(function($detail) {
            $samsat = $detail->suratJalan->samsat ?? null;
            return $samsat && $samsat->jumlah_motor >= 2 && $samsat->pajak_progresif > 0;
        });

        $tempGroup = [];
        $grandTotal = 0;
        foreach ($filteredDetails as $detail) {
            $sjk = $detail->suratJalan;
            $tipe = strtoupper($sjk->spk->motorType->nama_type ?? '-');
            $milik = $sjk->samsat->jumlah_motor;
            $pajak = $sjk->samsat->pajak_progresif;
            $nama = strtoupper($sjk->spk->nama_stnk ?? '-');

            $key = $tipe . '_' . $milik . '_' . $pajak;

            if (!isset($tempGroup[$key])) {
                $tempGroup[$key] = [
                    'tipe_motor' => $tipe,
                    'milik' => $milik,
                    'pajak_progresif' => $pajak,
                    'names' => [],
                    'unit' => 0,
                    'sub_total' => 0
                ];
            }
            $tempGroup[$key]['names'][] = $nama;
            $tempGroup[$key]['unit'] += 1;
            $tempGroup[$key]['sub_total'] += $pajak;
            $grandTotal += $pajak;
        }
        $groupedData = array_values($tempGroup);

        return view('transaction.realisasi-pajak.print', compact('pengajuan', 'groupedData', 'grandTotal', 'tanggal_realisasi'));
    }
}
