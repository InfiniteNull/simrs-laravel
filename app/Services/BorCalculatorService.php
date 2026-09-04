<?php

namespace App\Services;

class BorCalculatorService
{
    /**
     * Menghitung Indikator Efisiensi Pelayanan Rawat Inap Rumah Sakit
     * Berdasarkan Pedoman Standar Depkes / Kemenkes RI.
     *
     * @param int $totalTempatTidur   (A) Jumlah tempat tidur siap pakai
     * @param int $tempatTidurTerisi  (O) Rata-rata tempat tidur terisi
     * @param int $jumlahHariPeriode  (t) Periode analisis (misal: 30 hari untuk 1 bulan)
     * @param int $pasienKeluar       (D) Total pasien keluar hidup & meninggal
     * @return array
     */
    public function calculateMonthlyIndicators(
        int $totalTempatTidur,
        int $tempatTidurTerisi,
        int $jumlahHariPeriode = 30,
        int $pasienKeluar = 420
    ): array {
        $totalTempatTidur = max(1, $totalTempatTidur);
        $pasienKeluar = max(1, $pasienKeluar);

        // 1. Hari Perawatan (HP) = Tempat Tidur Terisi * Hari Periode
        $hariPerawatan = $tempatTidurTerisi * $jumlahHariPeriode;

        // 2. BOR (Bed Occupancy Rate) = (Hari Perawatan / (Total TT * Hari Periode)) * 100%
        // Standar Ideal Kemenkes: 60% - 85%
        $bor = round(($hariPerawatan / ($totalTempatTidur * $jumlahHariPeriode)) * 100, 2);

        // 3. ALOS (Average Length of Stay) = Hari Perawatan / Pasien Keluar
        // Standar Ideal Kemenkes: 3 - 6 hari
        $alos = round($hariPerawatan / $pasienKeluar, 1);

        // 4. TOI (Turn Over Interval) = ((Total TT * Hari Periode) - Hari Perawatan) / Pasien Keluar
        // Standar Ideal Kemenkes: 1 - 3 hari
        $toi = round((($totalTempatTidur * $jumlahHariPeriode) - $hariPerawatan) / $pasienKeluar, 1);

        // 5. BTO (Bed Turn Over) = Pasien Keluar / Total TT
        // Standar Ideal Kemenkes: 40 - 50 kali per tahun (~3 - 5 kali per bulan)
        $bto = round($pasienKeluar / $totalTempatTidur, 1);

        // Evaluasi Kualitas BOR
        $statusBor = 'Ideal (Efisien)';
        $badgeClass = 'bg-emerald-100 text-emerald-800';
        if ($bor < 60) {
            $statusBor = 'Under-utilized (Rendah)';
            $badgeClass = 'bg-amber-100 text-amber-800';
        } elseif ($bor > 85) {
            $statusBor = 'Overcrowded (Kelebihan Beban)';
            $badgeClass = 'bg-rose-100 text-rose-800';
        }

        return [
            'total_tempat_tidur' => $totalTempatTidur,
            'tempat_tidur_terisi' => $tempatTidurTerisi,
            'tempat_tidur_kosong' => max(0, $totalTempatTidur - $tempatTidurTerisi),
            'periode_hari' => $jumlahHariPeriode,
            'hari_perawatan' => $hariPerawatan,
            'bor_percentage' => $bor,
            'alos_days' => $alos,
            'toi_days' => $toi,
            'bto_times' => $bto,
            'status_bor' => $statusBor,
            'badge_class' => $badgeClass,
        ];
    }
}
