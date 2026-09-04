<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_antrean',
        'kode_booking',
        'pasien_id',
        'dokter_id',
        'tanggal_kunjungan',
        'jenis_pelayanan', // rawat_jalan, rawat_inap, igd
        'metode_pembayaran', // bpjs, asuransi_swasta, umum
        'status_antrean', // menunggu, sedang_dilayani, selesai, batal
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
    ];

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function rekamMedis(): HasOne
    {
        return $this->hasOne(RekamMedis::class);
    }

    public function reseps(): HasMany
    {
        return $this->hasMany(Resep::class);
    }

    public function laboratoriums(): HasMany
    {
        return $this->hasMany(Laboratorium::class);
    }

    public function billing(): HasOne
    {
        return $this->hasOne(Billing::class);
    }
}
