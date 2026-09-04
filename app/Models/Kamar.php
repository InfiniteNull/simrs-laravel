<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_kamar',
        'nama_bangsal',
        'kelas_kamar', // vvip, vip, kelas_1, kelas_2, kelas_3, icu, nicu
        'total_tempat_tidur',
        'tempat_tidur_terisi',
        'tarif_per_hari',
    ];

    protected $casts = [
        'total_tempat_tidur' => 'integer',
        'tempat_tidur_terisi' => 'integer',
        'tarif_per_hari' => 'decimal:2',
    ];

    /**
     * Hitung sisa tempat tidur kosong.
     */
    public function getTempatTidurKosongAttribute(): int
    {
        return max(0, $this->total_tempat_tidur - $this->tempat_tidur_terisi);
    }
}
