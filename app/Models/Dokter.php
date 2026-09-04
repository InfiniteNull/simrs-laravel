<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dokter extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip_dokter',
        'nama_dokter',
        'spesialisasi',
        'nomor_sip',
        'jadwal_hari',
        'jam_mulai',
        'jam_selesai',
        'kuota_harian',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'kuota_harian' => 'integer',
    ];

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }
}
