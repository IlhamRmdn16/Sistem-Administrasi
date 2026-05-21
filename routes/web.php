<?php

use App\Http\Controllers\Master\BiayaAdministrasiController;
use App\Http\Controllers\Master\LeasingController;
use App\Http\Controllers\Master\MotorTypeController;
use App\Http\Controllers\Master\PdiManController;
use App\Http\Controllers\Master\RekeningController;
use App\Http\Controllers\Master\SalesController;
use App\Http\Controllers\Transaction\MotorUnitController;
use App\Http\Controllers\Transaction\PengajuanStnkController;
use App\Http\Controllers\Transaction\PenyerahanStnkBpkbController;
use App\Http\Controllers\Transaction\RealisasiPajakController;
use App\Http\Controllers\Transaction\SamsatController;
use App\Http\Controllers\Transaction\SpkController;
use App\Http\Controllers\Transaction\SuratJalanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/master/motor-type');
});

Route::get('/master/motor-type', [MotorTypeController::class, 'index'])->name('motor-type.index');
Route::post('/master/motor-type', [MotorTypeController::class, 'store'])->name('motor-type.store');
Route::put('/master/motor-type/{id}', [MotorTypeController::class, 'update'])->name('motor-type.update');
Route::delete('/master/motor-type/{id}', [MotorTypeController::class, 'destroy'])->name('motor-type.destroy');

Route::get('/transaction/motor-unit', [MotorUnitController::class, 'index'])->name('motor-unit.index');
Route::post('/transaction/motor-unit', [MotorUnitController::class, 'store'])->name('motor-unit.store');
Route::put('/transaction/motor-unit/{id}', [MotorUnitController::class, 'update'])->name('motor-unit.update');
Route::delete('/transaction/motor-unit/{id}', [MotorUnitController::class, 'destroy'])->name('motor-unit.destroy');
Route::get('/transaction/motor-unit/{id}/print', [MotorUnitController::class, 'print'])->name('motor-unit.print');

// Master Sales
Route::resource('/master/sales', SalesController::class)->except(['create', 'show', 'edit']);

// Master Leasing
Route::resource('/master/leasing', LeasingController::class)->except(['create', 'show', 'edit']);

Route::get('/master/rekening', [RekeningController::class, 'index'])->name('rekening.index');
Route::post('/master/rekening', [RekeningController::class, 'store'])->name('rekening.store');
Route::put('/master/rekening/{id}', [RekeningController::class, 'update'])->name('rekening.update');
Route::delete('/master/rekening/{id}', [RekeningController::class, 'destroy'])->name('rekening.destroy');

Route::get('/transaction/spk', [SpkController::class, 'index'])->name('spk.index');
Route::post('/transaction/spk', [SpkController::class, 'store'])->name('spk.store');
Route::put('/transaction/spk/{id}', [SpkController::class, 'update'])->name('spk.update');
Route::delete('/transaction/spk/{id}', [SpkController::class, 'destroy'])->name('spk.destroy');
Route::get('/transaction/spk/{id}/print', [SpkController::class, 'print'])->name('spk.print');

Route::get('/master/pdi-man', [PdiManController::class, 'index'])->name('pdiman.index');
Route::post('/master/pdi-man', [PdiManController::class, 'store'])->name('pdiman.store');
Route::put('/master/pdi-man/{id}', [PdiManController::class, 'update'])->name('pdiman.update');
Route::delete('/master/pdi-man/{id}', [PdiManController::class, 'destroy'])->name('pdiman.destroy');

Route::get('/transaction/suratjalan', [SuratJalanController::class, 'index'])->name('suratjalan.index');
Route::post('/transaction/suratjalan', [SuratJalanController::class, 'store'])->name('suratjalan.store');
Route::put('/transaction/suratjalan/{id}', [SuratJalanController::class, 'update'])->name('suratjalan.update');
Route::delete('/transaction/suratjalan/{id}', [SuratJalanController::class, 'destroy'])->name('suratjalan.destroy');
Route::get('/transaction/suratjalan/{id}/print', [SuratJalanController::class, 'print'])->name('suratjalan.print');

Route::resource('biaya-administrasi', BiayaAdministrasiController::class);

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
