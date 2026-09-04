<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\RekamMedis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RekamMedisController extends Controller
{
    /**
     * Tampilkan Riwayat Rekam Medis Pasien (RME).
     */
    public function index(Request $request): View
    {
        $query = RekamMedis::with(['pendaftaran.pasien', 'pendaftaran.dokter']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pendaftaran.pasien', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('no_rkm_medis', 'like', "%{$search}%");
            });
        }

        $rekamMedisList = $query->latest()->paginate(15);

        return view('rme.index', compact('rekamMedisList'));
    }

    /**
     * Form Pengisian Asesmen Medis SOAP & Diagnosis ICD-10.
     */
    public function create(Pendaftaran $pendaftaran): View
    {
        $pendaftaran->load(['pasien', 'dokter']);
        return view('rme.create', compact('pendaftaran'));
    }

    /**
     * Simpan Asesmen Rekam Medis Elektronik (SOAP).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pendaftaran_id' => ['required', 'exists:pendaftarans,id', 'unique:rekam_medis,pendaftaran_id'],
            
            // S: Subjektif
            'keluhan_utama' => ['required', 'string'],
            'riwayat_penyakit_sekarang' => ['nullable', 'string'],
            'riwayat_alergi' => ['nullable', 'string'],

            // O: Objektif (Tanda-Tanda Vital & Fisik)
            'tekanan_darah' => ['required', 'string', 'max:20'], // contoh: 120/80 mmHg
            'nadi' => ['required', 'integer'],                  // contoh: 80 x/mnt
            'suhu' => ['required', 'numeric'],                  // contoh: 36.8 C
            'pernapasan' => ['required', 'integer'],            // contoh: 20 x/mnt
            'berat_badan' => ['required', 'numeric'],           // contoh: 65.5 kg
            'tinggi_badan' => ['required', 'numeric'],          // contoh: 170 cm
            'spo2' => ['nullable', 'integer'],                  // contoh: 98 %

            // A: Asesmen (Diagnosis Klinis & ICD-10)
            'kode_icd10' => ['required', 'string', 'max:10'],
            'nama_diagnosis' => ['required', 'string', 'max:255'],
            'tipe_diagnosis' => ['required', 'in:primer,sekunder'],

            // P: Plan (Penatalaksanaan & Resep Terapi)
            'tindakan_medis' => ['nullable', 'string'],
            'resep_obat' => ['required', 'string'],
            'edukasi_pasien' => ['nullable', 'string'],
            'rencana_kontrol_lanjutan' => ['nullable', 'date', 'after:today'],
        ]);

        $rekamMedis = RekamMedis::create($validated);

        // Update status pendaftaran menjadi selesai
        Pendaftaran::where('id', $validated['pendaftaran_id'])
            ->update(['status_antrean' => 'selesai']);

        return redirect()->route('rme.show', $rekamMedis->id)
            ->with('success', 'Rekam Medis Elektronik (RME SOAP) berhasil disimpan ke sistem SatuSehat.');
    }
}
