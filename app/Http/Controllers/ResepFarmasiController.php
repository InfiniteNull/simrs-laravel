<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Resep;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResepFarmasiController extends Controller
{
    /**
     * Tampilkan Antrean Resep di Instalasi Farmasi.
     */
    public function index(): View
    {
        $reseps = Resep::with(['pendaftaran.pasien', 'pendaftaran.dokter'])
            ->latest()
            ->paginate(20);

        return view('farmasi.index', compact('reseps'));
    }

    /**
     * Input Resep Elektronik (E-Prescribing) oleh Dokter Pemeriksa.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pendaftaran_id' => ['required', 'exists:pendaftarans,id'],
            'nama_obat' => ['required', 'string', 'max:255'],
            'bentuk_sediaan' => ['required', 'string', 'max:50'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'aturan_pakai_latin' => ['required', 'string', 'max:100'],
            'harga_satuan' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['total_harga'] = $validated['jumlah'] * $validated['harga_satuan'];
        $validated['status_telaah'] = 'lolos';
        $validated['status_dispensing'] = 'antre';

        Resep::create($validated);

        return redirect()->back()
            ->with('success', 'Resep obat berhasil diteruskan ke Instalasi Farmasi.');
    }

    /**
     * Dispensing & Penyerahan Obat kepada Pasien.
     */
    public function updateStatus(Request $request, Resep $resep): JsonResponse
    {
        $request->validate([
            'status_dispensing' => ['required', 'in:diracik,diserahkan'],
        ]);

        $resep->update([
            'status_dispensing' => $request->status_dispensing,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status dispensing obat berhasil diperbarui.',
            'data' => $resep,
        ]);
    }
}
