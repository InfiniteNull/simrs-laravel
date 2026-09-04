<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laboratorium extends Model
{
    use HasFactory;

    protected $table = 'laboratoriums';

    protected $fillable = [
        'pendaftaran_id',
        'nama_pemeriksaan',
        'kode_loinc',
        'nilai_hasil',
        'nilai_rujukan',
        'satuan',
        'status_hasil', // normal, low, high, critical
        'catatan_lab',
        'status_pemeriksaan', // requested, in_progress, completed
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }
}
