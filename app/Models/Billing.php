<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Billing extends Model
{
    use HasFactory;

    protected $table = 'billings';

    protected $fillable = [
        'nomor_invoice',
        'pendaftaran_id',
        'biaya_konsultasi_dokter',
        'biaya_tindakan_medis',
        'biaya_obat_farmasi',
        'biaya_laboratorium',
        'biaya_kamar_rawat',
        'total_tagihan',
        'potongan_asuransi_bpjs',
        'sisa_bayar_pasien',
        'metode_pembayaran', // bpjs, asuransi_swasta, tunai, qris, kartu_debit
        'status_pembayaran', // lunas, pending, klaim_terkirim
        'waktu_pembayaran',
    ];

    protected $casts = [
        'biaya_konsultasi_dokter' => 'decimal:2',
        'biaya_tindakan_medis' => 'decimal:2',
        'biaya_obat_farmasi' => 'decimal:2',
        'biaya_laboratorium' => 'decimal:2',
        'biaya_kamar_rawat' => 'decimal:2',
        'total_tagihan' => 'decimal:2',
        'potongan_asuransi_bpjs' => 'decimal:2',
        'sisa_bayar_pasien' => 'decimal:2',
        'waktu_pembayaran' => 'datetime',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }
}
