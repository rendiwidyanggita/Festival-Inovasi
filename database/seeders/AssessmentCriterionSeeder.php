<?php

namespace Database\Seeders;

use App\Enums\AssessmentType;
use App\Models\AssessmentCriterion;
use App\Models\Festival;
use Illuminate\Database\Seeder;

class AssessmentCriterionSeeder extends Seeder
{
    /** Bobot inovasi dan instansi masing-masing wajib berjumlah 100 (BUSINESS_RULES 6). */
    public function run(Festival $festival): void
    {
        $criteria = [
            AssessmentType::Innovation->value => [
                ['name' => 'Kemampuan Presentasi', 'weight' => 20],
                ['name' => 'Orisinalitas Ide', 'weight' => 30],
                ['name' => 'Manfaat dan Dampak', 'weight' => 20],
                ['name' => 'Proses Transfer Knowledge', 'weight' => 30],
            ],
            AssessmentType::Institution->value => [
                ['name' => 'Kemampuan Presentasi', 'weight' => 20],
                ['name' => 'Managerial', 'weight' => 30],
                ['name' => 'Komitmen', 'weight' => 30],
                ['name' => 'Strategi dan Mitigasi', 'weight' => 20],
            ],
        ];

        foreach ($criteria as $type => $items) {
            foreach ($items as $index => $item) {
                AssessmentCriterion::firstOrCreate(
                    [
                        'festival_id' => $festival->id,
                        'assessment_type' => $type,
                        'name' => $item['name'],
                    ],
                    [
                        'weight' => $item['weight'],
                        'sort_order' => $index + 1,
                    ],
                );
            }
        }
    }
}
