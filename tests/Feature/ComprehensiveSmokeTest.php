<?php

namespace Tests\Feature;

use App\Enums\AssessmentStatus;
use App\Enums\AssessmentType;
use App\Enums\FestivalJuryStatus;
use App\Enums\FestivalStatus;
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
use App\Models\JurySubcategoryParticipation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprehensiveSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;
    protected User $jury;
    protected Festival $festival;
    protected FestivalCategory $category;
    protected AwardCategory $awardCategory;
    protected Institution $institution;
    protected Innovation $innovation;
    protected AssessmentCriterion $criterion;
    protected FestivalJury $festivalJury;
    protected JurySubcategoryParticipation $participation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Superadmin,
            'is_active' => true,
        ]);

        $this->jury = User::create([
            'name' => 'Juri Satu',
            'email' => 'juri1@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Jury,
            'is_active' => true,
        ]);

        $this->festival = Festival::create([
            'name' => 'Festival Inovasi 2026',
            'year' => 2026,
            'status' => FestivalStatus::Ongoing,
        ]);

        $this->category = FestivalCategory::create([
            'festival_id' => $this->festival->id,
            'name' => 'Inovasi Pelayanan Publik',
            'assessment_type' => AssessmentType::Innovation,
            'sort_order' => 1,
        ]);

        $this->awardCategory = AwardCategory::create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $this->category->id,
            'name' => 'Kesehatan',
            'winner_count' => 3,
            'sort_order' => 1,
            'scoring_status' => ScoringStatus::Preparation,
        ]);

        $this->institution = Institution::create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $this->awardCategory->id,
            'name' => 'Dinas Kesehatan',
        ]);

        $this->innovation = Innovation::create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $this->awardCategory->id,
            'institution_id' => $this->institution->id,
            'name' => 'Aplikasi SehatKu',
            'innovator_name' => 'Dr. Rendi',
        ]);

        $this->criterion = AssessmentCriterion::create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Innovation,
            'name' => 'Kreativitas',
            'weight' => 100,
            'sort_order' => 1,
        ]);

        $this->festivalJury = FestivalJury::create([
            'festival_id' => $this->festival->id,
            'user_id' => $this->jury->id,
            'status' => FestivalJuryStatus::Active,
        ]);

        $this->participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury->id,
            'award_category_id' => $this->awardCategory->id,
        ]);
    }

    public function test_public_pages_render_without_errors(): void
    {
        $this->get('/')->assertOk();
        $this->get('/festival/' . $this->festival->id)->assertOk();
        $this->get('/festival/' . $this->festival->id . '/kategori/' . $this->awardCategory->id)->assertOk();
    }

    public function test_superadmin_dashboard_and_selection_render(): void
    {
        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->get(route('admin.select-festival.index'))
            ->assertOk();
    }

    public function test_superadmin_festivals_pages_render(): void
    {
        $this->actingAs($this->superadmin)
            ->get(route('admin.festivals.index'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->get(route('admin.festivals.create'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->get(route('admin.festivals.edit', $this->festival))
            ->assertOk();
    }

    public function test_superadmin_festival_categories_pages_render(): void
    {
        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.festival-categories.index'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.festival-categories.create'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.festival-categories.edit', $this->category))
            ->assertOk();
    }

    public function test_superadmin_award_categories_pages_render(): void
    {
        // Index
        $response = $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.festival-categories.award-categories.index', $this->category));
        $response->assertOk();

        // Create
        $response = $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.festival-categories.award-categories.create', $this->category));
        $response->assertOk();

        // Edit
        $response = $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.award-categories.edit', $this->awardCategory));
        $response->assertOk();
    }

    public function test_superadmin_institutions_pages_render(): void
    {
        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.institutions.index'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.institutions.create'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.institutions.edit', $this->institution))
            ->assertOk();
    }

    public function test_superadmin_innovations_pages_render(): void
    {
        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.innovations.index'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.innovations.create'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.innovations.edit', $this->innovation))
            ->assertOk();
    }

    public function test_superadmin_juries_pages_render(): void
    {
        $this->actingAs($this->superadmin)
            ->get(route('admin.juries.index'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->get(route('admin.juries.create'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->get(route('admin.juries.edit', $this->jury))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.festival-juries.index'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.festival-juries.create'))
            ->assertOk();
    }

    public function test_superadmin_criteria_pages_render(): void
    {
        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.assessment-criteria.index'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.assessment-criteria.create'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.assessment-criteria.edit', $this->criterion))
            ->assertOk();
    }

    public function test_superadmin_scoring_monitoring_render(): void
    {
        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.scoring.monitoring'))
            ->assertOk();
    }

    public function test_superadmin_winners_and_reports_render(): void
    {
        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.winners.index'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.award-categories.rankings', $this->awardCategory))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.reports.index'))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.reports.preview', $this->awardCategory))
            ->assertOk();
    }

    public function test_jury_dashboard_and_flow_render(): void
    {
        $this->actingAs($this->jury)
            ->get(route('jury.dashboard'))
            ->assertOk();

        $this->actingAs($this->jury)
            ->get(route('jury.participations.index', $this->festival))
            ->assertOk();

        $this->actingAs($this->jury)
            ->get(route('jury.subcategories.show', $this->awardCategory))
            ->assertOk();

        $this->actingAs($this->jury)
            ->get(route('jury.assessments.show', [
                'awardCategory' => $this->awardCategory,
                'type' => 'innovation',
                'id' => $this->innovation->id,
            ]))
            ->assertOk();
    }
}
