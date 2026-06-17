<?php

namespace App\Http\Controllers;

use App\Models\MotorType;
use App\Models\MotorUnit;
use App\Models\MutasiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function detil(Request $request)
    {
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);

        // Ambil data unit yang berstatus Tersedia
        $query = MotorUnit::with(['type', 'color'])
            ->where('status_unit', 'Tersedia');

        // Fitur Pencarian Multi-Kolom (Mesin, Rangka, Kunci, Tipe, Warna)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_mesin', 'like', "%{$search}%")
                  ->orWhere('no_rangka', 'like', "%{$search}%")
                  ->orWhere('no_kunci', 'like', "%{$search}%")
                  ->orWhereHas('type', function($t) use ($search) {
                      $t->where('nama_type', 'like', "%{$search}%")
                        ->orWhere('kode_tipe', 'like', "%{$search}%");
                  })
                  ->orWhereHas('color', function($c) use ($search) {
                      $c->where('warna', 'like', "%{$search}%");
                  });
            });
        }

        // Urutkan berdasarkan unit yang paling baru didaftarkan
        $units = $query->latest()->paginate($per_page)->withQueryString();

        return view('laporan.stok.detil', compact('units', 'search', 'per_page'));
    }

    public function printDetil(Request $request)
    {
        $search = $request->input('search');

        $query = MotorUnit::with(['type', 'color'])
            ->where('status_unit', 'Tersedia');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_mesin', 'like', "%{$search}%")
                  ->orWhere('no_rangka', 'like', "%{$search}%")
                  ->orWhere('no_kunci', 'like', "%{$search}%")
                  ->orWhereHas('type', function($t) use ($search) {
                      $t->where('nama_type', 'like', "%{$search}%")
                        ->orWhere('kode_tipe', 'like', "%{$search}%");
                  })
                  ->orWhereHas('color', function($c) use ($search) {
                      $c->where('warna', 'like', "%{$search}%");
                  });
            });
        }

        $units = $query->latest()->get();

        return view('laporan.stok.print-detil', compact('units', 'search'));
    }
    public function salesGlobal(Request $request)
    {
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);

        // Grouping unit berdasarkan kombinasi Sales, Tipe, dan Warna
        $query = MotorUnit::select('lokasi_pop_id', 'motor_type_id', 'motor_color_id', DB::raw('count(*) as stok_unit'))
            ->where('status_unit', 'Tersedia')
            ->where('posisi_stok', 'POP')
            ->with(['lokasiPop', 'type', 'color'])
            ->groupBy('lokasi_pop_id', 'motor_type_id', 'motor_color_id');

        // Fitur Pencarian Multi-Kolom (Nama Sales/POP, Tipe Motor, Warna)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('lokasiPop', function($s) use ($search) {
                    $s->where('nama_sales', 'like', "%{$search}%");
                })
                ->orWhereHas('type', function($t) use ($search) {
                    $t->where('nama_type', 'like', "%{$search}%")->orWhere('kode_tipe', 'like', "%{$search}%");
                })
                ->orWhereHas('color', function($c) use ($search) {
                    $c->where('warna', 'like', "%{$search}%");
                });
            });
        }

        // Urutkan berdasarkan lokasi_pop_id agar pengelompokan baris rapi
        $salesStoks = $query->orderBy('lokasi_pop_id')->paginate($per_page)->withQueryString();

        // Hitung Grand Total Stok yang ada di POP
        $grandTotalQuery = MotorUnit::where('status_unit', 'Tersedia')->where('posisi_stok', 'POP');
        if ($search) {
            $grandTotalQuery->where(function($q) use ($search) {
                $q->whereHas('lokasiPop', function($s) use ($search) { $s->where('nama_sales', 'like', "%{$search}%"); })
                ->orWhereHas('type', function($t) use ($search) { $t->where('nama_type', 'like', "%{$search}%")->orWhere('kode_tipe', 'like', "%{$search}%"); })
                ->orWhereHas('color', function($c) use ($search) { $c->where('warna', 'like', "%{$search}%"); });
            });
        }
        $grandTotal = $grandTotalQuery->count();

        return view('laporan.stok.sales-global', compact('salesStoks', 'grandTotal', 'search', 'per_page'));
    }

    public function printSalesGlobal(Request $request)
    {
        $search = $request->input('search');

        $query = MotorUnit::select('lokasi_pop_id', 'motor_type_id', 'motor_color_id', DB::raw('count(*) as stok_unit'))
            ->where('status_unit', 'Tersedia')
            ->where('posisi_stok', 'POP')
            ->with(['lokasiPop', 'type', 'color'])
            ->groupBy('lokasi_pop_id', 'motor_type_id', 'motor_color_id')
            ->orderBy('lokasi_pop_id');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('lokasiPop', function($s) use ($search) { $s->where('nama_sales', 'like', "%{$search}%"); })
                ->orWhereHas('type', function($t) use ($search) { $t->where('nama_type', 'like', "%{$search}%")->orWhere('kode_tipe', 'like', "%{$search}%"); })
                ->orWhereHas('color', function($c) use ($search) { $c->where('warna', 'like', "%{$search}%"); });
            });
        }

        $salesStoks = $query->get();
        $grandTotal = $salesStoks->sum('stok_unit');

        return view('laporan.stok.print-sales-global', compact('salesStoks', 'grandTotal', 'search'));
    }

    // ... [Fungsi-fungsi laporan sebelumnya tetap ada] ...

    public function salesDetil(Request $request)
    {
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);

        // Query Motor Unit + Subquery untuk mengambil tanggal mutasi terakhir
        $query = MotorUnit::select('motor_units.*')
            ->addSelect(['tgl_mutasi' => MutasiDetail::select('mutasis.tanggal')
                ->join('mutasis', 'mutasis.id', '=', 'mutasi_details.mutasi_id')
                ->whereColumn('mutasi_details.motor_unit_id', 'motor_units.id')
                ->orderBy('mutasis.tanggal', 'desc')
                ->limit(1)
            ])
            ->with(['lokasiPop', 'type', 'color'])
            ->where('status_unit', 'Tersedia')
            ->where('posisi_stok', 'POP');

        // Fitur Pencarian Multi-Kolom
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_mesin', 'like', "%{$search}%")
                  ->orWhere('no_rangka', 'like', "%{$search}%")
                  ->orWhere('no_kunci', 'like', "%{$search}%")
                  ->orWhere('tahun_pembuatan', 'like', "%{$search}%")
                  ->orWhereHas('lokasiPop', function($s) use ($search) {
                      $s->where('nama_sales', 'like', "%{$search}%");
                  })
                  ->orWhereHas('type', function($t) use ($search) {
                      $t->where('nama_type', 'like', "%{$search}%")->orWhere('kode_tipe', 'like', "%{$search}%");
                  })
                  ->orWhereHas('color', function($c) use ($search) {
                      $c->where('warna', 'like', "%{$search}%");
                  });
            });
        }

        // Urutkan berdasarkan Sales/POP terlebih dahulu agar rapi
        $salesDetils = $query->orderBy('lokasi_pop_id')->paginate($per_page)->withQueryString();

        return view('laporan.stok.sales-detil', compact('salesDetils', 'search', 'per_page'));
    }

    public function printSalesDetil(Request $request)
    {
        $search = $request->input('search');

        $query = MotorUnit::select('motor_units.*')
            ->addSelect(['tgl_mutasi' => MutasiDetail::select('mutasis.tanggal')
                ->join('mutasis', 'mutasis.id', '=', 'mutasi_details.mutasi_id')
                ->whereColumn('mutasi_details.motor_unit_id', 'motor_units.id')
                ->orderBy('mutasis.tanggal', 'desc')
                ->limit(1)
            ])
            ->with(['lokasiPop', 'type', 'color'])
            ->where('status_unit', 'Tersedia')
            ->where('posisi_stok', 'POP');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_mesin', 'like', "%{$search}%")
                  ->orWhere('no_rangka', 'like', "%{$search}%")
                  ->orWhere('no_kunci', 'like', "%{$search}%")
                  ->orWhere('tahun_pembuatan', 'like', "%{$search}%")
                  ->orWhereHas('lokasiPop', function($s) use ($search) {
                      $s->where('nama_sales', 'like', "%{$search}%");
                  })
                  ->orWhereHas('type', function($t) use ($search) {
                      $t->where('nama_type', 'like', "%{$search}%")->orWhere('kode_tipe', 'like', "%{$search}%");
                  })
                  ->orWhereHas('color', function($c) use ($search) {
                      $c->where('warna', 'like', "%{$search}%");
                  });
            });
        }

        $salesDetils = $query->orderBy('lokasi_pop_id')->get();

        return view('laporan.stok.print-sales-detil', compact('salesDetils', 'search'));
    }

    public function gudangDetil(Request $request)
    {
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);

        $query = MotorUnit::with(['type', 'color'])
            ->where('status_unit', 'Tersedia')
            ->whereIn('posisi_stok', ['Gudang 1', 'Gudang 2']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_mesin', 'like', "%{$search}%")
                  ->orWhere('no_rangka', 'like', "%{$search}%")
                  ->orWhere('no_kunci', 'like', "%{$search}%")
                  ->orWhere('posisi_stok', 'like', "%{$search}%")
                  ->orWhereHas('type', function($t) use ($search) {
                      $t->where('nama_type', 'like', "%{$search}%")->orWhere('kode_tipe', 'like', "%{$search}%");
                  })
                  ->orWhereHas('color', function($c) use ($search) {
                      $c->where('warna', 'like', "%{$search}%");
                  });
            });
        }

        $units = $query->latest()->paginate($per_page)->withQueryString();

        return view('laporan.stok.gudang-detil', compact('units', 'search', 'per_page'));
    }

    public function printGudangDetil(Request $request)
    {
        $search = $request->input('search');

        $query = MotorUnit::with(['type', 'color'])
            ->where('status_unit', 'Tersedia')
            ->whereIn('posisi_stok', ['Gudang 1', 'Gudang 2']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_mesin', 'like', "%{$search}%")
                  ->orWhere('no_rangka', 'like', "%{$search}%")
                  ->orWhere('no_kunci', 'like', "%{$search}%")
                  ->orWhere('posisi_stok', 'like', "%{$search}%")
                  ->orWhereHas('type', function($t) use ($search) {
                      $t->where('nama_type', 'like', "%{$search}%")->orWhere('kode_tipe', 'like', "%{$search}%");
                  })
                  ->orWhereHas('color', function($c) use ($search) {
                      $c->where('warna', 'like', "%{$search}%");
                  });
            });
        }

        $units = $query->latest()->get();

        return view('laporan.stok.print-gudang-detil', compact('units', 'search'));
    }

    public function showroomDetil(Request $request)
    {
        $search = $request->input('search');
        $per_page = $request->input('per_page', 10);

        $query = MotorUnit::with(['type', 'color'])
            ->where('status_unit', 'Tersedia')
            ->where('posisi_stok', 'Showroom Pusat');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_mesin', 'like', "%{$search}%")
                  ->orWhere('no_rangka', 'like', "%{$search}%")
                  ->orWhere('no_kunci', 'like', "%{$search}%")
                  ->orWhereHas('type', function($t) use ($search) {
                      $t->where('nama_type', 'like', "%{$search}%")->orWhere('kode_tipe', 'like', "%{$search}%");
                  })
                  ->orWhereHas('color', function($c) use ($search) {
                      $c->where('warna', 'like', "%{$search}%");
                  });
            });
        }

        $units = $query->latest()->paginate($per_page)->withQueryString();

        return view('laporan.stok.showroom-detil', compact('units', 'search', 'per_page'));
    }

    public function printShowroomDetil(Request $request)
    {
        $search = $request->input('search');

        $query = MotorUnit::with(['type', 'color'])
            ->where('status_unit', 'Tersedia')
            ->where('posisi_stok', 'Showroom Pusat');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_mesin', 'like', "%{$search}%")
                  ->orWhere('no_rangka', 'like', "%{$search}%")
                  ->orWhere('no_kunci', 'like', "%{$search}%")
                  ->orWhereHas('type', function($t) use ($search) {
                      $t->where('nama_type', 'like', "%{$search}%")->orWhere('kode_tipe', 'like', "%{$search}%");
                  })
                  ->orWhereHas('color', function($c) use ($search) {
                      $c->where('warna', 'like', "%{$search}%");
                  });
            });
        }

        $units = $query->latest()->get();

        return view('laporan.stok.print-showroom-detil', compact('units', 'search'));
    }
}
