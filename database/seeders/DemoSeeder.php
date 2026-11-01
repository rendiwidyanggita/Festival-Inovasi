<?php

namespace Database\Seeders;

use App\Enums\AssessmentType;
use App\Enums\FestivalJuryStatus;
use App\Enums\UserRole;
use App\Models\AwardCategory;
use App\Models\Festival;
use App\Models\FestivalCategory;
use App\Models\FestivalJury;
use App\Models\Innovation;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Data contoh untuk pengembangan lokal saja.
 * Jalankan eksplisit: php artisan db:seed --seeder=DemoSeeder
 * Tidak dipanggil oleh DatabaseSeeder agar tidak ikut ter-seed di staging/production.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $festival = Festival::where('year', FestivalSeeder::targetYear())->firstOrFail();

        $this->seedJuries($festival);
        $this->seedInstitutions($festival);
        $this->seedInnovations($festival);
    }

    private function seedJuries(Festival $festival): void
    {
        $names = ['Juri Contoh Satu', 'Juri Contoh Dua', 'Juri Contoh Tiga'];

        foreach ($names as $index => $name) {
            $user = User::firstOrCreate(
                ['email' => "juri".($index + 1)."@festivalinovasi.test"],
                [
                    'name' => $name,
                    'password' => 'password',
                    'role' => UserRole::Jury,
                    'is_active' => true,
                ],
            );

            FestivalJury::firstOrCreate(
                ['festival_id' => $festival->id, 'user_id' => $user->id],
                ['status' => FestivalJuryStatus::Active],
            );
        }
    }

    private function seedInstitutions(Festival $festival): void
    {
        $suffixBySubcategory = [
            'LEMBAGA TERINOVATIF KELURAHAN' => 'Kelurahan',
            'LEMBAGA TERINOVATIF OPD' => 'Dinas',
            'LEMBAGA TERINOVATIF PUSKESMAS' => 'Puskesmas',
            'LEMBAGA TERINOVATIF TK' => 'TK',
            'LEMBAGA TERINOVATIF SD' => 'SD',
            'LEMBAGA TERINOVATIF SMP' => 'SMP',
        ];

        $institutionSubcategories = AwardCategory::query()
            ->where('festival_id', $festival->id)
            ->whereHas('festivalCategory', fn ($q) => $q->where('assessment_type', AssessmentType::Institution->value))
            ->get();

        foreach ($institutionSubcategories as $subcategory) {
            $suffix = $suffixBySubcategory[$subcategory->name] ?? 'Instansi';

            foreach (range(1, 3) as $i) {
                Institution::firstOrCreate(
                    [
                        'festival_id' => $festival->id,
                        'award_category_id' => $subcategory->id,
                        'name' => "Instansi Contoh {$i} {$suffix}",
                    ],
                );
            }
        }
    }

    private function seedInnovations(Festival $festival): void
    {
        $innovationSubcategories = AwardCategory::query()
            ->where('festival_id', $festival->id)
            ->whereHas('festivalCategory', fn ($q) => $q->where('assessment_type', AssessmentType::Innovation->value))
            ->get();

        // Instansi pemilik inovasi contoh: ambil dari subkategori OPD pada festival yang sama.
        // Kategori inovasi tidak harus sama dengan kategori instansi pemiliknya (PRD 4.5).
        $institutionPool = Institution::query()
            ->where('festival_id', $festival->id)
            ->where('award_category_id', function ($query) use ($festival) {
                $query->select('id')
                    ->from('award_categories')
                    ->where('festival_id', $festival->id)
                    ->where('name', 'LEMBAGA TERINOVATIF OPD')
                    ->limit(1);
            })
            ->orderBy('id')
            ->get();

        if ($institutionPool->isEmpty()) {
            $institutionPool = Institution::where('festival_id', $festival->id)->orderBy('id')->limit(3)->get();
        }

        if ($institutionPool->isEmpty()) {
            return;
        }

        foreach ($innovationSubcategories as $index => $subcategory) {
            $owner = $institutionPool[$index % $institutionPool->count()];

            foreach (range(1, 2) as $i) {
                Innovation::firstOrCreate(
                    [
                        'festival_id' => $festival->id,
                        'award_category_id' => $subcategory->id,
                        'name' => "Inovasi Contoh {$i} — {$subcategory->name}",
                    ],
                    [
                        'institution_id' => $owner->id,
                        'innovator_name' => 'Inovator Contoh '.($index * 2 + $i),
                    ],
                );
            }
        }
    }
}
