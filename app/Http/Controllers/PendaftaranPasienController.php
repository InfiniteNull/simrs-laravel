<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PendaftaranPasienController extends Controller
{
    /**
     * Tampilkan Daftar Pendaftaran & Antrean Pasien Hari Ini.
     */
    public function index(): View
    {
        $pendaftarans = Pendaftaran::with(['pasien', 'dokter'])
            ->whereDate('tanggal_kunjungan', today())
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('pendaftaran.index', compact('pendaftarans'));
    }

    /**
     * Form Pendaftaran Pasien Baru / Lama & Rencana Kontrol.
     */
    public function create(): View
    {
        $dokters = Dokter::where('status_aktif', true)->get();
        return view('pendaftaran.create', compact('dokters'));
    }

    /**
     * Simpan Data Pendaftaran Baru & Generate Nomor Antrean Otomatis.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'size:16'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tanggal_lahir' => ['required', 'date'],
            'no_telepon' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string'],
            'dokter_id' => ['required', 'exists:dokters,id'],
            'jenis_pelayanan' => ['required', 'in:rawat_jalan,rawat_inap,igd'],
            'metode_pembayaran' => ['required', 'in:bpjs,asuransi_swasta,umum'],
            'tanggal_kunjungan' => ['required', 'date', 'after_or_equal:today'],
        ]);

        // Cari atau buat pasien baru
        $pasien = Pasien::firstOrCreate(
            ['nik' => $validated['nik']],
            [
                'no_rkm_medis' => 'RM-' . date('Ym') . '-' . str_pad((string)(Pasien::count() + 1), 4, '0', STR_PAD_LEFT),
                'nama_lengkap' => $validated['nama_lengkap'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'no_telepon' => $validated['no_telepon'],
                'alamat' => $validated['alamat'],
            ]
        );

        // Generate Nomor Antrean Poli: Contoh A-001, B-002
        $dokter = Dokter::findOrFail($validated['dokter_id']);
        $antreanCount = Pendaftaran::where('dokter_id', $dokter->id)
            ->whereDate('tanggal_kunjungan', $validated['tanggal_kunjungan'])
            ->count() + 1;

        $kodePoli = strtoupper(substr($dokter->spesialisasi, 0, 1));
        $nomorAntrean = $kodePoli . '-' . str_pad((string)$antreanCount, 3, '0', STR_PAD_LEFT);

        $pendaftaran = Pendaftaran::create([
            'nomor_antrean' => $nomorAntrean,
            'kode_booking' => 'BK-' . strtoupper(Str::random(8)),
            'pasien_id' => $pasien->id,
            'dokter_id' => $dokter->id,
            'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
            'jenis_pelayanan' => $validated['jenis_pelayanan'],
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'status_antrean' => 'menunggu',
        ]);

        return redirect()->route('pendaftaran.show', $pendaftaran->id)
            ->with('success', "Pendaftaran Berhasil! Nomor Antrean: {$nomorAntrean}");
    }

    /**
     * Cek Kuota Dokter Berdasarkan Tanggal Kunjungan (AJAX Endpoint).
     */
    public function checkQuota(Request $request): JsonResponse
    {
        $request->validate([
            'dokter_id' => ['required', 'exists:dokters,id'],
            'tanggal_kunjungan' => ['required', 'date'],
        ]);

        $dokter = Dokter::findOrFail($request->dokter_id);
        $terpakai = Pendaftaran::where('dokter_id', $dokter->id)
            ->whereDate('tanggal_kunjungan', $request->tanggal_kunjungan)
            ->count();

        $sisaKuota = max(0, $dokter->kuota_harian - $terpakai);

        return response()->json([
            'dokter' => $dokter->nama_dokter,
            'spesialisasi' => $dokter->spesialisasi,
            'kuota_total' => $dokter->kuota_harian,
            'kuota_terpakai' => $terpakai,
            'kuota_tersisa' => $sisaKuota,
            'status_tersedia' => $sisaKuota > 0,
        ]);
    }
}
