<?php

namespace App\Http\Controllers;

use App\Models\MotorType;
use App\Models\MotorUnit;
use Illuminate\Http\Request;

class LaporanStokController extends Controller
{
   private function getBaseQuery($search)
    {
        $query = MotorType::withCount([
            'motorUnits as stok_gudang' => function($q) {
                $q->where('status_unit', 'Tersedia')->whereIn('posisi_stok', ['Gudang 1', 'Gudang 2']);
            },
            'motorUnits as stok_showroom' => function($q) {
                // PERBAIKAN: Disamakan persis dengan data MutasiStokController
                $q->where('status_unit', 'Tersedia')->where('posisi_stok', 'Showroom Pusat');
            },
            'motorUnits as stok_pop' => function($q) {
                $q->where('status_unit', 'Tersedia')->where('posisi_stok', 'POP');
            },
            'motorUnits as stok_gp' => function($q) {
                $q->where('status_unit', 'Tersedia')->where('posisi_stok', 'Showroom GP');
            },
            'motorUnits as stok_total' => function($q) {
                $q->where('status_unit', 'Tersedia');
            }
        ]);

        // PERBAIKAN: Mengganti havingRaw menjadi whereHas agar 100% aman di semua versi database
        // Hanya tampilkan Tipe Motor yang punya minimal 1 unit dengan status 'Tersedia'
        $query->whereHas('motorUnits', function($q) {
            $q->where('status_unit', 'Tersedia');
        });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_tipe', 'like', "%{$search}%")
                  ->orWhere('nama_type', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    // Helper untuk menghitung total akumulasi seluruh baris yang difilter
    private function getGlobalTotals($search)
    {
        $baseUnitQuery = MotorUnit::where('status_unit', 'Tersedia')
            ->whereHas('type', function($q) use ($search) {
                if ($search) {
                    $q->where('kode_tipe', 'like', "%{$search}%")
                      ->orWhere('nama_type', 'like', "%{$search}%");
                }
            });

        return [
            'gudang' => (clone $baseUnitQuery)->whereIn('posisi_stok', ['Gudang 1', 'Gudang 2'])->count(),
            // PERBAIKAN: Disamakan persis dengan data MutasiStokController
            'showroom' => (clone $baseUnitQuery)->where('posisi_stok', 'Showroom Pusat')->count(),
            'pop' => (clone $baseUnitQuery)->where('posisi_stok', 'POP')->count(),
            'gp' => (clone $baseUnitQuery)->where('posisi_stok', 'Showroom GP')->count(),
            'total' => (clone $baseUnitQuery)->count(),
        ];
    }

    public function global(Request $request)
    {
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);

        // Panggil helper getBaseQuery()
        $query = $this->getBaseQuery($search);

        $stokTypes = $query->paginate($per_page)->withQueryString();
        $totals = $this->getGlobalTotals($search);

        return view('laporan.stok.global', compact('stokTypes', 'totals', 'search', 'per_page'));
    }

    public function printGlobal(Request $request)
    {
        $search = $request->input('search');

        // Tarik semua data tanpa paginasi untuk kebutuhan cetak
        $stokTypes = $this->getBaseQuery($search)->get();
        $totals = $this->getGlobalTotals($search);

        return view('laporan.stok.print-global', compact('stokTypes', 'totals', 'search'));
    }

    public function warna(Request $request)
    {
        $searchTipe = $request->input('search_tipe');
        $searchWarna = $request->input('search_warna');
        $per_page = $request->input('per_page', 10);

        // Tarik tipe motor beserta warna-warnanya
        $query = MotorType::with(['colors' => function($q) use ($searchWarna) {
                if ($searchWarna) {
                    $q->where('warna', 'like', "%{$searchWarna}%");
                }
                $q->withCount([
                    'motorUnits as stok_gudang' => function($sq) { $sq->where('status_unit', 'Tersedia')->whereIn('posisi_stok', ['Gudang 1', 'Gudang 2']); },
                    'motorUnits as stok_showroom' => function($sq) { $sq->where('status_unit', 'Tersedia')->where('posisi_stok', 'Showroom Pusat'); },
                    'motorUnits as stok_pop' => function($sq) { $sq->where('status_unit', 'Tersedia')->where('posisi_stok', 'POP'); },
                    'motorUnits as stok_gp' => function($sq) { $sq->where('status_unit', 'Tersedia')->where('posisi_stok', 'Showroom GP'); },
                    'motorUnits as stok_total' => function($sq) { $sq->where('status_unit', 'Tersedia'); }
                ]);
            }])
            ->withCount([
                // Hitung Sub-Total untuk masing-masing Tipe (Filter by Warna jika ada pencarian warna)
                'motorUnits as stok_gudang' => function($q) use ($searchWarna) {
                    $q->where('status_unit', 'Tersedia')->whereIn('posisi_stok', ['Gudang 1', 'Gudang 2']);
                    if ($searchWarna) $q->whereHas('color', function($c) use($searchWarna) { $c->where('warna', 'like', "%{$searchWarna}%"); });
                },
                'motorUnits as stok_showroom' => function($q) use ($searchWarna) {
                    $q->where('status_unit', 'Tersedia')->where('posisi_stok', 'Showroom Pusat');
                    if ($searchWarna) $q->whereHas('color', function($c) use($searchWarna) { $c->where('warna', 'like', "%{$searchWarna}%"); });
                },
                'motorUnits as stok_pop' => function($q) use ($searchWarna) {
                    $q->where('status_unit', 'Tersedia')->where('posisi_stok', 'POP');
                    if ($searchWarna) $q->whereHas('color', function($c) use($searchWarna) { $c->where('warna', 'like', "%{$searchWarna}%"); });
                },
                'motorUnits as stok_gp' => function($q) use ($searchWarna) {
                    $q->where('status_unit', 'Tersedia')->where('posisi_stok', 'Showroom GP');
                    if ($searchWarna) $q->whereHas('color', function($c) use($searchWarna) { $c->where('warna', 'like', "%{$searchWarna}%"); });
                },
                'motorUnits as stok_total' => function($q) use ($searchWarna) {
                    $q->where('status_unit', 'Tersedia');
                    if ($searchWarna) $q->whereHas('color', function($c) use($searchWarna) { $c->where('warna', 'like', "%{$searchWarna}%"); });
                }
            ])
            ->whereHas('motorUnits', function($q) use ($searchWarna) {
                // Pastikan tipe motor yang ditarik minimal punya 1 stok yang 'Tersedia'
                $q->where('status_unit', 'Tersedia');
                if ($searchWarna) {
                    $q->whereHas('color', function($c) use($searchWarna) { $c->where('warna', 'like', "%{$searchWarna}%"); });
                }
            });

        // Filter berdasarkan Tipe/Kode Motor
        if ($searchTipe) {
            $query->where(function($q) use ($searchTipe) {
                $q->where('kode_tipe', 'like', "%{$searchTipe}%")
                  ->orWhere('nama_type', 'like', "%{$searchTipe}%");
            });
        }

        $stokTypes = $query->paginate($per_page)->withQueryString();

        // Hitung Grand Total
        $baseUnitQuery = MotorUnit::where('status_unit', 'Tersedia')
            ->when($searchTipe, function($q) use ($searchTipe) {
                $q->whereHas('type', function($t) use ($searchTipe) {
                    $t->where('kode_tipe', 'like', "%{$searchTipe}%")
                      ->orWhere('nama_type', 'like', "%{$searchTipe}%");
                });
            })
            ->when($searchWarna, function($q) use ($searchWarna) {
                $q->whereHas('color', function($c) use ($searchWarna) {
                    $c->where('warna', 'like', "%{$searchWarna}%");
                });
            });

        $totals = [
            'gudang' => (clone $baseUnitQuery)->whereIn('posisi_stok', ['Gudang 1', 'Gudang 2'])->count(),
            'showroom' => (clone $baseUnitQuery)->where('posisi_stok', 'Showroom Pusat')->count(),
            'pop' => (clone $baseUnitQuery)->where('posisi_stok', 'POP')->count(),
            'gp' => (clone $baseUnitQuery)->where('posisi_stok', 'Showroom GP')->count(),
            'total' => (clone $baseUnitQuery)->count(),
        ];

        return view('laporan.stok.warna', compact('stokTypes', 'totals', 'searchTipe', 'searchWarna', 'per_page'));
    }

    public function printWarna(Request $request)
    {
        $searchTipe = $request->input('search_tipe');
        $searchWarna = $request->input('search_warna');

        // Logic query sama dengan method warna(), tapi menggunakan ->get() alih-alih paginate()
        $query = MotorType::with(['colors' => function($q) use ($searchWarna) {
                if ($searchWarna) { $q->where('warna', 'like', "%{$searchWarna}%"); }
                $q->withCount([
                    'motorUnits as stok_gudang' => function($sq) { $sq->where('status_unit', 'Tersedia')->whereIn('posisi_stok', ['Gudang 1', 'Gudang 2']); },
                    'motorUnits as stok_showroom' => function($sq) { $sq->where('status_unit', 'Tersedia')->where('posisi_stok', 'Showroom Pusat'); },
                    'motorUnits as stok_pop' => function($sq) { $sq->where('status_unit', 'Tersedia')->where('posisi_stok', 'POP'); },
                    'motorUnits as stok_gp' => function($sq) { $sq->where('status_unit', 'Tersedia')->where('posisi_stok', 'Showroom GP'); },
                    'motorUnits as stok_total' => function($sq) { $sq->where('status_unit', 'Tersedia'); }
                ]);
            }])
            ->withCount([
                'motorUnits as stok_gudang' => function($q) use ($searchWarna) {
                    $q->where('status_unit', 'Tersedia')->whereIn('posisi_stok', ['Gudang 1', 'Gudang 2']);
                    if ($searchWarna) $q->whereHas('color', function($c) use($searchWarna) { $c->where('warna', 'like', "%{$searchWarna}%"); });
                },
                'motorUnits as stok_showroom' => function($q) use ($searchWarna) {
                    $q->where('status_unit', 'Tersedia')->where('posisi_stok', 'Showroom Pusat');
                    if ($searchWarna) $q->whereHas('color', function($c) use($searchWarna) { $c->where('warna', 'like', "%{$searchWarna}%"); });
                },
                'motorUnits as stok_pop' => function($q) use ($searchWarna) {
                    $q->where('status_unit', 'Tersedia')->where('posisi_stok', 'POP');
                    if ($searchWarna) $q->whereHas('color', function($c) use($searchWarna) { $c->where('warna', 'like', "%{$searchWarna}%"); });
                },
                'motorUnits as stok_gp' => function($q) use ($searchWarna) {
                    $q->where('status_unit', 'Tersedia')->where('posisi_stok', 'Showroom GP');
                    if ($searchWarna) $q->whereHas('color', function($c) use($searchWarna) { $c->where('warna', 'like', "%{$searchWarna}%"); });
                },
                'motorUnits as stok_total' => function($q) use ($searchWarna) {
                    $q->where('status_unit', 'Tersedia');
                    if ($searchWarna) $q->whereHas('color', function($c) use($searchWarna) { $c->where('warna', 'like', "%{$searchWarna}%"); });
                }
            ])
            ->whereHas('motorUnits', function($q) use ($searchWarna) {
                $q->where('status_unit', 'Tersedia');
                if ($searchWarna) { $q->whereHas('color', function($c) use($searchWarna) { $c->where('warna', 'like', "%{$searchWarna}%"); }); }
            });

        if ($searchTipe) {
            $query->where(function($q) use ($searchTipe) {
                $q->where('kode_tipe', 'like', "%{$searchTipe}%")->orWhere('nama_type', 'like', "%{$searchTipe}%");
            });
        }

        $stokTypes = $query->get();

        $baseUnitQuery = MotorUnit::where('status_unit', 'Tersedia')
            ->when($searchTipe, function($q) use ($searchTipe) {
                $q->whereHas('type', function($t) use ($searchTipe) { $t->where('kode_tipe', 'like', "%{$searchTipe}%")->orWhere('nama_type', 'like', "%{$searchTipe}%"); });
            })
            ->when($searchWarna, function($q) use ($searchWarna) {
                $q->whereHas('color', function($c) use ($searchWarna) { $c->where('warna', 'like', "%{$searchWarna}%"); });
            });

        $totals = [
            'gudang' => (clone $baseUnitQuery)->whereIn('posisi_stok', ['Gudang 1', 'Gudang 2'])->count(),
            'showroom' => (clone $baseUnitQuery)->where('posisi_stok', 'Showroom Pusat')->count(),
            'pop' => (clone $baseUnitQuery)->where('posisi_stok', 'POP')->count(),
            'gp' => (clone $baseUnitQuery)->where('posisi_stok', 'Showroom GP')->count(),
            'total' => (clone $baseUnitQuery)->count(),
        ];

        return view('laporan.stok.print-warna', compact('stokTypes', 'totals', 'searchTipe', 'searchWarna'));
    }
}
