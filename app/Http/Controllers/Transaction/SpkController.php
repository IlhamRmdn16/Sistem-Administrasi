<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Leasing;
use App\Models\MotorType;
use App\Models\Sales;
use App\Models\Spk;
use Illuminate\Http\Request;

class SpkController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $sales = Sales::orderBy('nama_sales')->get();
        $motorTypes = MotorType::with('colors')->orderBy('nama_type')->get();
        $leasings = Leasing::orderBy('nama_leasing')->get();

        $query = Spk::with(['sales', 'motorType', 'motorColor', 'leasing']);
        if ($search) {
            $query->where('no_spk', 'like', "%{$search}%")
                  ->orWhere('nama_pemohon', 'like', "%{$search}%")
                  ->orWhereHas('sales', function ($q) use ($search) {
                      $q->where('nama_sales', 'like', "%{$search}%");
                  });
        }
        $spks = $query->latest()->paginate(10)->withQueryString();

        $now = now();
        $prefix = "SPK{$now->format('Y')}/{$now->format('m')}/";
        $lastSpk = Spk::where('no_spk', 'like', "{$prefix}%")->latest('no_spk')->first();

        $nextNumber = $lastSpk ? str_pad(intval(substr($lastSpk->no_spk, -4)) + 1, 4, '0', STR_PAD_LEFT) : '0001';
        $autoNoSpk = $prefix . $nextNumber;

        return view('transaction.spk.index', compact('spks', 'sales', 'motorTypes', 'leasings', 'autoNoSpk'));
    }

    public function store(Request $request)
    {
        $rules = $this->getValidationRules();
        $rules['no_spk'] = 'required|unique:spks,no_spk';

        $request->validate($rules);
        Spk::create($request->all());

        return back()->with('success', 'Data SPK berhasil dibuat!');
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

        return back()->with('success', 'Data SPK berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Spk::findOrFail($id)->delete();
        return back()->with('success', 'Data SPK berhasil dihapus!');
    }

    public function print($id)
    {
        $spk = Spk::with(['sales', 'motorType', 'motorColor', 'leasing'])->findOrFail($id);
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
            'motor_type_id' => 'required',
            'motor_color_id' => 'required',
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
