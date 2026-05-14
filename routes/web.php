<?php

use App\Http\Controllers\Master\MotorTypeController;
use App\Http\Controllers\Transaction\MotorUnitController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/master/motor-type');
});

Route::get('/master/motor-type', [MotorTypeController::class, 'index'])->name('motor-type.index');
Route::post('/master/motor-type', [MotorTypeController::class, 'store'])->name('motor-type.store');
Route::delete('/master/motor-type/{id}', [MotorTypeController::class, 'destroy'])->name('motor-type.destroy');

Route::get('/transaction/motor-unit', [MotorUnitController::class, 'index'])->name('motor-unit.index');
Route::post('/transaction/motor-unit', [MotorUnitController::class, 'store'])->name('motor-unit.store');
Route::put('/transaction/motor-unit/{id}', [MotorUnitController::class, 'update'])->name('motor-unit.update');
Route::delete('/transaction/motor-unit/{id}', [MotorUnitController::class, 'destroy'])->name('motor-unit.destroy');
