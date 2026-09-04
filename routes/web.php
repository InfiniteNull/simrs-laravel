<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PendaftaranPasienController;
use App\Http\Controllers\PksAsuransiController;
use App\Http\Controllers\RekamMedisController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SIMRS Hospital Information System
|--------------------------------------------------------------------------
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Modul 1 & 3: Pendaftaran Online & Rencana Kontrol Pasien
Route::prefix('pendaftaran')->name('pendaftaran.')->group(function () {
    Route::get('/', [PendaftaranPasienController::class, 'index'])->name('index');
    Route::get('/baru', [PendaftaranPasienController::class, 'create'])->name('create');
    Route::post('/', [PendaftaranPasienController::class, 'store'])->name('store');
    Route::get('/check-quota', [PendaftaranPasienController::class, 'checkQuota'])->name('checkQuota');
});

// Modul 5 & 6: Rekam Medis Elektronik (RME SOAP & ICD-10)
Route::prefix('rme')->name('rme.')->group(function () {
    Route::get('/', [RekamMedisController::class, 'index'])->name('index');
    Route::get('/asesmen/{pendaftaran}', [RekamMedisController::class, 'create'])->name('create');
    Route::post('/asesmen', [RekamMedisController::class, 'store'])->name('store');
});

// Modul 4: Monitoring PKS Asuransi
Route::prefix('pks-asuransi')->name('pks.')->group(function () {
    Route::get('/', [PksAsuransiController::class, 'index'])->name('index');
    Route::post('/', [PksAsuransiController::class, 'store'])->name('store');
    Route::put('/{pks}/extend', [PksAsuransiController::class, 'extend'])->name('extend');
});
