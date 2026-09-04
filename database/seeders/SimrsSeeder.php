<?php

namespace Database\Seeders;

use App\Models\Billing;
use App\Models\Dokter;
use App\Models\Kamar;
use App\Models\Laboratorium;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\PksAsuransi;
use App\Models\RekamMedis;
use App\Models\Resep;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SimrsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Staff Users
        User::firstOrCreate(
            ['email' => 'admin@simrs.local'],
            [
                'name' => 'Administrator SIMRS',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
            ]
        );

        // 2. Master Dokter Spesialis
        $dokters = [
            [
                'nip_dokter' => '198501122010011002',
                'nama_dokter' => 'dr. Hendra Wijaya, Sp.PD',
                'spesialisasi' => 'Penyakit Dalam',
                'nomor_sip' => '503/SIP/012/DPMPTSP/2023',
                'jadwal_hari' => 'Senin - Kamis',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '13:00:00',
                'kuota_harian' => 30,
            ],
            [
                'nip_dokter' => '198804222014022001',
                'nama_dokter' => 'dr. Siti Rahmawati, Sp.A',
                'spesialisasi' => 'Kesehatan Anak',
                'nomor_sip' => '503/SIP/045/DPMPTSP/2023',
                'jadwal_hari' => 'Senin - Sabtu',
                'jam_mulai' => '09:00:00',
                'jam_selesai' => '14:00:00',
                'kuota_harian' => 35,
            ],
            [
                'nip_dokter' => '197911052006041003',
                'nama_dokter' => 'dr. Budi Santoso, Sp.B',
                'spesialisasi' => 'Bedah Umum',
                'nomor_sip' => '503/SIP/088/DPMPTSP/2022',
                'jadwal_hari' => 'Selasa & Kamis',
                'jam_mulai' => '10:00:00',
                'jam_selesai' => '15:00:00',
                'kuota_harian' => 20,
            ],
            [
                'nip_dokter' => '198207192009032004',
                'nama_dokter' => 'dr. Maya Kusuma, Sp.JP',
                'spesialisasi' => 'Jantung & Pembuluh Darah',
                'nomor_sip' => '503/SIP/103/DPMPTSP/2024',
                'jadwal_hari' => 'Senin, Rabu, Jumat',
                'jam_mulai' => '08:30:00',
                'jam_selesai' => '12:30:00',
                'kuota_harian' => 25,
            ]
        ];

        foreach ($dokters as $d) {
            Dokter::firstOrCreate(['nip_dokter' => $d['nip_dokter']], $d);
        }

        // 3. Kamar & Bed Rawat Inap
        $kamars = [
            ['kode_kamar' => 'VVIP-01', 'nama_bangsal' => 'Paviliun Garuda', 'kelas_kamar' => 'vvip', 'total_tempat_tidur' => 6, 'tempat_tidur_terisi' => 5, 'tarif_per_hari' => 1500000],
            ['kode_kamar' => 'VIP-01', 'nama_bangsal' => 'Paviliun Cenderawasih', 'kelas_kamar' => 'vip', 'total_tempat_tidur' => 16, 'tempat_tidur_terisi' => 13, 'tarif_per_hari' => 950000],
            ['kode_kamar' => 'K1-MELATI', 'nama_bangsal' => 'Bangsal Melati', 'kelas_kamar' => 'kelas_1', 'total_tempat_tidur' => 28, 'tempat_tidur_terisi' => 22, 'tarif_per_hari' => 500000],
            ['kode_kamar' => 'K2-MAWAR', 'nama_bangsal' => 'Bangsal Mawar', 'kelas_kamar' => 'kelas_2', 'total_tempat_tidur' => 36, 'tempat_tidur_terisi' => 29, 'tarif_per_hari' => 300000],
            ['kode_kamar' => 'K3-ANGGREK', 'nama_bangsal' => 'Bangsal Anggrek', 'kelas_kamar' => 'kelas_3', 'total_tempat_tidur' => 50, 'tempat_tidur_terisi' => 42, 'tarif_per_hari' => 150000],
            ['kode_kamar' => 'ICU-CENTRAL', 'nama_bangsal' => 'Intensive Care Unit (ICU)', 'kelas_kamar' => 'icu', 'total_tempat_tidur' => 12, 'tempat_tidur_terisi' => 9, 'tarif_per_hari' => 2000000],
        ];

        foreach ($kamars as $k) {
            Kamar::firstOrCreate(['kode_kamar' => $k['kode_kamar']], $k);
        }

        // 4. PKS Asuransi
        $pksData = [
            [
                'nomor_pks' => '001/PKS-RS/BPJS-KTR/2025',
                'nama_mitra' => 'BPJS Kesehatan Kantor Cabang Utama',
                'jenis_mitra' => 'bpjs',
                'tanggal_mulai' => '2025-01-01',
                'tanggal_berakhir' => '2026-12-31',
                'penanggung_jawab' => 'dr. Farida Hanum, M.Kes',
                'kontak_person' => '0811-9988-7766',
                'cakupan_layanan' => json_encode(['Rawat Jalan', 'Rawat Inap', 'IGD', 'Farmasi PRB']),
                'status_pks' => 'aktif',
            ],
            [
                'nomor_pks' => '089/PKS/ALLIANZ-MED/2025',
                'nama_mitra' => 'PT Asuransi Allianz Life Indonesia',
                'jenis_mitra' => 'asuransi_swasta',
                'tanggal_mulai' => '2025-04-15',
                'tanggal_berakhir' => '2026-10-15',
                'penanggung_jawab' => 'Kevin Tan, AAIJ',
                'kontak_person' => '0812-3456-7890',
                'cakupan_layanan' => json_encode(['Rawat Inap Cashless', 'VIP Upgrade', 'Operasi']),
                'status_pks' => 'aktif',
            ],
            [
                'nomor_pks' => '045/PKS-CORP/PRU-HOSP/2024',
                'nama_mitra' => 'PT Prudential Life Assurance',
                'jenis_mitra' => 'asuransi_swasta',
                'tanggal_mulai' => '2024-09-01',
                'tanggal_berakhir' => '2026-09-01',
                'penanggung_jawab' => 'Clara Novita, S.E.',
                'kontak_person' => '0813-8877-6655',
                'cakupan_layanan' => json_encode(['Rawat Inap VIP', 'Rawat Jalan Lanjutan']),
                'status_pks' => 'evaluasi',
            ]
        ];

        foreach ($pksData as $pks) {
            PksAsuransi::firstOrCreate(['nomor_pks' => $pks['nomor_pks']], $pks);
        }

        // 5. Pasien & Kunjungan
        $pasien = Pasien::firstOrCreate(
            ['nik' => '1271012304950001'],
            [
                'no_rkm_medis' => 'RM-202609-0001',
                'nama_lengkap' => 'Bambang Sudarmono',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '1995-04-23',
                'no_telepon' => '081265438899',
                'alamat' => 'Jl. Gatot Subroto No. 45, Medan',
                'golongan_darah' => 'O',
            ]
        );

        $dokter = Dokter::first();

        $pendaftaran = Pendaftaran::firstOrCreate(
            ['kode_booking' => 'BK-20260905-01'],
            [
                'nomor_antrean' => 'P-001',
                'pasien_id' => $pasien->id,
                'dokter_id' => $dokter->id,
                'tanggal_kunjungan' => today(),
                'jenis_pelayanan' => 'rawat_jalan',
                'metode_pembayaran' => 'bpjs',
                'status_antrean' => 'selesai',
            ]
        );

        // 6. Rekam Medis
        RekamMedis::firstOrCreate(
            ['pendaftaran_id' => $pendaftaran->id],
            [
                'keluhan_utama' => 'Keluhan pusing berputar sejak 3 hari, tengkuk kaku setelah lembur kerja.',
                'riwayat_penyakit_sekarang' => 'Riwayat hipertensi 2 tahun tidak rutin kontrol.',
                'riwayat_alergi' => 'Disangkal.',
                'tekanan_darah' => '150/95 mmHg',
                'nadi' => 84,
                'suhu' => 36.6,
                'pernapasan' => 20,
                'berat_badan' => 74.0,
                'tinggi_badan' => 168.0,
                'spo2' => 99,
                'kode_icd10' => 'I10',
                'nama_diagnosis' => 'Essential (primary) hypertension',
                'tipe_diagnosis' => 'primer',
                'tindakan_medis' => 'Pemeriksaan EKG 12 Lead dasar.',
                'resep_obat' => "R/ Amlodipine 10 mg tab No. XXX | S 1 dd tab 1 (pagi pc)\nR/ Candesartan 8 mg tab No. XXX | S 1 dd tab 1 (malam pc)",
                'edukasi_pasien' => 'Diet rendah garam natrium < 2000 mg/hari.',
                'rencana_kontrol_lanjutan' => today()->addDays(30),
            ]
        );

        // 7. E-Order Laboratorium
        Laboratorium::firstOrCreate(
            ['pendaftaran_id' => $pendaftaran->id, 'nama_pemeriksaan' => 'Darah Lengkap (Hematologi Rutin)'],
            [
                'kode_loinc' => '58410-2',
                'nilai_hasil' => 'Hb: 14.8 g/dL, Leukosit: 7.200 /uL, Trombosit: 265.000 /uL',
                'nilai_rujukan' => 'Hb: 13.5-17.5 g/dL',
                'satuan' => 'g/dL',
                'status_hasil' => 'normal',
                'catatan_lab' => 'Profil sel darah dalam batas normal.',
                'status_pemeriksaan' => 'completed',
            ]
        );

        // 8. E-Resep Farmasi
        Resep::firstOrCreate(
            ['pendaftaran_id' => $pendaftaran->id, 'nama_obat' => 'Amlodipine 10 mg'],
            [
                'bentuk_sediaan' => 'Tablet',
                'jumlah' => 30,
                'aturan_pakai_latin' => 'S 1 dd tab 1 (pagi pc)',
                'harga_satuan' => 2500.00,
                'total_harga' => 75000.00,
                'status_telaah' => 'lolos',
                'status_dispensing' => 'diserahkan',
            ]
        );

        // 9. Billing Kasir
        Billing::firstOrCreate(
            ['pendaftaran_id' => $pendaftaran->id],
            [
                'nomor_invoice' => 'INV/20260905/0001',
                'biaya_konsultasi_dokter' => 150000.00,
                'biaya_tindakan_medis' => 75000.00,
                'biaya_obat_farmasi' => 75000.00,
                'biaya_laboratorium' => 120000.00,
                'biaya_kamar_rawat' => 0.00,
                'total_tagihan' => 420000.00,
                'potongan_asuransi_bpjs' => 420000.00,
                'sisa_bayar_pasien' => 0.00,
                'metode_pembayaran' => 'bpjs',
                'status_pembayaran' => 'lunas',
                'waktu_pembayaran' => now(),
            ]
        );
    }
}
