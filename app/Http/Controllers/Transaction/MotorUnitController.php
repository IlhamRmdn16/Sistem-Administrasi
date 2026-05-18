<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\MotorType;
use App\Models\MotorUnit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MotorUnitController extends Controller
{
   public function index(Request $request)
    {
        $search = $request->input('search');
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');
        $per_page = $request->input('per_page', 10);
        
        $types = MotorType::with('colors')->get();

        $query = MotorUnit::with(['type', 'color']);

        // Menggunakan kolom created_at atau tanggal_sp untuk filter periode.
        // Di sini kita gunakan tanggal_sp berdasarkan format dd/mm/yyyy yang tersimpan di kolom no_sp
        if ($dari_tanggal && $sampai_tanggal) {
             // Karena tanggal bergabung di kolom no_sp dengan format "No / dd/mm/yyyy"
             // Pendekatan terbaik adalah memfilter rentang tanggal jika Anda menyimpan tanggal_sp terpisah
             // Jika 'tanggal_sp' tidak ada di database, kita asumsikan menggunakan 'created_at' untuk filter periode pendaftaran unit.
            $query->whereBetween('created_at', [$dari_tanggal . ' 00:00:00', $sampai_tanggal . ' 23:59:59']);
        }

        if ($search) {
            $query->where('no_mesin', 'like', "%{$search}%")
                  ->orWhere('no_rangka', 'like', "%{$search}%")
                  ->orWhere('no_do', 'like', "%{$search}%")
                  ->orWhereHas('type', function ($q) use ($search) {
                      $q->where('nama_type', 'like', "%{$search}%");
                  });
        }

        $motorUnits = $query->latest()->paginate($per_page)->withQueryString();

        return view('transaction.motor-unit.index', compact('motorUnits', 'types', 'search', 'dari_tanggal', 'sampai_tanggal', 'per_page'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_do' => 'required',
            'no_sp' => 'required',
            'tanggal_sp' => 'required|date',
            'motor_type_id' => 'required',
            'motor_color_id' => 'required',
            'no_mesin' => 'required|unique:motor_units',
            'no_rangka' => 'required|unique:motor_units',
            'no_seri_kunci' => 'required',
            'no_kunci' => 'required',
            'tahun_pembuatan' => 'required',
            'no_accu' => 'required',
        ]);

        $tanggal = Carbon::parse($request->tanggal_sp)->format('d/m/Y');
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
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Kendaraan berhasil diregistrasi!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'no_do' => 'required',
            'no_sp' => 'required',
            'tanggal_sp' => 'required|date',
            'motor_type_id' => 'required',
            'motor_color_id' => 'required',
            'no_mesin' => 'required|unique:motor_units,no_mesin,'.$id,
            'no_rangka' => 'required|unique:motor_units,no_rangka,'.$id,
            'no_seri_kunci' => 'required',
            'no_kunci' => 'required',
            'tahun_pembuatan' => 'required',
            'no_accu' => 'required',
        ]);

        $tanggal = Carbon::parse($request->tanggal_sp)->format('d/m/Y');
        $no_sp_full = $request->no_sp . ' / ' . $tanggal;

        $unit = MotorUnit::findOrFail($id);

        $unit->update([
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
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Kendaraan berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $unit = MotorUnit::findOrFail($id);
        $unit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Kendaraan berhasil dihapus!'
        ]);
    }

    public function print($id)
    {
        $unit = MotorUnit::with(['type', 'color'])->findOrFail($id);
        return view('transaction.motor-unit.print', compact('unit'));
    }
}
