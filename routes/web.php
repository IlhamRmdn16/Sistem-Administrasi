<?php

use App\Http\Controllers\Master\LeasingController;
use App\Http\Controllers\Master\MotorTypeController;
use App\Http\Controllers\Master\RekeningController;
use App\Http\Controllers\Master\SalesController;
use App\Http\Controllers\Transaction\MotorUnitController;
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
