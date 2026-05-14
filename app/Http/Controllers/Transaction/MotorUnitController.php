<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\MotorType;
use App\Models\MotorUnit;
use Illuminate\Http\Request;

class MotorUnitController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $types = MotorType::with('colors')->get();

        $query = MotorUnit::with(['type', 'color']);

        if ($search) {
            $query->where('no_mesin', 'like', "%{$search}%")
                  ->orWhere('no_rangka', 'like', "%{$search}%")
                  ->orWhere('no_do', 'like', "%{$search}%")
                  ->orWhereHas('type', function ($q) use ($search) {
                      $q->where('nama_type', 'like', "%{$search}%");
                  });
        }

        $motorUnits = $query->latest()->paginate(10)->withQueryString();

        return view('transaction.motor-unit.index', compact('motorUnits', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_do' => 'required',
            'no_sp' => 'required',
            'motor_type_id' => 'required',
            'motor_color_id' => 'required',
            'no_mesin' => 'required|unique:motor_units',
            'no_rangka' => 'required|unique:motor_units',
            'no_seri_kunci' => 'required',
            'no_kunci' => 'required',
            'tahun_pembuatan' => 'required',
            'no_accu' => 'required',
        ]);

        $tanggal = date('d/m/Y');
        $no_sp_full = $request->no_sp . ' / ' . $tanggal;

        MotorUnit::create([
            'no_do' => $request->no_do,
            'no_sp' => $no_sp_full,
            'motor_type_id' => $request->motor_type_id,
            'motor_color_id' => $request->motor_color_id,
            'no_mesin' => $request->no_mesin,
            'no_rangka' => $request->no_rangka,
            'no_seri_kunci' => $request->no_seri_kunci,
            'no_kunci' => $request->no_kunci,
            'tahun_pembuatan' => $request->tahun_pembuatan,
            'no_accu' => $request->no_accu,
            'status' => 'Tersedia'
        ]);

        return back()->with('success', 'Data Kendaraan berhasil diregistrasi!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'no_do' => 'required',
            'no_sp' => 'required',
            'motor_type_id' => 'required',
            'motor_color_id' => 'required',
            'no_mesin' => 'required|unique:motor_units,no_mesin,'.$id,
            'no_rangka' => 'required|unique:motor_units,no_rangka,'.$id,
            'no_seri_kunci' => 'required',
            'no_kunci' => 'required',
            'tahun_pembuatan' => 'required',
            'no_accu' => 'required',
        ]);

        $unit = MotorUnit::findOrFail($id);
        
        $unit->update([
            'no_do' => $request->no_do,
            'no_sp' => $request->no_sp,
            'motor_type_id' => $request->motor_type_id,
            'motor_color_id' => $request->motor_color_id,
            'no_mesin' => $request->no_mesin,
            'no_rangka' => $request->no_rangka,
            'no_seri_kunci' => $request->no_seri_kunci,
            'no_kunci' => $request->no_kunci,
            'tahun_pembuatan' => $request->tahun_pembuatan,
            'no_accu' => $request->no_accu,
        ]);

        return back()->with('success', 'Data Kendaraan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $unit = MotorUnit::findOrFail($id);
        $unit->delete();

        return back()->with('success', 'Data Kendaraan berhasil dihapus!');
    }
}
