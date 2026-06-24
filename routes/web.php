<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanAccuController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\LaporanStokController;
use App\Http\Controllers\Master\BiayaAdministrasiController;
use App\Http\Controllers\Master\LeasingController;
use App\Http\Controllers\Master\MotorTypeController;
use App\Http\Controllers\Master\PdiManController;
use App\Http\Controllers\Master\RekeningController;
use App\Http\Controllers\Master\SalesController;
use App\Http\Controllers\MutasiStokController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Transaction\CetakBlankoSamsatController;
use App\Http\Controllers\Transaction\KontrolHargaPenjualanController;
use App\Http\Controllers\Transaction\KuitansiKonsumenController;
use App\Http\Controllers\Transaction\KuitansiLainLainController;
use App\Http\Controllers\Transaction\KwitansiProgresifController;
use App\Http\Controllers\Transaction\MotorUnitController;
use App\Http\Controllers\Transaction\PenagihanLeasingController;
use App\Http\Controllers\Transaction\PencairanLeasingController;
use App\Http\Controllers\Transaction\PengajuanStnkController;
use App\Http\Controllers\Transaction\PenyerahanBpkbLeasingController;
use App\Http\Controllers\Transaction\PenyerahanStnkBpkbController;
use App\Http\Controllers\Transaction\RealisasiPajakController;
use App\Http\Controllers\Transaction\SamsatController;
use App\Http\Controllers\Transaction\SpkController;
use App\Http\Controllers\Transaction\SuratJalanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return redirect()->route('motor-type.index');
    })->name('dashboard');

    Route::resource('roles', RoleController::class)->except(['show']);
    Route::resource('users', UserController::class)->except(['show']);

    Route::prefix('master')->group(function () {
        Route::get('/motor-type', [MotorTypeController::class, 'index'])->name('motor-type.index');
        Route::post('/motor-type', [MotorTypeController::class, 'store'])->name('motor-type.store');
        Route::put('/motor-type/{id}', [MotorTypeController::class, 'update'])->name('motor-type.update');
        Route::delete('/motor-type/{id}', [MotorTypeController::class, 'destroy'])->name('motor-type.destroy');

        Route::resource('/sales', SalesController::class)->except(['create', 'show', 'edit']);
        Route::resource('/leasing', LeasingController::class)->except(['create', 'show', 'edit']);

        Route::get('/rekening', [RekeningController::class, 'index'])->name('rekening.index');
        Route::post('/rekening', [RekeningController::class, 'store'])->name('rekening.store');
        Route::put('/rekening/{id}', [RekeningController::class, 'update'])->name('rekening.update');
        Route::delete('/rekening/{id}', [RekeningController::class, 'destroy'])->name('rekening.destroy');

        Route::get('/pdi-man', [PdiManController::class, 'index'])->name('pdiman.index');
        Route::post('/pdi-man', [PdiManController::class, 'store'])->name('pdiman.store');
        Route::put('/pdi-man/{id}', [PdiManController::class, 'update'])->name('pdiman.update');
        Route::delete('/pdi-man/{id}', [PdiManController::class, 'destroy'])->name('pdiman.destroy');

        Route::resource('/biaya-administrasi', BiayaAdministrasiController::class);
    });

    Route::prefix('transaction')->group(function () {
        Route::get('/motor-unit', [MotorUnitController::class, 'index'])->name('motor-unit.index');
        Route::post('/motor-unit', [MotorUnitController::class, 'store'])->name('motor-unit.store');
        Route::put('/motor-unit/{id}', [MotorUnitController::class, 'update'])->name('motor-unit.update');
        Route::delete('/motor-unit/{id}', [MotorUnitController::class, 'destroy'])->name('motor-unit.destroy');
        Route::get('/motor-unit/{id}/print', [MotorUnitController::class, 'print'])->name('motor-unit.print');

        Route::get('/spk', [SpkController::class, 'index'])->name('spk.index');
        Route::post('/spk', [SpkController::class, 'store'])->name('spk.store');
        Route::put('/spk/{id}', [SpkController::class, 'update'])->name('spk.update');
        Route::delete('/spk/{id}', [SpkController::class, 'destroy'])->name('spk.destroy');
        Route::get('/spk/{id}/print', [SpkController::class, 'print'])->name('spk.print');

        Route::get('/transaction/mutasi/api/available-units', [MutasiStokController::class, 'getAvailableUnits'])->name('mutasi.api.units');
        Route::get('/transaction/mutasi/detail/{id}', [MutasiStokController::class, 'show'])->name('mutasi.show');
        Route::get('/transaction/mutasi/{jenis}', [MutasiStokController::class, 'index'])->name('mutasi.index');
        Route::get('/transaction/mutasi/{jenis}/create', [MutasiStokController::class, 'create'])->name('mutasi.create');
        Route::post('/transaction/mutasi/{jenis}', [MutasiStokController::class, 'store'])->name('mutasi.store');
        Route::delete('/transaction/mutasi/{id}', [MutasiStokController::class, 'destroy'])->name('mutasi.destroy');

        Route::get('/suratjalan', [SuratJalanController::class, 'index'])->name('suratjalan.index');
        Route::post('/suratjalan', [SuratJalanController::class, 'store'])->name('suratjalan.store');
        Route::put('/suratjalan/{id}', [SuratJalanController::class, 'update'])->name('suratjalan.update');
        Route::delete('/suratjalan/{id}', [SuratJalanController::class, 'destroy'])->name('suratjalan.destroy');
        Route::get('/suratjalan/{id}/print', [SuratJalanController::class, 'print'])->name('suratjalan.print');

        Route::prefix('kontrol-harga')->name('kontrol-harga.')->group(function () {
            Route::get('/', [KontrolHargaPenjualanController::class, 'index'])->name('index');
            Route::post('/store', [KontrolHargaPenjualanController::class, 'store'])->name('store');
            Route::get('/{spk_id}/print-options', [KontrolHargaPenjualanController::class, 'printOptions'])->name('print-options');
            Route::get('/{spk_id}/print/otr', [KontrolHargaPenjualanController::class, 'printOtr'])->name('print.otr');
            Route::get('/{spk_id}/print/dp-po', [KontrolHargaPenjualanController::class, 'printDpPo'])->name('print.dp-po');
            Route::get('/{spk_id}/print/otr-dp-po', [KontrolHargaPenjualanController::class, 'printOtrDpPo'])->name('print.otr-dp-po');
            Route::get('/{spk_id}/print/surat-pernyataan-bpkb', [KontrolHargaPenjualanController::class, 'printSuratPernyataanBpkb'])->name('print.surat-pernyataan-bpkb');
            Route::get('/{spk_id}/print/kw1', [KontrolHargaPenjualanController::class, 'printKw1'])->name('print.kw1');
            Route::get('/{spk_id}/print/kw2', [KontrolHargaPenjualanController::class, 'printKw2'])->name('print.kw2');
            Route::get('/{spk_id}/print/setoran-spk', [KontrolHargaPenjualanController::class, 'printSetoranSpk'])->name('print.setoran-spk');
        });

        Route::prefix('kuitansi-konsumen')->name('kuitansi-konsumen.')->group(function () {
            Route::get('/', [KuitansiKonsumenController::class, 'index'])->name('index');
            Route::post('/store', [KuitansiKonsumenController::class, 'store'])->name('store');
            Route::get('/print/{id}', [KuitansiKonsumenController::class, 'print'])->name('print');
            Route::get('/api/search', [KuitansiKonsumenController::class, 'searchApi'])->name('search-api');
        });

        Route::prefix('kuitansi-lain')->name('kuitansi-lain.')->group(function () {
            Route::get('/', [KuitansiLainLainController::class, 'index'])->name('index');
            Route::post('/store', [KuitansiLainLainController::class, 'store'])->name('store');
            Route::put('/{id}', [KuitansiLainLainController::class, 'update'])->name('update');
            Route::delete('/{id}', [KuitansiLainLainController::class, 'destroy'])->name('destroy');
            Route::get('/print/{id}', [KuitansiLainLainController::class, 'print'])->name('print');
        });

        Route::prefix('penagihan-leasing')->name('penagihan-leasing.')->group(function () {
            Route::get('/', [PenagihanLeasingController::class, 'index'])->name('index');
            Route::get('/api/pending/{leasing_id}', [PenagihanLeasingController::class, 'getPending'])->name('api.pending');
            Route::post('/store', [PenagihanLeasingController::class, 'store'])->name('store');
            Route::delete('/{id}', [PenagihanLeasingController::class, 'destroy'])->name('destroy');
            Route::get('/print/{id}', [PenagihanLeasingController::class, 'print'])->name('print');
        });

        Route::prefix('pencairan-leasing')->name('pencairan-leasing.')->group(function () {
            Route::get('/', [PencairanLeasingController::class, 'index'])->name('index');
            Route::get('/api/pending/{leasing_id}', [PencairanLeasingController::class, 'getPending'])->name('api.pending');
            Route::post('/store', [PencairanLeasingController::class, 'store'])->name('store');
            Route::delete('/{id}', [PencairanLeasingController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('penyerahan-bpkb-leasing')->name('penyerahan-bpkb-leasing.')->group(function () {
            Route::get('/', [PenyerahanBpkbLeasingController::class, 'index'])->name('index');
            Route::get('/{leasing_id}', [PenyerahanBpkbLeasingController::class, 'show'])->name('show');
            Route::post('/', [PenyerahanBpkbLeasingController::class, 'store'])->name('store');
            Route::delete('/{id}', [PenyerahanBpkbLeasingController::class, 'destroy'])->name('destroy');
            Route::get('/print/{id}', [PenyerahanBpkbLeasingController::class, 'print'])->name('print');
        });

    });

    Route::resource('samsat', SamsatController::class)->only(['index', 'update']);

    Route::get('pengajuan-stnk/riwayat', [PengajuanStnkController::class, 'riwayat'])->name('pengajuan-stnk.riwayat');
    Route::resource('pengajuan-stnk', PengajuanStnkController::class)->except(['create', 'show']);
    Route::get('pengajuan-stnk/{id}/print', [PengajuanStnkController::class, 'print'])->name('pengajuan-stnk.print');

    Route::prefix('penyerahan-stnk-bpkb')->name('penyerahan-stnk-bpkb.')->group(function () {
        Route::get('/', [PenyerahanStnkBpkbController::class, 'index'])->name('index');
        Route::put('/{id}', [PenyerahanStnkBpkbController::class, 'update'])->name('update');
        Route::get('/{id}/print', [PenyerahanStnkBpkbController::class, 'print'])->name('print');
    });

    Route::prefix('realisasi-pajak')->name('realisasi-pajak.')->group(function () {
        Route::get('/', [RealisasiPajakController::class, 'index'])->name('index');
        Route::get('/print', [RealisasiPajakController::class, 'print'])->name('print');
    });

    Route::prefix('kwitansi-progresif')->name('kwitansi-progresif.')->group(function () {
        Route::get('/', [KwitansiProgresifController::class, 'index'])->name('index');
        Route::post('/store', [KwitansiProgresifController::class, 'store'])->name('store');
        Route::get('/{id}/print', [KwitansiProgresifController::class, 'print'])->name('print');
    });

    Route::prefix('cetak-blanko-samsat')->name('cetak-blanko-samsat.')->group(function () {
        Route::get('/', [CetakBlankoSamsatController::class, 'index'])->name('index');
        Route::get('/{id}/print', [CetakBlankoSamsatController::class, 'print'])->name('print');
    });

    Route::get('/laporan/stok/global', [LaporanStokController::class, 'global'])->name('laporan.stok.global');
    Route::get('/laporan/stok/global/print', [LaporanStokController::class, 'printGlobal'])->name('laporan.stok.global.print');
    Route::get('/laporan/stok/warna', [LaporanStokController::class, 'warna'])->name('laporan.stok.warna');
    Route::get('/laporan/stok/warna/print', [LaporanStokController::class, 'printWarna'])->name('laporan.stok.warna.print');
    Route::get('/laporan/stok/detil', [LaporanStokController::class, 'detil'])->name('laporan.stok.detil');
    Route::get('/laporan/stok/detil/print', [LaporanStokController::class, 'printDetil'])->name('laporan.stok.detil.print');
    Route::get('/laporan/stok/sales-global', [LaporanStokController::class, 'salesGlobal'])->name('laporan.stok.sales-global');
    Route::get('/laporan/stok/sales-global/print', [LaporanStokController::class, 'printSalesGlobal'])->name('laporan.stok.sales-global.print');
    Route::get('/laporan/stok/sales-detil', [LaporanStokController::class, 'salesDetil'])->name('laporan.stok.sales-detil');
    Route::get('/laporan/stok/sales-detil/print', [LaporanStokController::class, 'printSalesDetil'])->name('laporan.stok.sales-detil.print');
    Route::get('/laporan/stok/gudang-detil', [LaporanStokController::class, 'gudangDetil'])->name('laporan.stok.gudang-detil');
Route::get('/laporan/stok/gudang-detil/print', [LaporanStokController::class, 'printGudangDetil'])->name('laporan.stok.gudang-detil.print');

Route::get('/laporan/stok/showroom-detil', [LaporanStokController::class, 'showroomDetil'])->name('laporan.stok.showroom-detil');
Route::get('/laporan/stok/showroom-detil/print', [LaporanStokController::class, 'printShowroomDetil'])->name('laporan.stok.showroom-detil.print');

Route::get('/laporan/penjualan/global-unit', [LaporanPenjualanController::class, 'globalUnit'])->name('laporan.penjualan.global-unit');
Route::get('/laporan/penjualan/global-unit/print', [LaporanPenjualanController::class, 'printGlobalUnit'])->name('laporan.penjualan.global-unit.print');

Route::get('/laporan/penjualan/terperinci', [LaporanPenjualanController::class, 'terperinci'])->name('laporan.penjualan.terperinci');
Route::get('/laporan/penjualan/terperinci/print', [LaporanPenjualanController::class, 'printTerperinci'])->name('laporan.penjualan.terperinci.print');

Route::get('/laporan/penjualan/subsidi-main-dealer', [LaporanPenjualanController::class, 'subsidiMainDealer'])->name('laporan.penjualan.subsidi-main-dealer');
Route::get('/laporan/penjualan/subsidi-main-dealer/print', [LaporanPenjualanController::class, 'printSubsidiMainDealer'])->name('laporan.penjualan.subsidi-main-dealer.print');

Route::get('/laporan/penjualan/sales-pop-global', [LaporanPenjualanController::class, 'salesPopGlobal'])->name('laporan.penjualan.sales-pop-global');
Route::get('/laporan/penjualan/sales-pop-global/print', [LaporanPenjualanController::class, 'printSalesPopGlobal'])->name('laporan.penjualan.sales-pop-global.print');
Route::get('/laporan/penjualan/sales-pop-terperinci', [LaporanPenjualanController::class, 'salesPopTerperinci'])->name('laporan.penjualan.sales-pop-terperinci');
Route::get('/laporan/penjualan/sales-pop-terperinci/print', [LaporanPenjualanController::class, 'printSalesPopTerperinci'])->name('laporan.penjualan.sales-pop-terperinci.print');
Route::get('/laporan/penjualan/leasing-global', [LaporanPenjualanController::class, 'leasingGlobal'])->name('laporan.penjualan.leasing-global');
Route::get('/laporan/penjualan/leasing-global/print', [LaporanPenjualanController::class, 'printLeasingGlobal'])->name('laporan.penjualan.leasing-global.print');
Route::get('/laporan/penjualan/leasing-terperinci', [LaporanPenjualanController::class, 'leasingTerperinci'])->name('laporan.penjualan.leasing-terperinci');
Route::get('/laporan/penjualan/leasing-terperinci/print', [LaporanPenjualanController::class, 'printLeasingTerperinci'])->name('laporan.penjualan.leasing-terperinci.print');
Route::get('/laporan/penjualan/pdi-man-global', [LaporanPenjualanController::class, 'pdiManGlobal'])->name('laporan.penjualan.pdi-man-global');
Route::get('/laporan/penjualan/pdi-man-global/print', [LaporanPenjualanController::class, 'printPdiManGlobal'])->name('laporan.penjualan.pdi-man-global.print');
Route::get('/laporan/penjualan/pdi-man-terperinci', [LaporanPenjualanController::class, 'pdiManTerperinci'])->name('laporan.penjualan.pdi-man-terperinci');
Route::get('/laporan/penjualan/pdi-man-terperinci/print', [LaporanPenjualanController::class, 'printPdiManTerperinci'])->name('laporan.penjualan.pdi-man-terperinci.print');
Route::get('/laporan/kontrol-accu/mutasi', [LaporanAccuController::class, 'mutasiPenjualan'])->name('laporan.accu.mutasi');
Route::get('/laporan/kontrol-accu/mutasi/print', [LaporanAccuController::class, 'printMutasiPenjualan'])->name('laporan.accu.mutasi.print');
Route::get('/laporan/kontrol-accu/stok', [LaporanAccuController::class, 'stok'])->name('laporan.accu.stok');
Route::get('/laporan/kontrol-accu/stok/print', [LaporanAccuController::class, 'printStok'])->name('laporan.accu.stok.print');

});
