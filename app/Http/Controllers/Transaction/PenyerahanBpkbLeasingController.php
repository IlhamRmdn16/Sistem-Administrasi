<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Leasing;
use App\Models\PenyerahanBpkbLeasing;
use App\Models\PenyerahanBpkbLeasingDetail;
use App\Models\SuratJalan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenyerahanBpkbLeasingController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $leasings = Leasing::all();

        return view('transaction.penyerahan-bpkb-leasing.index', compact('leasings', 'tanggal'));
    }

    public function show(Request $request, $leasing_id)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $leasing = Leasing::findOrFail($leasing_id);

        $document = PenyerahanBpkbLeasing::with(['details.suratJalan.spk', 'details.suratJalan.samsat', 'details.suratJalan.motorUnit.type'])
            ->where('leasing_id', $leasing_id)
            ->whereDate('tanggal', $tanggal)
            ->first();

        $no_bukti = '';
        $items = collect();

        if ($document) {
            $no_bukti = $document->no_bukti;
            $items = $document->details->map(function ($detail) {
                return $detail->suratJalan;
            });
        } else {
            $now = Carbon::parse($tanggal);
            $prefix = 'PSL' . $now->format('Y/m/');

            $lastDoc = PenyerahanBpkbLeasing::where('no_bukti', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            $urut = 1;
            if ($lastDoc) {
                $lastUrut = (int) substr($lastDoc->no_bukti, -4);
                $urut = $lastUrut + 1;
            }
            $no_bukti = $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);

            $items = SuratJalan::whereHas('spk', function ($q) use ($leasing_id) {
                    $q->where('leasing_id', $leasing_id);
                })
                ->whereHas('samsat', function ($q) {
                    $q->whereNotNull('no_bpkb')->where('no_bpkb', '!=', '');
                })
                ->whereNotIn('id', function ($q) {
                    $q->select('surat_jalan_id')->from('penyerahan_bpkb_leasing_details');
                })
                ->with(['spk', 'samsat', 'motorUnit.type'])
                ->get();
        }

        return view('transaction.penyerahan-bpkb-leasing.show', compact('leasing', 'tanggal', 'document', 'no_bukti', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leasing_id' => 'required|exists:leasings,id',
            'tanggal' => 'required|date',
            'no_bukti' => 'required|unique:penyerahan_bpkb_leasings,no_bukti',
            'sjk_ids' => 'required|array|min:1',
            'sjk_ids.*' => 'exists:surat_jalans,id'
        ]);

        try {
            DB::beginTransaction();

            $header = PenyerahanBpkbLeasing::create([
                'no_bukti' => $request->no_bukti,
                'tanggal' => $request->tanggal,
                'leasing_id' => $request->leasing_id,
            ]);

            foreach ($request->sjk_ids as $sjk_id) {
                PenyerahanBpkbLeasingDetail::create([
                    'penyerahan_bpkb_leasing_id' => $header->id,
                    'surat_jalan_id' => $sjk_id,
                ]);
            }

            DB::commit();

            return redirect()->route('penyerahan-bpkb-leasing.print', $header->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.');
        }
    }

    public function destroy($id)
    {
        try {
            $document = PenyerahanBpkbLeasing::findOrFail($id);
            $leasing_id = $document->leasing_id;
            $tanggal = $document->tanggal;

            $document->delete();

            return redirect()->route('penyerahan-bpkb-leasing.show', ['leasing_id' => $leasing_id, 'tanggal' => $tanggal])
                             ->with('success', 'Data penyerahan BPKB berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan dokumen.');
        }
    }

    public function print($id)
    {
        $document = PenyerahanBpkbLeasing::with(['leasing', 'details.suratJalan.spk', 'details.suratJalan.samsat', 'details.suratJalan.motorUnit.type'])
            ->findOrFail($id);

        return view('transaction.penyerahan-bpkb-leasing.print', compact('document'));
    }
}
