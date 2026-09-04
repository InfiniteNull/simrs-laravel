<?php

namespace App\Http\Controllers;

use App\Models\Laboratorium;
use App\Models\Pendaftaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaboratoriumController extends Controller
{
    /**
     * Tampilkan Daftar Permintaan Uji Laboratorium (E-Order Lab).
     */
    public function index(): View
    {
        $labOrders = Laboratorium::with(['pendaftaran.pasien', 'pendaftaran.dokter'])
            ->latest()
            ->paginate(15);

        return view('laboratorium.index', compact('labOrders'));
    }

    /**
     * Simpan Permintaan Uji Lab dari Poliklinik / IGD.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pendaftaran_id' => ['required', 'exists:pendaftarans,id'],
            'nama_pemeriksaan' => ['required', 'string', 'max:255'],
            'kode_loinc' => ['required', 'string', 'max:20'],
            'nilai_rujukan' => ['required', 'string', 'max:50'],
            'satuan' => ['required', 'string', 'max:20'],
        ]);

        $validated['status_pemeriksaan'] = 'requested';

        Laboratorium::create($validated);

        return redirect()->back()
            ->with('success', "Permintaan laboratorium {$validated['nama_pemeriksaan']} berhasil diteruskan.");
    }

    /**
     * Input Hasil Pemeriksaan Laboratorium oleh Petugas Analis Lab.
     */
    public function updateResult(Request $request, Laboratorium $laboratorium): JsonResponse
    {
        $validated = $request->validate([
            'nilai_hasil' => ['required', 'string', 'max:50'],
            'status_hasil' => ['required', 'in:normal,low,high,critical'],
            'catatan_lab' => ['nullable', 'string'],
        ]);

        $validated['status_pemeriksaan'] = 'completed';

        $laboratorium->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Hasil laboratorium berhasil divalidasi.',
            'data' => $laboratorium,
        ]);
    }
}
