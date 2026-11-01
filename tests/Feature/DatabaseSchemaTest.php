<?php

namespace Tests\Feature;

use App\Models\AwardCategory;
use App\Models\Festival;
use App\Models\FestivalCategory;
use App\Models\FestivalJury;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function semua_tabel_domain_dan_infrastruktur_tersedia(): void
    {
        $tables = [
            // Infrastruktur Laravel
            'sessions', 'cache', 'jobs',
            // Domain (18 tabel paket migration v0.1)
            'users', 'password_reset_tokens', 'festivals', 'festival_settings',
            'festival_categories', 'award_categories', 'institutions', 'innovations',
            'festival_juries', 'jury_subcategory_participations', 'assessment_criteria',
            'innovation_assessments', 'innovation_assessment_details',
            'institution_assessments', 'institution_assessment_details',
            'innovation_winners', 'institution_winners', 'audit_logs',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Tabel {$table} tidak ditemukan.");
        }
    }

    #[Test]
    public function tahun_festival_harus_unik(): void
    {
        Festival::create(['year' => 2030, 'name' => 'Festival 2030']);

        $this->expectException(QueryException::class);

        DB::table('festivals')->insert(['year' => 2030, 'name' => 'Duplikat 2030']);
    }

    #[Test]
    public function composite_fk_menolak_inovasi_yang_melintasi_festival(): void
    {
        $festivalA = Festival::create(['year' => 2031, 'name' => 'Festival A']);
        $festivalB = Festival::create(['year' => 2032, 'name' => 'Festival B']);

        $categoryA = FestivalCategory::create([
            'festival_id' => $festivalA->id,
            'name' => 'ASN TERINOVATIF',
            'assessment_type' => 'innovation',
        ]);
        $subcategoryA = AwardCategory::create([
            'festival_id' => $festivalA->id,
            'festival_category_id' => $categoryA->id,
            'name' => 'ASN TERINOVATIF DIGITAL OPD',
        ]);

        $this->expectException(QueryException::class);

        // festival_id B dipasangkan dengan award_category milik festival A — harus ditolak DB.
        DB::table('innovations')->insert([
            'festival_id' => $festivalB->id,
            'award_category_id' => $subcategoryA->id,
            'institution_id' => 1,
            'name' => 'Inovasi Melintasi Festival',
            'innovator_name' => 'Inovator',
        ]);
    }

    #[Test]
    public function satu_juri_tidak_bisa_ikut_subkategori_yang_sama_dua_kali(): void
    {
        $festival = Festival::create(['year' => 2033, 'name' => 'Festival C']);
        $jury = User::create(['name' => 'Juri', 'email' => 'juri-dup@test.local', 'password' => 'password', 'role' => 'jury']);
        $festivalJury = FestivalJury::create(['festival_id' => $festival->id, 'user_id' => $jury->id]);

        $category = FestivalCategory::create([
            'festival_id' => $festival->id,
            'name' => 'ASN TERINOVATIF',
            'assessment_type' => 'innovation',
        ]);
        $subcategory = AwardCategory::create([
            'festival_id' => $festival->id,
            'festival_category_id' => $category->id,
            'name' => 'ASN TERINOVATIF DIGITAL OPD',
        ]);

        DB::table('jury_subcategory_participations')->insert([
            'festival_id' => $festival->id,
            'festival_jury_id' => $festivalJury->id,
            'award_category_id' => $subcategory->id,
        ]);

        $this->expectException(QueryException::class);

        DB::table('jury_subcategory_participations')->insert([
            'festival_id' => $festival->id,
            'festival_jury_id' => $festivalJury->id,
            'award_category_id' => $subcategory->id,
        ]);
    }

    #[Test]
    public function satu_posisi_juara_tidak_boleh_dipakai_dua_objek(): void
    {
        $festival = Festival::create(['year' => 2034, 'name' => 'Festival D']);
        $admin = User::create(['name' => 'Admin', 'email' => 'admin-winner@test.local', 'password' => 'password', 'role' => 'superadmin']);

        $category = FestivalCategory::create([
            'festival_id' => $festival->id,
            'name' => 'ASN TERINOVATIF',
            'assessment_type' => 'innovation',
        ]);
        $subcategory = AwardCategory::create([
            'festival_id' => $festival->id,
            'festival_category_id' => $category->id,
            'name' => 'ASN TERINOVATIF DIGITAL OPD',
        ]);

        $institution = DB::table('institutions')->insertGetId([
            'festival_id' => $festival->id,
            'award_category_id' => $subcategory->id,
            'name' => 'Instansi Pemilik',
        ]);

        $innovationIds = [];
        foreach (['Inovasi Satu', 'Inovasi Dua'] as $name) {
            $innovationIds[] = DB::table('innovations')->insertGetId([
                'festival_id' => $festival->id,
                'award_category_id' => $subcategory->id,
                'institution_id' => $institution,
                'name' => $name,
                'innovator_name' => 'Inovator',
            ]);
        }

        DB::table('innovation_winners')->insert([
            'festival_id' => $festival->id,
            'award_category_id' => $subcategory->id,
            'innovation_id' => $innovationIds[0],
            'position' => 1,
            'appointed_by' => $admin->id,
            'appointed_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('innovation_winners')->insert([
            'festival_id' => $festival->id,
            'award_category_id' => $subcategory->id,
            'innovation_id' => $innovationIds[1],
            'position' => 1,
            'appointed_by' => $admin->id,
            'appointed_at' => now(),
        ]);
    }

    #[Test]
    public function seeder_menghasilkan_data_awal_sesuai_prd(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->assertDatabaseHas('users', ['role' => 'superadmin']);
        $this->assertDatabaseCount('festival_categories', 2);
        $this->assertDatabaseCount('award_categories', 13); // 7 ASN + 6 Lembaga

        $this->assertDatabaseHas('festival_categories', ['assessment_type' => 'innovation']);
        $this->assertDatabaseHas('festival_categories', ['assessment_type' => 'institution']);

        foreach (['innovation', 'institution'] as $type) {
            $sum = DB::table('assessment_criteria')
                ->where('assessment_type', $type)
                ->sum('weight');
            $this->assertSame(100, (int) $sum, "Total bobot {$type} harus 100.");
            $count = DB::table('assessment_criteria')->where('assessment_type', $type)->count();
            $this->assertSame(4, $count, "Jumlah kriteria {$type} harus 4.");
        }
    }

    #[Test]
    public function soft_delete_user_tidak_menghapus_relasi_historis(): void
    {
        $festival = Festival::create(['year' => 2035, 'name' => 'Festival E']);
        $jury = User::create(['name' => 'Juri Historis', 'email' => 'juri-hist@test.local', 'password' => 'password', 'role' => 'jury']);
        $festivalJury = FestivalJury::create(['festival_id' => $festival->id, 'user_id' => $jury->id]);

        $jury->delete();

        $this->assertSoftDeleted('users', ['id' => $jury->id]);
        $this->assertDatabaseHas('festival_juries', ['id' => $festivalJury->id]);
    }
}
