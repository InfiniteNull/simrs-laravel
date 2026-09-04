<?php

use App\Http\Controllers\BillingKasirController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaboratoriumController;
use App\Http\Controllers\PendaftaranPasienController;
use App\Http\Controllers\PksAsuransiController;
use App\Http\Controllers\RekamMedisController;
use App\Http\Controllers\ResepFarmasiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SIMRS Core Hospital Information System
|--------------------------------------------------------------------------
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Modul 1: Admisi & Pendaftaran Pasien
Route::prefix('pendaftaran')->name('pendaftaran.')->group(function () {
    Route::get('/', [PendaftaranPasienController::class, 'index'])->name('index');
    Route::get('/baru', [PendaftaranPasienController::class, 'create'])->name('create');
    Route::post('/', [PendaftaranPasienController::class, 'store'])->name('store');
    Route::get('/check-quota', [PendaftaranPasienController::class, 'checkQuota'])->name('checkQuota');
});

// Modul 2: Rekam Medis Elektronik (RME SOAP & ICD-10)
Route::prefix('rme')->name('rme.')->group(function () {
    Route::get('/', [RekamMedisController::class, 'index'])->name('index');
    Route::get('/asesmen/{pendaftaran}', [RekamMedisController::class, 'create'])->name('create');
    Route::post('/asesmen', [RekamMedisController::class, 'store'])->name('store');
});

// Modul 3: E-Order Laboratorium
Route::prefix('laboratorium')->name('laboratorium.')->group(function () {
    Route::get('/', [LaboratoriumController::class, 'index'])->name('index');
    Route::post('/', [LaboratoriumController::class, 'store'])->name('store');
    Route::put('/{laboratorium}/hasil', [LaboratoriumController::class, 'updateResult'])->name('updateResult');
});

// Modul 4: E-Prescribing & Farmasi
Route::prefix('farmasi')->name('farmasi.')->group(function () {
    Route::get('/', [ResepFarmasiController::class, 'index'])->name('index');
    Route::post('/resep', [ResepFarmasiController::class, 'store'])->name('store');
    Route::put('/resep/{resep}/status', [ResepFarmasiController::class, 'updateStatus'])->name('updateStatus');
});

// Modul 5: Billing & Kasir Rumah Sakit
Route::prefix('billing')->name('billing.')->group(function () {
    Route::get('/', [BillingKasirController::class, 'index'])->name('index');
    Route::post('/generate/{pendaftaran}', [BillingKasirController::class, 'generateInvoice'])->name('generateInvoice');
    Route::post('/bayar/{billing}', [BillingKasirController::class, 'pay'])->name('pay');
});

// Modul 6: Monitoring PKS Asuransi
Route::prefix('pks-asuransi')->name('pks.')->group(function () {
    Route::get('/', [PksAsuransiController::class, 'index'])->name('index');
    Route::post('/', [PksAsuransiController::class, 'store'])->name('store');
    Route::put('/{pks}/extend', [PksAsuransiController::class, 'extend'])->name('extend');
});
