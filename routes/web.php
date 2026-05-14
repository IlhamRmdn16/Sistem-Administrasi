<?php

use App\Http\Controllers\Master\MotorTypeController;
use App\Http\Controllers\Transaction\MotorUnitController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/master/motor-type/create');
});

Route::get('/master/motor-type/create', [MotorTypeController::class, 'create'])->name('motor-type.create');
Route::post('/master/motor-type', [MotorTypeController::class, 'store'])->name('motor-type.store');

Route::get('/transaction/motor-unit/create', [MotorUnitController::class, 'create'])->name('motor-unit.create');
Route::post('/transaction/motor-unit', [MotorUnitController::class, 'store'])->name('motor-unit.store');
