<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\Kamar;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\PksAsuransi;
use App\Models\RekamMedis;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SimrsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Staff Users
        User::firstOrCreate(
            ['email' => 'admin@simrs.local'],
            [
                'name' => 'Administrator SIMRS',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
            ]
        );

        // 2. Seed Dokter Spesialis
        $dokters = [
            [
                'nip_dokter' => 'DR-19850112-001',
                'nama_dokter' => 'dr. Hendra Wijaya, Sp.PD',
                'spesialisasi' => 'Spesialis Penyakit Dalam',
                'nomor_sip' => 'SIP.446/001/DS/2024',
                'jadwal_hari' => 'Senin - Kamis',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '13:00:00',
                'kuota_harian' => 30,
            ],
            [
                'nip_dokter' => 'DR-19880422-002',
                'nama_dokter' => 'dr. Siti Rahmawati, Sp.A',
                'spesialisasi' => 'Spesialis Anak',
                'nomor_sip' => 'SIP.446/002/DS/2024',
                'jadwal_hari' => 'Senin - Sabtu',
                'jam_mulai' => '09:00:00',
                'jam_selesai' => '14:00:00',
                'kuota_harian' => 35,
            ],
            [
                'nip_dokter' => 'DR-19791105-003',
                'nama_dokter' => 'dr. Budi Santoso, Sp.B',
                'spesialisasi' => 'Spesialis Bedah Umum',
                'nomor_sip' => 'SIP.446/003/DS/2024',
                'jadwal_hari' => 'Selasa & Kamis',
                'jam_mulai' => '10:00:00',
                'jam_selesai' => '15:00:00',
                'kuota_harian' => 20,
            ],
            [
                'nip_dokter' => 'DR-19820719-004',
                'nama_dokter' => 'dr. Maya Kusuma, Sp.JP',
                'spesialisasi' => 'Spesialis Jantung & Pembuluh Darah',
                'nomor_sip' => 'SIP.446/004/DS/2024',
                'jadwal_hari' => 'Senin, Rabu, Jumat',
                'jam_mulai' => '08:30:00',
                'jam_selesai' => '12:30:00',
                'kuota_harian' => 25,
            ]
        ];

        foreach ($dokters as $d) {
            Dokter::firstOrCreate(['nip_dokter' => $d['nip_dokter']], $d);
        }

        // 3. Seed Kamar & Bed Rawat Inap
        $kamars = [
            ['kode_kamar' => 'VVIP-01', 'nama_bangsal' => 'Paviliun Garuda', 'kelas_kamar' => 'vvip', 'total_tempat_tidur' => 5, 'tempat_tidur_terisi' => 4, 'tarif_per_hari' => 1500000],
            ['kode_kamar' => 'VIP-01', 'nama_bangsal' => 'Paviliun Cenderawasih', 'kelas_kamar' => 'vip', 'total_tempat_tidur' => 15, 'tempat_tidur_terisi' => 12, 'tarif_per_hari' => 950000],
            ['kode_kamar' => 'K1-01', 'nama_bangsal' => 'Bangsal Melati (Kelas 1)', 'kelas_kamar' => 'kelas_1', 'total_tempat_tidur' => 25, 'tempat_tidur_terisi' => 20, 'tarif_per_hari' => 500000],
            ['kode_kamar' => 'K2-01', 'nama_bangsal' => 'Bangsal Mawar (Kelas 2)', 'kelas_kamar' => 'kelas_2', 'total_tempat_tidur' => 30, 'tempat_tidur_terisi' => 23, 'tarif_per_hari' => 300000],
            ['kode_kamar' => 'K3-01', 'nama_bangsal' => 'Bangsal Anggrek (Kelas 3)', 'kelas_kamar' => 'kelas_3', 'total_tempat_tidur' => 40, 'tempat_tidur_terisi' => 31, 'tarif_per_hari' => 150000],
            ['kode_kamar' => 'ICU-01', 'nama_bangsal' => 'Unit Perawatan Intensif (ICU)', 'kelas_kamar' => 'icu', 'total_tempat_tidur' => 10, 'tempat_tidur_terisi' => 7, 'tarif_per_hari' => 2000000],
        ];

        foreach ($kamars as $k) {
            Kamar::firstOrCreate(['kode_kamar' => $k['kode_kamar']], $k);
        }

        // 4. Seed PKS Asuransi
        $pksData = [
            [
                'nomor_pks' => 'PKS/BPJS-KTR/2025/001',
                'nama_mitra' => 'BPJS Kesehatan Kantor Cabang Utama',
                'jenis_mitra' => 'bpjs',
                'tanggal_mulai' => '2025-01-01',
                'tanggal_berakhir' => '2026-12-31',
                'penanggung_jawab' => 'dr. Farida Hanum, M.Kes',
                'kontak_person' => '0811-9988-7766',
                'cakupan_layanan' => json_encode(['Rawat Inap', 'Rawat Jalan', 'IGD 24 Jam', 'Ambulans', 'Farmasi Kronis']),
                'status_pks' => 'aktif',
            ],
            [
                'nomor_pks' => 'PKS/ALLIANZ-MED/2025/089',
                'nama_mitra' => 'PT Asuransi Allianz Life Indonesia',
                'jenis_mitra' => 'asuransi_swasta',
                'tanggal_mulai' => '2025-04-15',
                'tanggal_berakhir' => '2026-10-15', // Warning: Segera berakhir (~40 hari)
                'penanggung_jawab' => 'Kevin Tan, AAIJ',
                'kontak_person' => '0812-3456-7890',
                'cakupan_layanan' => json_encode(['Cashless Rawat Inap', 'Executive MCU', 'Operasi Bedah']),
                'status_pks' => 'aktif',
            ],
            [
                'nomor_pks' => 'PKS/PRU-HOSP/2024/045',
                'nama_mitra' => 'PT Prudential Life Assurance',
                'jenis_mitra' => 'asuransi_swasta',
                'tanggal_mulai' => '2024-09-01',
                'tanggal_berakhir' => '2026-09-01', // Expired baru saja
                'penanggung_jawab' => 'Clara Novita, S.E.',
                'kontak_person' => '0813-8877-6655',
                'cakupan_layanan' => json_encode(['Rawat Inap VIP', 'Rawat Jalan Lanjutan']),
                'status_pks' => 'evaluasi',
            ],
            [
                'nomor_pks' => 'PKS/SINARMAS-MSIG/2025/112',
                'nama_mitra' => 'PT Asuransi Sinarmas',
                'jenis_mitra' => 'asuransi_swasta',
                'tanggal_mulai' => '2025-06-01',
                'tanggal_berakhir' => '2027-06-01',
                'penanggung_jawab' => 'Ahmad Fauzi',
                'kontak_person' => '0852-1122-3344',
                'cakupan_layanan' => json_encode(['Semua Layanan Rawat & Penunjang Medis']),
                'status_pks' => 'aktif',
            ]
        ];

        foreach ($pksData as $pks) {
            PksAsuransi::firstOrCreate(['nomor_pks' => $pks['nomor_pks']], $pks);
        }

        // 5. Seed Pasien Contoh & Pendaftaran
        $pasien1 = Pasien::firstOrCreate(
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

        $dokterHendra = Dokter::where('nip_dokter', 'DR-19850112-001')->first();

        $pendaftaran = Pendaftaran::firstOrCreate(
            ['kode_booking' => 'BK-MED202609A'],
            [
                'nomor_antrean' => 'P-001',
                'pasien_id' => $pasien1->id,
                'dokter_id' => $dokterHendra->id,
                'tanggal_kunjungan' => today(),
                'jenis_pelayanan' => 'rawat_jalan',
                'metode_pembayaran' => 'bpjs',
                'status_antrean' => 'selesai',
            ]
        );

        // 6. Seed Rekam Medis (RME SOAP)
        RekamMedis::firstOrCreate(
            ['pendaftaran_id' => $pendaftaran->id],
            [
                'keluhan_utama' => 'Kepala pusing berputar sejak 3 hari yang lalu, tengkuk terasa kaku dan tegang.',
                'riwayat_penyakit_sekarang' => 'Pasien memiliki riwayat hipertensi tidak terkontrol, sering makan makanan bergaram tinggi.',
                'riwayat_alergi' => 'Tidak ada riwayat alergi obat penisilin.',
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
                'tindakan_medis' => 'Pemeriksaan EKG dasar, Konseling Diet Rendah Garam (DASH Diet).',
                'resep_obat' => "1. Amlodipine 10 mg tab No. XXX (1x1 pagi)\n2. Candesartan 8 mg tab No. XXX (1x1 malam)\n3. Paracetamol 500 mg tab No. X (3x1 prn pusing)",
                'edukasi_pasien' => 'Kurangi konsumsi natrium/garam, olahraga teratur 30 menit per hari, kontrol tensi berkala.',
                'rencana_kontrol_lanjutan' => today()->addDays(30),
            ]
        );
    }
}
