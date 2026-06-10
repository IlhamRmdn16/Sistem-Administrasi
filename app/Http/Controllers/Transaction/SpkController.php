<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Leasing;
use App\Models\MotorUnit;
use App\Models\Sales;
use App\Models\Spk;
use Illuminate\Http\Request;

class SpkController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');
        $per_page = $request->input('per_page', 10);

        $sales = Sales::orderBy('nama_sales')->get();
        $leasings = Leasing::orderBy('nama_leasing')->get();

        // Hanya mengambil unit yang statusnya 'Tersedia' beserta informasi lokasinya
        $motorUnits = MotorUnit::with(['type', 'color', 'lokasiPop'])
                        ->where('status_unit', 'Tersedia')
                        ->get();
                        
        $usedUnitIds = Spk::pluck('motor_unit_id')->filter()->toArray();

        $query = Spk::with(['sales', 'motorUnit.type', 'motorUnit.color', 'leasing']);

        if ($dari_tanggal && $sampai_tanggal) {
            $query->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal]);
        }

        if ($search) {
            $query->where('no_spk', 'like', "%{$search}%")
                  ->orWhere('nama_pemohon', 'like', "%{$search}%")
                  ->orWhereHas('sales', function ($q) use ($search) {
                      $q->where('nama_sales', 'like', "%{$search}%");
                  });
        }
        
        $spks = $query->latest()->paginate($per_page)->withQueryString();

        $now = now();
        $prefix = "SPK{$now->format('Y')}/{$now->format('m')}/";
        
        $existingNumbers = Spk::where('no_spk', 'like', "{$prefix}%")
            ->pluck('no_spk')
            ->map(function ($no_spk) {
                return intval(substr($no_spk, -4));
            })
            ->toArray();

        $nextNumber = 1;
        while (in_array($nextNumber, $existingNumbers)) {
            $nextNumber++;
        }

        $autoNoSpk = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('transaction.spk.index', compact('spks', 'sales', 'motorUnits', 'usedUnitIds', 'leasings', 'autoNoSpk', 'dari_tanggal', 'sampai_tanggal', 'per_page', 'search'));
    }

    public function store(Request $request)
    {
        $rules = $this->getValidationRules();
        $rules['no_spk'] = 'required|unique:spks,no_spk';

        $request->validate($rules);
        Spk::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data SPK berhasil dibuat!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $rules = $this->getValidationRules();
        $rules['no_spk'] = 'required|unique:spks,no_spk,'.$id;

        $request->validate($rules);

        $spk = Spk::findOrFail($id);

        $data = $request->all();
        if ($request->jenis_pembayaran == 'Cash') {
            $data['uang_muka'] = null;
            $data['tanda_jadi'] = null;
            $data['leasing_id'] = null;
            $data['tenor_bulan'] = null;
            $data['cicilan'] = null;
        }

        $spk->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data SPK berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        Spk::findOrFail($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data SPK berhasil dihapus!'
        ]);
    }

    public function print($id)
    {
        $spk = Spk::with(['sales', 'motorUnit.type', 'motorUnit.color', 'leasing'])->findOrFail($id);
        return view('transaction.spk.print', compact('spk'));
    }

    private function getValidationRules()
    {
        $rules = [
            'tanggal' => 'required|date',
            'sales_id' => 'required',
            'nama_pemohon' => 'required',
            'nama_stnk' => 'required',
            'alamat' => 'required',
            'rt_rw' => 'required',
            'desa_kelurahan' => 'required',
            'kecamatan' => 'required',
            'kota_kabupaten' => 'required',
            'telepon' => 'required',
            'nik' => 'required',
            'jenis_pembayaran' => 'required',
            'motor_unit_id' => 'required',
            'harga_otr' => 'required|numeric',
        ];

        if (request('jenis_pembayaran') == 'Kredit') {
            $rules['uang_muka'] = 'required|numeric';
            $rules['tanda_jadi'] = 'required|numeric';
            $rules['leasing_id'] = 'required';
            $rules['tenor_bulan'] = 'required|numeric';
            $rules['cicilan'] = 'required|numeric';
        }

        return $rules;
    }
}
