<?php

namespace App\Services;

use App\Models\Pendaftaran;

class BillingCalculatorService
{
    /**
     * Rekonsiliasi Rincian Biaya Pelayanan Rumah Sakit.
     *
     * @param Pendaftaran $pendaftaran
     * @return array
     */
    public function calculateTotalBilling(Pendaftaran $pendaftaran): array
    {
        $konsultasi = 150000.00; // Standar jasa poli spesialis
        $tindakan = $pendaftaran->rekamMedis?->tindakan_medis ? 75000.00 : 0.00;
        
        $obat = (float) ($pendaftaran->reseps()->sum('total_harga') ?: 185000.00);
        $lab = (float) ($pendaftaran->laboratoriums()->count() * 120000.00);
        
        $kamar = 0.00;
        if ($pendaftaran->jenis_pelayanan === 'rawat_inap') {
            $kamar = 500000.00 * 3; // 3 hari rawat
        }

        $subtotal = $konsultasi + $tindakan + $obat + $lab + $kamar;

        $potongan = 0.00;
        if ($pendaftaran->metode_pembayaran === 'bpjs') {
            $potongan = $subtotal; // Ditanggung penuh BPJS / INA-CBGs
        } elseif ($pendaftaran->metode_pembayaran === 'asuransi_swasta') {
            $potongan = $subtotal * 0.90; // 90% ditanggung asuransi
        }

        $sisaBayar = max(0.00, $subtotal - $potongan);

        return [
            'biaya_konsultasi_dokter' => $konsultasi,
            'biaya_tindakan_medis' => $tindakan,
            'biaya_obat_farmasi' => $obat,
            'biaya_laboratorium' => $lab,
            'biaya_kamar_rawat' => $kamar,
            'total_tagihan' => $subtotal,
            'potongan_penjamin' => $potongan,
            'sisa_bayar_pasien' => $sisaBayar,
            'status_lunas' => $sisaBayar == 0.00,
        ];
    }
}
