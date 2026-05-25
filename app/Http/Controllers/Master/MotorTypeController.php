<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MotorType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MotorTypeController extends Controller
{
   public function index(Request $request)
    {
        $search = $request->input('search');
        $filter_tahun = $request->input('filter_tahun');
        $per_page = $request->input('per_page', 10);

        $query = MotorType::with('colors');

        if ($filter_tahun) {
            $query->where('tahun_pembuatan', $filter_tahun);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_motor', 'like', "%{$search}%")
                  ->orWhere('kode_tipe', 'like', "%{$search}%")
                  ->orWhere('nama_type', 'like', "%{$search}%");
            });
        }

        $motorTypes = $query->latest()->paginate($per_page)->withQueryString();

        $years = MotorType::select('tahun_pembuatan')
            ->whereNotNull('tahun_pembuatan')
            ->distinct()
            ->orderBy('tahun_pembuatan', 'desc')
            ->pluck('tahun_pembuatan');

        $existingNumbers = MotorType::pluck('kode_tipe')
            ->map(function ($kode_tipe) {
                return intval($kode_tipe);
            })
            ->toArray();

        $nextNumber = 1;
        while (in_array($nextNumber, $existingNumbers)) {
            $nextNumber++;
        }

        $autoKodeTipe = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return view('master.motor-type.index', compact('motorTypes', 'autoKodeTipe', 'search', 'filter_tahun', 'per_page', 'years'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_tipe' => 'required|unique:motor_types,kode_tipe',
            'jenis' => 'required',
            'nama_type' => 'required',
            'tahun_pembuatan' => 'required',
            'kode_motor' => 'required|unique:motor_types,kode_motor',
            'sampul_buku' => 'required|array',
            'otr' => 'required|numeric',
            'notice_pajak' => 'required|numeric',
            'colors' => 'required|array',
            'colors.*.warna' => 'required',
            'colors.*.kode_warna' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $motorType = MotorType::create([
                'kode_tipe' => $request->kode_tipe,
                'jenis' => $request->jenis,
                'nama_type' => $request->nama_type,
                'tahun_pembuatan' => $request->tahun_pembuatan,
                'kode_motor' => $request->kode_motor,
                'sampul_buku' => $request->sampul_buku,
                'otr' => (int) round($request->otr),
                'notice_pajak' => (int) round($request->notice_pajak),
            ]);

            foreach ($request->colors as $color) {
                $motorType->colors()->create([
                    'warna' => $color['warna'],
                    'kode_warna' => $color['kode_warna'],
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Data Motor berhasil disimpan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_tipe' => 'required|unique:motor_types,kode_tipe,'.$id,
            'jenis' => 'required',
            'nama_type' => 'required',
            'tahun_pembuatan' => 'required',
            'kode_motor' => 'required|unique:motor_types,kode_motor,'.$id,
            'sampul_buku' => 'required|array',
            'otr' => 'required|numeric',
            'notice_pajak' => 'required|numeric',
            'colors' => 'required|array',
            'colors.*.warna' => 'required',
            'colors.*.kode_warna' => 'required',
        ]);

        DB::transaction(function () use ($request, $id) {
            $motorType = MotorType::findOrFail($id);

            $motorType->update([
                'kode_tipe' => $request->kode_tipe,
                'jenis' => $request->jenis,
                'nama_type' => $request->nama_type,
                'tahun_pembuatan' => $request->tahun_pembuatan,
                'kode_motor' => $request->kode_motor,
                'sampul_buku' => $request->sampul_buku,
                'otr' => (int) round($request->otr),
                'notice_pajak' => (int) round($request->notice_pajak),
            ]);

            $submittedColorIds = [];

            foreach ($request->colors as $colorData) {
                if (!empty($colorData['id'])) {
                    $existingColor = $motorType->colors()->find($colorData['id']);
                    if ($existingColor) {
                        $existingColor->update([
                            'warna' => $colorData['warna'],
                            'kode_warna' => $colorData['kode_warna'],
                        ]);
                        $submittedColorIds[] = $existingColor->id;
                    }
                } else {
                    $newColor = $motorType->colors()->create([
                        'warna' => $colorData['warna'],
                        'kode_warna' => $colorData['kode_warna'],
                    ]);
                    $submittedColorIds[] = $newColor->id;
                }
            }

            $colorsToDelete = $motorType->colors()->whereNotIn('id', $submittedColorIds)->get();
            foreach ($colorsToDelete as $oldColor) {
                try {
                    $oldColor->delete();
                } catch (\Exception $e) {
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Data Motor berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $motorType = MotorType::findOrFail($id);
        $motorType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Motor berhasil dihapus!'
        ]);
    }
}
