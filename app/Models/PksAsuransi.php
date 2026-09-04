<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PksAsuransi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pks',
        'nama_mitra',
        'jenis_mitra', // bpjs, asuransi_swasta, perusahaan, yayasan
        'tanggal_mulai',
        'tanggal_berakhir',
        'penanggung_jawab',
        'kontak_person',
        'cakupan_layanan',
        'status_pks', // aktif, evaluasi, nonaktif
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
        'cakupan_layanan' => 'array',
    ];

    /**
     * Accessor untuk menghitung sisa hari masa berlaku PKS.
     */
    public function getSisaHariAttribute(): int
    {
        return (int) Carbon::now()->diffInDays(Carbon::parse($this->tanggal_berakhir), false);
    }
}
