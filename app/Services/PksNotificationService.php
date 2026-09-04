<?php

namespace App\Services;

use App\Models\PksAsuransi;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PksNotificationService
{
    /**
     * Dapatkan daftar PKS Asuransi yang akan kadaluarsa dalam rentang hari tertentu.
     */
    public function getExpiringPks(int $thresholdDays = 60): Collection
    {
        $today = Carbon::today();
        $targetDate = Carbon::today()->addDays($thresholdDays);

        return PksAsuransi::whereBetween('tanggal_berakhir', [$today, $targetDate])
            ->where('status_pks', 'aktif')
            ->orderBy('tanggal_berakhir', 'asc')
            ->get();
    }

    /**
     * Tentukan Kategori Status Berdasarkan Sisa Hari Masa Berlaku.
     */
    public function resolveStatusCategory(int $sisaHari): string
    {
        if ($sisaHari < 0) {
            return 'expired';
        }

        if ($sisaHari <= 60) {
            return 'warning';
        }

        return 'active';
    }
}
