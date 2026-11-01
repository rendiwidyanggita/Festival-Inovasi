<?php

namespace Database\Seeders;

use App\Enums\AssessmentStatus;
use App\Enums\AssessmentType;
use App\Enums\FestivalJuryStatus;
use App\Enums\ParticipationStatus;
use App\Enums\ScoringStatus;
use App\Enums\UserRole;
use App\Models\AssessmentCriterion;
use App\Models\AwardCategory;
use App\Models\Festival;
use App\Models\FestivalCategory;
use App\Models\FestivalJury;
use App\Models\Innovation;
use App\Models\InnovationAssessment;
use App\Models\InnovationAssessmentDetail;
use App\Models\Institution;
use App\Models\InstitutionAssessment;
use App\Models\InstitutionAssessmentDetail;
use App\Models\JurySubcategoryParticipation;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Data contoh komprehensif sesuai PRD untuk pengembangan & demonstrasi.
 * Jalankan eksplisit: php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $festival = Festival::where('year', FestivalSeeder::targetYear())->firstOrFail();

        $juries = $this->seedJuries($festival);
        $institutions = $this->seedInstitutions($festival);
        $innovations = $this->seedInnovations($festival, $institutions);
        $this->seedParticipationsAndAssessments($festival, $juries, $innovations, $institutions);
    }

    /**
     * @return array<int, User>
     */
    private function seedJuries(Festival $festival): array
    {
        $juryData = [
            [
                'email' => 'juri1@festivalinovasi.test',
                'name' => 'Prof. Dr. Ir. H. Ahmad Fauzi, M.Eng.',
            ],
            [
                'email' => 'juri2@festivalinovasi.test',
                'name' => 'Dr. Sri Wahyuni, M.Si.',
            ],
            [
                'email' => 'juri3@festivalinovasi.test',
                'name' => 'Ir. Bambang Pamungkas, M.M.',
            ],
            [
                'email' => 'juri4@festivalinovasi.test',
                'name' => 'Dewi Lestari, S.Kom., M.T.',
            ],
            [
                'email' => 'juri5@festivalinovasi.test',
                'name' => 'Rahmat Hidayat, S.IP., M.Pub.Pol.',
            ],
        ];

        $users = [];
        foreach ($juryData as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => 'password',
                    'role' => UserRole::Jury,
                    'is_active' => true,
                ]
            );

            FestivalJury::firstOrCreate(
                ['festival_id' => $festival->id, 'user_id' => $user->id],
                ['status' => FestivalJuryStatus::Active]
            );

            $users[] = $user;
        }

        return $users;
    }

    /**
     * @return array<string, Institution>
     */
    private function seedInstitutions(Festival $festival): array
    {
        $institutionsBySubcategory = [
            'LEMBAGA TERINOVATIF OPD' => [
                'Dinas Komunikasi dan Informatika',
                'Dinas Kesehatan',
                'Badan Perencanaan Pembangunan Daerah (Bappeda)',
                'Badan Pendapatan Daerah (Bapenda)',
                'Dinas Kependudukan dan Pencatatan Sipil',
                'Dinas Sosial',
            ],
            'LEMBAGA TERINOVATIF KELURAHAN' => [
                'Kelurahan Sukamaju',
                'Kelurahan Melati Indah',
                'Kelurahan Menteng Asri',
                'Kelurahan Kebon Jeruk',
            ],
            'LEMBAGA TERINOVATIF PUSKESMAS' => [
                'Puskesmas Mawar Sehat Sejahtera',
                'Puskesmas Melati Jaya',
                'Puskesmas Terpadu Mandiri',
            ],
            'LEMBAGA TERINOVATIF TK' => [
                'TK Negeri Pembina 1',
                'TK Aisyiyah Bustanul Athfal 02',
            ],
            'LEMBAGA TERINOVATIF SD' => [
                'SD Negeri Percontohan 01',
                'SD Negeri Harapan Bangsa 03',
                'SD Negeri Cendikia Mandiri',
            ],
            'LEMBAGA TERINOVATIF SMP' => [
                'SMP Negeri 1 Madani',
                'SMP Negeri 3 Unggulan',
                'SMP Negeri 5 Cemerlang',
            ],
        ];

        $institutions = [];

        foreach ($institutionsBySubcategory as $subcategoryName => $names) {
            $subcategory = AwardCategory::where('festival_id', $festival->id)
                ->where('name', $subcategoryName)
                ->first();

            if (! $subcategory) {
                continue;
            }

            foreach ($names as $name) {
                $inst = Institution::firstOrCreate(
                    [
                        'festival_id' => $festival->id,
                        'award_category_id' => $subcategory->id,
                        'name' => $name,
                    ]
                );
                $institutions[$name] = $inst;
            }
        }

        return $institutions;
    }

    /**
     * @param array<string, Institution> $institutions
     * @return array<int, Innovation>
     */
    private function seedInnovations(Festival $festival, array $institutions): array
    {
        $innovationsData = [
            'ASN TERINOVATIF DIGITAL OPD' => [
                [
                    'name' => 'Si-Cantik OPD (Integrasi Layanan & Tanggap Keluhan Warga)',
                    'innovator' => 'Dr. Budi Santoso, S.Kom., M.T.',
                    'institution' => 'Dinas Komunikasi dan Informatika',
                ],
                [
                    'name' => 'E-Disposisi Prima (Manajemen Penugasan Real-Time Eksekutif)',
                    'innovator' => 'Hendra Gunawan, S.T., M.Eng.',
                    'institution' => 'Badan Perencanaan Pembangunan Daerah (Bappeda)',
                ],
                [
                    'name' => 'Lapor Sehat (Tele-Konsultasi & Rujukan Cepat Puskesmas)',
                    'innovator' => 'dr. Farida Hanum, Sp.A.',
                    'institution' => 'Dinas Kesehatan',
                ],
                [
                    'name' => 'SIGAP Pajak: Layanan e-Billing Retribusi & PBB Terpadu',
                    'innovator' => 'Ahmad Reza, S.E., M.Ak.',
                    'institution' => 'Badan Pendapatan Daerah (Bapenda)',
                ],
            ],
            'ASN TERINOVATIF NON DIGITAL OPD' => [
                [
                    'name' => 'Grebek Adminduk Door-to-Door Bagi Disabilitas & Lansia',
                    'innovator' => 'Agus Setiawan, S.STP',
                    'institution' => 'Dinas Kependudukan dan Pencatatan Sipil',
                ],
                [
                    'name' => 'Rantang Kasih Sosial: Logistik Makanan Sehat Dhuafa',
                    'innovator' => 'Siti Aminah, S.Sos.',
                    'institution' => 'Dinas Sosial',
                ],
            ],
            'ASN TERINOVATIF DIGITAL UNIT' => [
                [
                    'name' => 'Antrean Online Mandiri & Notifikasi WhatsApp Puskesmas',
                    'innovator' => 'Dian Puspitasari, S.Farm., Apt.',
                    'institution' => 'Puskesmas Mawar Sehat Sejahtera',
                ],
                [
                    'name' => 'SIM-PBB Kelurahan: Cetak Tagihan Pajak Mandiri Warga',
                    'innovator' => 'Rian Pratama, S.AP',
                    'institution' => 'Kelurahan Sukamaju',
                ],
            ],
            'ASN TERINOVATIF NON DIGITAL UNIT' => [
                [
                    'name' => 'Kantin Kejujuran Terintegrasi Budidaya Sayur Hidroponik',
                    'innovator' => 'Joko Susilo, S.Pd.',
                    'institution' => 'SMP Negeri 1 Madani',
                ],
                [
                    'name' => 'Posyandu Ramah Anak & Taman Tumbuh Kembang Balita',
                    'innovator' => 'Ratna Dewi, A.Md.Keb.',
                    'institution' => 'Kelurahan Melati Indah',
                ],
            ],
            'ASN TERINOVATIF SEKOLAH TK' => [
                [
                    'name' => 'Kartu Emosi Bergambar Stimulasi Karakter Anak Usia Dini',
                    'innovator' => 'Ratna Sari, S.Pd.AUD',
                    'institution' => 'TK Negeri Pembina 1',
                ],
                [
                    'name' => 'Eksplorasi Loose Parts Berbasis Bahan Alami Lokal',
                    'innovator' => 'Anisa Wahyuni, S.Pd.',
                    'institution' => 'TK Aisyiyah Bustanul Athfal 02',
                ],
            ],
            'ASN TERINOVATIF SEKOLAH SD' => [
                [
                    'name' => 'Matematika Asik: Papan Berhitung Tradisional-Modern Interaktif',
                    'innovator' => 'Supriadi, S.Pd.SD',
                    'institution' => 'SD Negeri Percontohan 01',
                ],
                [
                    'name' => 'Pojok Literasi Digital & Bank Resensi Siswa Berbasis QR',
                    'innovator' => 'Eka Kurniawati, S.Pd.',
                    'institution' => 'SD Negeri Harapan Bangsa 03',
                ],
            ],
            'ASN TERINOVATIF SEKOLAH SMP' => [
                [
                    'name' => 'Laboratorium Virtual Eksperimen Fisika Sederhana Smartphone',
                    'innovator' => 'Muhammad Rizky, M.Pd.',
                    'institution' => 'SMP Negeri 1 Madani',
                ],
                [
                    'name' => 'Robot Pemilah Sampah Organik Otomatis Berbasis Sensor Siswa',
                    'innovator' => 'Ahmad Danial, S.Pd.',
                    'institution' => 'SMP Negeri 3 Unggulan',
                ],
            ],
        ];

        $innovations = [];

        foreach ($innovationsData as $subcategoryName => $items) {
            $subcategory = AwardCategory::where('festival_id', $festival->id)
                ->where('name', $subcategoryName)
                ->first();

            if (! $subcategory) {
                continue;
            }

            foreach ($items as $item) {
                $ownerInstitution = $institutions[$item['institution']] 
                    ?? Institution::where('festival_id', $festival->id)->first();

                if (! $ownerInstitution) {
                    continue;
                }

                $inno = Innovation::firstOrCreate(
                    [
                        'festival_id' => $festival->id,
                        'award_category_id' => $subcategory->id,
                        'name' => $item['name'],
                    ],
                    [
                        'institution_id' => $ownerInstitution->id,
                        'innovator_name' => $item['innovator'],
                    ]
                );

                $innovations[] = $inno;
            }
        }

        return $innovations;
    }

    /**
     * @param array<int, User> $juries
     * @param array<int, Innovation> $innovations
     * @param array<string, Institution> $institutions
     */
    private function seedParticipationsAndAssessments(
        Festival $festival,
        array $juries,
        array $innovations,
        array $institutions
    ): void {
        if (empty($juries)) {
            return;
        }

        // 1. Assign Juri 1, 2, 3 to 'ASN TERINOVATIF DIGITAL OPD'
        $digitalOpd = AwardCategory::where('festival_id', $festival->id)
            ->where('name', 'ASN TERINOVATIF DIGITAL OPD')
            ->first();

        // 2. Assign Juri 1, 2 to 'LEMBAGA TERINOVATIF OPD'
        $lembagaOpd = AwardCategory::where('festival_id', $festival->id)
            ->where('name', 'LEMBAGA TERINOVATIF OPD')
            ->first();

        // Assign all juries to some other categories in preparation status so they have choices
        $allSubcategories = AwardCategory::where('festival_id', $festival->id)->get();
        foreach ($juries as $jIndex => $jury) {
            $fj = FestivalJury::where('festival_id', $festival->id)
                ->where('user_id', $jury->id)
                ->first();

            if (! $fj) {
                continue;
            }

            // Each jury picks 2-3 categories
            $assignedCats = $allSubcategories->slice(($jIndex * 2) % $allSubcategories->count(), 3);
            foreach ($assignedCats as $cat) {
                JurySubcategoryParticipation::firstOrCreate(
                    [
                        'festival_id' => $festival->id,
                        'festival_jury_id' => $fj->id,
                        'award_category_id' => $cat->id,
                    ],
                    ['status' => ParticipationStatus::Selected]
                );
            }
        }

        // Now setup 'ASN TERINOVATIF DIGITAL OPD' as OPEN with active scoring
        if ($digitalOpd) {
            $digitalOpd->update([
                'scoring_status' => ScoringStatus::Open,
                'opened_at' => now()->subDays(2),
            ]);

            $criteria = AssessmentCriterion::where('festival_id', $festival->id)
                ->where('assessment_type', AssessmentType::Innovation)
                ->get();

            $categoryInnovations = Innovation::where('award_category_id', $digitalOpd->id)->get();

            // Give Juri 1 & Juri 2 participation in digitalOpd
            foreach (array_slice($juries, 0, 3) as $jIndex => $jury) {
                $fj = FestivalJury::where('festival_id', $festival->id)->where('user_id', $jury->id)->first();
                if (! $fj) {
                    continue;
                }

                $part = JurySubcategoryParticipation::updateOrCreate(
                    [
                        'festival_id' => $festival->id,
                        'festival_jury_id' => $fj->id,
                        'award_category_id' => $digitalOpd->id,
                    ],
                    ['status' => ParticipationStatus::InProgress]
                );

                // Juri 1: Submit all (scores 8-10)
                // Juri 2: Submit all (scores 7-9)
                // Juri 3: Draft first 2 (scores 8-9)
                foreach ($categoryInnovations as $iIndex => $innovation) {
                    if ($jIndex === 2 && $iIndex >= 2) {
                        continue; // Juri 3 only drafts first 2
                    }

                    $status = ($jIndex === 2) 
                        ? AssessmentStatus::Draft 
                        : AssessmentStatus::Submitted;

                    $assessment = InnovationAssessment::updateOrCreate(
                        [
                            'festival_id' => $festival->id,
                            'award_category_id' => $digitalOpd->id,
                            'jury_subcategory_participation_id' => $part->id,
                            'innovation_id' => $innovation->id,
                        ],
                        [
                            'status' => $status,
                            'submitted_at' => ($status === AssessmentStatus::Submitted) ? now()->subHours(12) : null,
                        ]
                    );

                    foreach ($criteria as $criterion) {
                        $baseScore = ($jIndex === 0) ? 9 : (($jIndex === 1) ? 8 : 7);
                        $score = min(10, max(6, $baseScore + (($iIndex + $criterion->id) % 3) - 1));

                        InnovationAssessmentDetail::updateOrCreate(
                            [
                                'innovation_assessment_id' => $assessment->id,
                                'assessment_criterion_id' => $criterion->id,
                            ],
                            [
                                'festival_id' => $festival->id,
                                'assessment_type' => AssessmentType::Innovation,
                                'score' => $score,
                            ]
                        );
                    }
                }
            }
        }

        // Setup 'LEMBAGA TERINOVATIF OPD' as OPEN with active scoring
        if ($lembagaOpd) {
            $lembagaOpd->update([
                'scoring_status' => ScoringStatus::Open,
                'opened_at' => now()->subDay(),
            ]);

            $instCriteria = AssessmentCriterion::where('festival_id', $festival->id)
                ->where('assessment_type', AssessmentType::Institution)
                ->get();

            $categoryInstitutions = Institution::where('award_category_id', $lembagaOpd->id)->get();

            foreach (array_slice($juries, 0, 2) as $jIndex => $jury) {
                $fj = FestivalJury::where('festival_id', $festival->id)->where('user_id', $jury->id)->first();
                if (! $fj) {
                    continue;
                }

                $part = JurySubcategoryParticipation::updateOrCreate(
                    [
                        'festival_id' => $festival->id,
                        'festival_jury_id' => $fj->id,
                        'award_category_id' => $lembagaOpd->id,
                    ],
                    ['status' => ParticipationStatus::InProgress]
                );

                foreach ($categoryInstitutions as $instIndex => $inst) {
                    $assessment = InstitutionAssessment::updateOrCreate(
                        [
                            'festival_id' => $festival->id,
                            'award_category_id' => $lembagaOpd->id,
                            'jury_subcategory_participation_id' => $part->id,
                            'institution_id' => $inst->id,
                        ],
                        [
                            'status' => AssessmentStatus::Submitted,
                            'submitted_at' => now()->subHours(6),
                        ]
                    );

                    foreach ($instCriteria as $criterion) {
                        $score = min(10, max(6, 8 + (($instIndex + $criterion->id) % 3) - 1));

                        InstitutionAssessmentDetail::updateOrCreate(
                            [
                                'institution_assessment_id' => $assessment->id,
                                'assessment_criterion_id' => $criterion->id,
                            ],
                            [
                                'festival_id' => $festival->id,
                                'assessment_type' => AssessmentType::Institution,
                                'score' => $score,
                            ]
                        );
                    }
                }
            }
        }
    }
}
