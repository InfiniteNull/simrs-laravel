<?php

namespace App\Http\Controllers;

use App\Models\PksAsuransi;
use App\Services\PksNotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PksAsuransiController extends Controller
{
    public function __construct(
        protected PksNotificationService $pksService
    ) {}

    /**
     * Tampilkan Dashboard Monitoring Masa Berlaku PKS Asuransi & Perusahaan.
     */
    public function index(): View
    {
        $allPks = PksAsuransi::orderBy('tanggal_berakhir', 'asc')->get();
        
        // Enrich data dengan status countdown & badge
        $pksList = $allPks->map(function ($pks) {
            $pks->sisa_hari = (int) Carbon::now()->diffInDays(Carbon::parse($pks->tanggal_berakhir), false);
            $pks->status_kategori = $this->pksService->resolveStatusCategory($pks->sisa_hari);
            return $pks;
        });

        $expiringCount = $pksList->where('status_kategori', 'warning')->count();
        $expiredCount = $pksList->where('status_kategori', 'expired')->count();
        $activeCount = $pksList->where('status_kategori', 'active')->count();

        return view('pks.index', compact('pksList', 'expiringCount', 'expiredCount', 'activeCount'));
    }

    /**
     * Tambah Kontrak Perjanjian Kerjasama (PKS) Baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_pks' => ['required', 'string', 'unique:pks_asuransis,nomor_pks'],
            'nama_mitra' => ['required', 'string', 'max:255'],
            'jenis_mitra' => ['required', 'in:bpjs,asuransi_swasta,perusahaan,yayasan'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_berakhir' => ['required', 'date', 'after:tanggal_mulai'],
            'penanggung_jawab' => ['required', 'string', 'max:255'],
            'kontak_person' => ['required', 'string', 'max:50'],
            'cakupan_layanan' => ['required', 'array'],
        ]);

        $validated['status_pks'] = 'aktif';
        $validated['cakupan_layanan'] = json_encode($validated['cakupan_layanan']);

        PksAsuransi::create($validated);

        return redirect()->route('pks.index')
            ->with('success', "PKS Kerjasama bersama {$validated['nama_mitra']} berhasil didaftarkan.");
    }

    /**
     * Perpanjang Kontrak / Addendum PKS.
     */
    public function extend(Request $request, PksAsuransi $pks): RedirectResponse
    {
        $request->validate([
            'tanggal_berakhir_baru' => ['required', 'date', 'after:' . $pks->tanggal_berakhir],
            'nomor_addendum' => ['required', 'string', 'max:255'],
        ]);

        $pks->update([
            'tanggal_berakhir' => $request->tanggal_berakhir_baru,
            'nomor_pks' => $request->nomor_addendum,
            'status_pks' => 'aktif',
        ]);

        return redirect()->route('pks.index')
            ->with('success', "Addendum perpanjangan PKS {$pks->nama_mitra} berhasil diperbarui.");
    }
}
