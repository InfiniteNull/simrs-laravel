<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Kamar;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\PksAsuransi;
use App\Services\BorCalculatorService;
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
}
