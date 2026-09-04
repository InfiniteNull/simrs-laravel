<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_rkm_medis',
        'nik',
        'nama_lengkap',
        'jenis_kelamin',
        'tanggal_lahir',
        'no_telepon',
        'alamat',
        'golongan_darah',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }
}
