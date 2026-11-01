<?php

namespace Database\Seeders;

use App\Models\Festival;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed aplikasi: superadmin awal, festival periode berjalan,
     * kategori/subkategori, dan kriteria penilaian.
     *
     * DemoSeeder (data contoh juri/peserta) tidak dijalankan di sini;
     * panggil eksplisit pada lingkungan pengembangan lokal.
     */
    public function run(): void
    {
        $this->call(SuperadminSeeder::class);
        $this->call(FestivalSeeder::class);

        $festival = Festival::where('year', FestivalSeeder::targetYear())->firstOrFail();

        $this->call(CategorySeeder::class, parameters: ['festival' => $festival]);
        $this->call(AssessmentCriterionSeeder::class, parameters: ['festival' => $festival]);
    }
}
