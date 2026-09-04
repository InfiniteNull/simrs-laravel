<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resep extends Model
{
    use HasFactory;

    protected $table = 'reseps';

    protected $fillable = [
        'pendaftaran_id',
        'nama_obat',
        'bentuk_sediaan', // tablet, sirup, kapsul, injeksi, salep
        'jumlah',
        'aturan_pakai_latin', // S 3 dd tab 1 pc
        'harga_satuan',
        'total_harga',
        'status_telaah', // lolos, ada_alergi, interaksi_obat
        'status_dispensing', // antre, diracik, diserahkan
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }
}
