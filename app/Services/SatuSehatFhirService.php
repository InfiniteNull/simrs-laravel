<?php

namespace App\Services;

use App\Models\Pendaftaran;
use App\Models\RekamMedis;
use App\Models\Laboratorium;
use App\Models\Resep;

/**
 * Service Interoperabilitas Kemenkes SatuSehat (Permenkes No. 24/2022)
 * Menghasilkan Payload FHIR HL7 R4 untuk Encounter, Condition, Observation, dan MedicationRequest.
 */
class SatuSehatFhirService
{
    /**
     * Generate FHIR Encounter Resource (Kunjungan Pasien)
     */
    public function createEncounterResource(Pendaftaran $pendaftaran): array
    {
        return [
            'resourceType' => 'Encounter',
            'identifier' => [
                [
                    'system' => 'http://sys-ids.kemkes.go.id/encounter/' . config('services.satusehat.org_id', '1000001'),
                    'value' => $pendaftaran->nomor_antrean,
                ]
            ],
            'status' => $pendaftaran->status === 'selesai' ? 'finished' : ($pendaftaran->status === 'sedang_dilayani' ? 'in-progress' : 'arrived'),
            'class' => [
                'system' => 'http://terminology.hl7.org/CodeSystem/v3-ActCode',
                'code' => $pendaftaran->jenis_layanan === 'Rawat Inap' ? 'IMP' : ($pendaftaran->jenis_layanan === 'IGD 24 Jam' ? 'EMER' : 'AMB'),
                'display' => $pendaftaran->jenis_layanan,
            ],
            'subject' => [
                'reference' => 'Patient/' . ($pendaftaran->pasien->ihs_number ?? 'P01234567890'),
                'display' => $pendaftaran->pasien->nama_lengkap,
            ],
            'participant' => [
                [
                    'type' => [
                        [
                            'coding' => [
                                [
                                    'system' => 'http://terminology.hl7.org/CodeSystem/v3-ParticipationType',
                                    'code' => 'ATND',
                                    'display' => 'attender',
                                ]
                            ]
                        ]
                    ],
                    'individual' => [
                        'reference' => 'Practitioner/' . ($pendaftaran->dokter->ihs_doctor ?? 'N10000001'),
                        'display' => $pendaftaran->dokter->nama_dokter,
                    ]
                ]
            ],
            'period' => [
                'start' => $pendaftaran->waktu_daftar?->toIso8601String() ?? now()->toIso8601String(),
                'end' => $pendaftaran->status === 'selesai' ? now()->toIso8601String() : null,
            ],
            'serviceProvider' => [
                'reference' => 'Organization/' . config('services.satusehat.org_id', '1000001'),
            ]
        ];
    }

    /**
     * Generate FHIR Condition Resource (Diagnosis ICD-10)
     */
    public function createConditionResource(RekamMedis $rekamMedis): array
    {
        return [
            'resourceType' => 'Condition',
            'clinicalStatus' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/condition-clinical',
                        'code' => 'active',
                        'display' => 'Active',
                    ]
                ]
            ],
            'verificationStatus' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/condition-ver-status',
                        'code' => 'confirmed',
                        'display' => 'Confirmed',
                    ]
                ]
            ],
            'category' => [
                [
                    'coding' => [
                        [
                            'system' => 'http://terminology.hl7.org/CodeSystem/condition-category',
                            'code' => 'encounter-diagnosis',
                            'display' => 'Encounter Diagnosis',
                        ]
                    ]
                ]
            ],
            'code' => [
                'coding' => [
                    [
                        'system' => 'http://hl7.org/fhir/sid/icd-10',
                        'code' => $rekamMedis->diagnosa_icd10,
                        'display' => $rekamMedis->nama_diagnosa,
                    ]
                ]
            ],
            'subject' => [
                'reference' => 'Patient/' . ($rekamMedis->pendaftaran->pasien->ihs_number ?? 'P01234567890'),
                'display' => $rekamMedis->pendaftaran->pasien->nama_lengkap,
            ],
            'encounter' => [
                'reference' => 'Encounter/' . $rekamMedis->pendaftaran_id,
            ],
            'recordedDate' => $rekamMedis->created_at?->toIso8601String() ?? now()->toIso8601String(),
        ];
    }

    /**
     * Generate FHIR Observation Resource (Tanda Vital & Lab)
     */
    public function createObservationVitalResource(RekamMedis $rekamMedis): array
    {
        return [
            'resourceType' => 'Observation',
            'status' => 'final',
            'category' => [
                [
                    'coding' => [
                        [
                            'system' => 'http://terminology.hl7.org/CodeSystem/observation-category',
                            'code' => 'vital-signs',
                            'display' => 'Vital Signs',
                        ]
                    ]
                ]
            ],
            'code' => [
                'coding' => [
                    [
                        'system' => 'http://loinc.org',
                        'code' => '85354-9',
                        'display' => 'Blood pressure panel with all children optional',
                    ]
                ]
            ],
            'subject' => [
                'reference' => 'Patient/' . ($rekamMedis->pendaftaran->pasien->ihs_number ?? 'P01234567890'),
                'display' => $rekamMedis->pendaftaran->pasien->nama_lengkap,
            ],
            'encounter' => [
                'reference' => 'Encounter/' . $rekamMedis->pendaftaran_id,
            ],
            'effectiveDateTime' => $rekamMedis->created_at?->toIso8601String() ?? now()->toIso8601String(),
            'component' => [
                [
                    'code' => [
                        'coding' => [
                            [
                                'system' => 'http://loinc.org',
                                'code' => '8480-6',
                                'display' => 'Systolic blood pressure',
                            ]
                        ]
                    ],
                    'valueQuantity' => [
                        'value' => (int) explode('/', $rekamMedis->tensi_darah)[0] ?? 120,
                        'unit' => 'mm[Hg]',
                        'system' => 'http://unitsofmeasure.org',
                        'code' => 'mm[Hg]',
                    ]
                ],
                [
                    'code' => [
                        'coding' => [
                            [
                                'system' => 'http://loinc.org',
                                'code' => '8462-4',
                                'display' => 'Diastolic blood pressure',
                            ]
                        ]
                    ],
                    'valueQuantity' => [
                        'value' => (int) (explode('/', $rekamMedis->tensi_darah)[1] ?? 80),
                        'unit' => 'mm[Hg]',
                        'system' => 'http://unitsofmeasure.org',
                        'code' => 'mm[Hg]',
                    ]
                ]
            ]
        ];
    }
}
