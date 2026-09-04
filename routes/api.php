<?php

use App\Http\Controllers\Api\SimrsApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - SIMRS SatuSehat FHIR & Mobile App Integration
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::get('/dokters', [SimrsApiController::class, 'getDokters']);
    Route::get('/kamar/ketersediaan', [SimrsApiController::class, 'getKamarAvailability']);
    Route::get('/pasien/{no_rm}/histori', [SimrsApiController::class, 'getPasienHistory']);
    Route::get('/pks/monitoring', [SimrsApiController::class, 'getPksStatus']);
    Route::get('/satusehat/bundle/{pendaftaranId}', [SimrsApiController::class, 'getSatuSehatBundle']);
});
