<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Kamar;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\PksAsuransi;
use App\Services\BorCalculatorService;
use App\Services\SatuSehatFhirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SimrsApiController extends Controller
{
    /**
     * Endpoint API: Daftar Dokter & Jadwal Praktik Poliklinik.
     */
    public function getDokters(): JsonResponse
    {
        $dokters = Dokter::where('status_aktif', true)->get();
        return response()->json([
            'status' => 'success',
            'timestamp' => now()->toISOString(),
            'total' => $dokters->count(),
            'data' => $dokters,
        ]);
    }

    /**
     * Endpoint API: Informasi Ketersediaan Bed Kamar Rawat Inap & Nilai BOR.
     */
    public function getKamarAvailability(BorCalculatorService $borService): JsonResponse
    {
        $kamars = Kamar::all();
        $totalTT = $kamars->sum('total_tempat_tidur');
        $terisiTT = $kamars->sum('tempat_tidur_terisi');
        $metrics = $borService->calculateMonthlyIndicators($totalTT, $terisiTT, 30);

        return response()->json([
            'status' => 'success',
            'hospital_metrics' => $metrics,
            'rooms_detail' => $kamars,
        ]);
    }

    /**
     * Endpoint API: Histori Rekam Medis Pasien Berdasarkan No RM.
     */
    public function getPasienHistory(string $noRm): JsonResponse
    {
        $pasien = Pasien::where('no_rkm_medis', $noRm)
            ->with(['pendaftarans.rekamMedis', 'pendaftarans.dokter'])
            ->first();

        if (!$pasien) {
            return response()->json([
                'status' => 'error',
                'message' => "Pasien dengan No Rekam Medis {$noRm} tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $pasien,
        ]);
    }

    /**
     * Endpoint API: Status Monitoring PKS Asuransi.
     */
    public function getPksStatus(): JsonResponse
    {
        $pksList = PksAsuransi::where('status_pks', 'aktif')->get();
        return response()->json([
            'status' => 'success',
            'data' => $pksList,
        ]);
    }

    /**
     * Endpoint API: Bridging Kemenkes SatuSehat FHIR Bundle (Permenkes 24/2022).
     */
    public function getSatuSehatBundle(int $pendaftaranId, SatuSehatFhirService $fhirService): JsonResponse
    {
        $pendaftaran = Pendaftaran::with(['pasien', 'dokter', 'rekamMedis'])->find($pendaftaranId);

        if (!$pendaftaran) {
            return response()->json(['status' => 'error', 'message' => 'Pendaftaran tidak ditemukan'], 404);
        }

        $encounter = $fhirService->createEncounterResource($pendaftaran);
        $condition = $pendaftaran->rekamMedis ? $fhirService->createConditionResource($pendaftaran->rekamMedis) : null;
        $vitalObservation = $pendaftaran->rekamMedis ? $fhirService->createObservationVitalResource($pendaftaran->rekamMedis) : null;

        return response()->json([
            'resourceType' => 'Bundle',
            'type' => 'transaction',
            'timestamp' => now()->toIso8601String(),
            'entry' => array_filter([
                ['resource' => $encounter],
                $condition ? ['resource' => $condition] : null,
                $vitalObservation ? ['resource' => $vitalObservation] : null,
            ])
        ]);
    }
}
