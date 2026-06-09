<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\SuratJalan;
use Illuminate\Http\Request;

class CetakBlankoSamsatController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));

        $suratJalans = SuratJalan::with(['spk', 'motorUnit.type', 'motorUnit.color'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->latest('tanggal')
            ->paginate(50)
            ->withQueryString();

        return view('samsat.cetak-blanko.index', compact('suratJalans', 'startDate', 'endDate'));
    }

    public function print($id)
    {
        $suratJalan = SuratJalan::with(['spk', 'motorUnit.type', 'motorUnit.color'])->findOrFail($id);

        if (!$suratJalan->is_cetak_samsat) {
            $suratJalan->is_cetak_samsat = true;
            $suratJalan->save();
        }

        return view('samsat.cetak-blanko.print', compact('suratJalan'));
    }
}
