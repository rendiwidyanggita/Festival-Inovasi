<?php

namespace Database\Seeders;

use App\Enums\AssessmentType;
use App\Enums\ScoringStatus;
use App\Models\AwardCategory;
use App\Models\Festival;
use App\Models\FestivalCategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Jumlah pemenang default per subkategori saat seeding.
     * Dapat diubah superadmin per subkategori sebelum penetapan pemenang (PRD 4.16).
     */
    public const DEFAULT_WINNER_COUNT = 3;

    public function run(Festival $festival): void
    {
        $mains = [
            [
                'name' => 'ASN TERINOVATIF',
                'assessment_type' => AssessmentType::Innovation,
                'subcategories' => [
                    'ASN TERINOVATIF DIGITAL OPD',
                    'ASN TERINOVATIF NON DIGITAL OPD',
                    'ASN TERINOVATIF DIGITAL UNIT',
                    'ASN TERINOVATIF NON DIGITAL UNIT',
                    'ASN TERINOVATIF SEKOLAH TK',
                    'ASN TERINOVATIF SEKOLAH SD',
                    'ASN TERINOVATIF SEKOLAH SMP',
                ],
            ],
            [
                'name' => 'LEMBAGA/INSTANSI TERINOVATIF',
                'assessment_type' => AssessmentType::Institution,
                'subcategories' => [
                    'LEMBAGA TERINOVATIF KELURAHAN',
                    'LEMBAGA TERINOVATIF OPD',
                    'LEMBAGA TERINOVATIF PUSKESMAS',
                    'LEMBAGA TERINOVATIF TK',
                    'LEMBAGA TERINOVATIF SD',
                    'LEMBAGA TERINOVATIF SMP',
                ],
            ],
        ];

        foreach ($mains as $mainIndex => $main) {
            $category = FestivalCategory::firstOrCreate(
                ['festival_id' => $festival->id, 'name' => $main['name']],
                [
                    'assessment_type' => $main['assessment_type'],
                    'sort_order' => $mainIndex + 1,
                ],
            );

            foreach ($main['subcategories'] as $subIndex => $subName) {
                AwardCategory::firstOrCreate(
                    ['festival_category_id' => $category->id, 'name' => $subName],
                    [
                        'festival_id' => $festival->id,
                        'winner_count' => static::DEFAULT_WINNER_COUNT,
                        'scoring_status' => ScoringStatus::Preparation,
                        'sort_order' => $subIndex + 1,
                    ],
                );
            }
        }
    }
}
