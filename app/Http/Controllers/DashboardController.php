<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\PksAsuransi;
use App\Services\BorCalculatorService;
use App\Services\PksNotificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected BorCalculatorService $borService,
        protected PksNotificationService $pksService
    ) {}

    /**
     * Tampilkan Ringkasan Dashboard SIMRS, Indikator BOR & Layanan Rumah Sakit.
     */
    public function index(): View
    {
        $totalPasien = Pasien::count();
        $kunjunganHariIni = Pendaftaran::whereDate('tanggal_kunjungan', today())->count();
        $rawatInapAktif = Pendaftaran::where('jenis_pelayanan', 'rawat_inap')
            ->where('status_antrean', 'sedang_dilayani')
            ->count();

        // Hitung Indikator Efisiensi Tempat Tidur (Standar Kemenkes RI)
        $totalTempatTidur = Kamar::sum('total_tempat_tidur') ?: 100;
        $tempatTidurTerisi = Kamar::sum('tempat_tidur_terisi') ?: 72;
        $borMetrics = $this->borService->calculateMonthlyIndicators($totalTempatTidur, $tempatTidurTerisi, 30);

        // Status PKS Asuransi & Rekomendasi Perpanjangan
        $pksExpiringSoon = $this->pksService->getExpiringPks(60);
        $totalPksAktif = PksAsuransi::where('status_pks', 'aktif')->count();

        return view('dashboard', compact(
            'totalPasien',
            'kunjunganHariIni',
            'rawatInapAktif',
            'borMetrics',
            'pksExpiringSoon',
            'totalPksAktif'
        ));
    }
}
