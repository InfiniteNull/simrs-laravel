<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekamMedis extends Model
{
    use HasFactory;

    protected $table = 'rekam_medis';

    protected $fillable = [
        'pendaftaran_id',
        // S: Subjektif
        'keluhan_utama',
        'riwayat_penyakit_sekarang',
        'riwayat_alergi',
        // O: Objektif
        'tekanan_darah',
        'nadi',
        'suhu',
        'pernapasan',
        'berat_badan',
        'tinggi_badan',
        'spo2',
        // A: Asesmen
        'kode_icd10',
        'nama_diagnosis',
        'tipe_diagnosis',
        // P: Plan
        'tindakan_medis',
        'resep_obat',
        'edukasi_pasien',
        'rencana_kontrol_lanjutan',
    ];

    protected $casts = [
        'rencana_kontrol_lanjutan' => 'date',
        'nadi' => 'integer',
        'suhu' => 'decimal:1',
        'pernapasan' => 'integer',
        'berat_badan' => 'decimal:1',
        'tinggi_badan' => 'decimal:1',
        'spo2' => 'integer',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }
}
