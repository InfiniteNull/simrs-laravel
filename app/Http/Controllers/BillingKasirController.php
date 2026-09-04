<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Pendaftaran;
use App\Services\BillingCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BillingKasirController extends Controller
{
    public function __construct(
        protected BillingCalculatorService $billingService
    ) {}

    /**
     * Tampilkan Daftar Tagihan & Kasir Pembayaran Rumah Sakit.
     */
    public function index(): View
    {
        $billings = Billing::with(['pendaftaran.pasien'])
            ->latest()
            ->paginate(15);

        return view('billing.index', compact('billings'));
    }

    /**
     * Generate Invoice & Rekonsiliasi Kasir.
     */
    public function generateInvoice(Pendaftaran $pendaftaran): RedirectResponse
    {
        $summary = $this->billingService->calculateTotalBilling($pendaftaran);

        $invoice = Billing::updateOrCreate(
            ['pendaftaran_id' => $pendaftaran->id],
            [
                'nomor_invoice' => 'INV/' . date('Ymd') . '/' . strtoupper(Str::random(6)),
                'biaya_konsultasi_dokter' => $summary['biaya_konsultasi_dokter'],
                'biaya_tindakan_medis' => $summary['biaya_tindakan_medis'],
                'biaya_obat_farmasi' => $summary['biaya_obat_farmasi'],
                'biaya_laboratorium' => $summary['biaya_laboratorium'],
                'biaya_kamar_rawat' => $summary['biaya_kamar_rawat'],
                'total_tagihan' => $summary['total_tagihan'],
                'potongan_asuransi_bpjs' => $summary['potongan_penjamin'],
                'sisa_bayar_pasien' => $summary['sisa_bayar_pasien'],
                'metode_pembayaran' => $pendaftaran->metode_pembayaran,
                'status_pembayaran' => $summary['status_lunas'] ? 'lunas' : 'pending',
                'waktu_pembayaran' => $summary['status_lunas'] ? now() : null,
            ]
        );

        return redirect()->route('billing.show', $invoice->id)
            ->with('success', "Invoice tagihan {$invoice->nomor_invoice} berhasil diterbitkan.");
    }

    /**
     * Pembayaran Kasir (Tunai / QRIS / Kartu Debit).
     */
    public function pay(Request $request, Billing $billing): JsonResponse
    {
        $request->validate([
            'metode_pembayaran' => ['required', 'in:tunai,qris,kartu_debit,bpjs,asuransi_swasta'],
        ]);

        $billing->update([
            'status_pembayaran' => 'lunas',
            'metode_pembayaran' => $request->metode_pembayaran,
            'waktu_pembayaran' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pembayaran tagihan berhasil diverifikasi.',
            'data' => $billing,
        ]);
    }
}
