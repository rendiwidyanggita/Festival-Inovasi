<?php

namespace Tests\Feature;

use App\Enums\AssessmentStatus;
use App\Enums\AssessmentType;
use App\Enums\FestivalStatus;
use App\Enums\ScoringStatus;
use App\Models\AssessmentCriterion;
use App\Models\AwardCategory;
use App\Models\Festival;
use App\Models\FestivalCategory;
use App\Models\FestivalJury;
use App\Models\Innovation;
use App\Models\InnovationAssessment;
use App\Models\InnovationAssessmentDetail;
use App\Models\InnovationWinner;
use App\Models\Institution;
use App\Models\InstitutionAssessment;
use App\Models\InstitutionAssessmentDetail;
use App\Models\InstitutionWinner;
use App\Models\JurySubcategoryParticipation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportingTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;
    protected Festival $festival;
    protected FestivalCategory $festivalCategory;
    protected AwardCategory $awardCategory;
    protected Institution $institution;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create(['role' => 'superadmin']);
        
        $this->festival = Festival::create([
            'year' => 2026,
            'name' => 'Festival Inovasi 2026',
            'status' => FestivalStatus::Ongoing,
        ]);

        $this->festivalCategory = FestivalCategory::create([
            'festival_id' => $this->festival->id,
            'name' => 'Kategori Inovasi',
            'assessment_type' => AssessmentType::Innovation,
        ]);

        $this->awardCategory = AwardCategory::create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $this->festivalCategory->id,
            'name' => 'Subkategori Inovasi Test',
            'winner_count' => 3,
            'scoring_status' => ScoringStatus::Closed,
            'sort_order' => 1,
        ]);

        // Default institution category & institution for innovation FK
        $instCat = AwardCategory::create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $this->festivalCategory->id,
            'name' => 'Subkategori Instansi Ref',
            'winner_count' => 3,
            'scoring_status' => ScoringStatus::Closed,
            'sort_order' => 99,
        ]);

        $this->institution = Institution::create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $instCat->id,
            'name' => 'Instansi Induk Test',
        ]);

        // Create assessment criteria
        AssessmentCriterion::create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Innovation,
            'name' => 'Kriteria 1',
            'weight' => 50,
        ]);

        AssessmentCriterion::create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Innovation,
            'name' => 'Kriteria 2',
            'weight' => 50,
        ]);
    }

    protected function createInnovation(array $attributes = []): Innovation
    {
        return Innovation::create(array_merge([
            'festival_id' => $this->festival->id,
            'award_category_id' => $this->awardCategory->id,
            'institution_id' => $this->institution->id,
            'name' => 'Inovasi Default',
            'innovator_name' => 'Inovator Default',
        ], $attributes));
    }
    protected function createJuryParticipation(AwardCategory $category): JurySubcategoryParticipation
    {
        $jury = User::factory()->create(['role' => 'jury']);
        $festivalJury = FestivalJury::create([
            'festival_id' => $category->festival_id,
            'user_id' => $jury->id,
            'status' => 'active',
        ]);

        return JurySubcategoryParticipation::create([
            'festival_id' => $category->festival_id,
            'festival_jury_id' => $festivalJury->id,
            'award_category_id' => $category->id,
            'status' => 'in_progress',
        ]);
    }


    public function test_ranking_calculation_works_correctly()
    {
        // Create innovations
        $innovation1 = $this->createInnovation(['name' => 'Inovasi A', 'innovator_name' => 'Inovator A']);
        $innovation2 = $this->createInnovation(['name' => 'Inovasi B', 'innovator_name' => 'Inovator B']);

        $participation1 = $this->createJuryParticipation($this->awardCategory);
        $participation2 = $this->createJuryParticipation($this->awardCategory);

        // Create locked assessments with different scores
        $this->createLockedAssessment($innovation1, $participation1, 90);
        $this->createLockedAssessment($innovation1, $participation2, 80);
        $this->createLockedAssessment($innovation2, $participation1, 70);
        $this->createLockedAssessment($innovation2, $participation2, 60);

        // Get rankings
        $rankingService = app(\App\Services\RankingService::class);
        $rankings = $rankingService->getRankings($this->awardCategory);

        $this->assertCount(2, $rankings);

        // Innovation 1 should be ranked 1 (higher average)
        $this->assertEquals(1, $rankings->firstWhere('id', $innovation1->id)->rank);
        $this->assertEquals(2, $rankings->firstWhere('id', $innovation2->id)->rank);

        // Average scores should be correct
        $avg1 = $rankings->firstWhere('id', $innovation1->id)->average_score;
        $avg2 = $rankings->firstWhere('id', $innovation2->id)->average_score;
        
        $this->assertEquals(85.0, $avg1);
        $this->assertEquals(65.0, $avg2);
    }

    public function test_winner_assignment_validates_winner_count()
    {
        $this->actingAs($this->superadmin);

        // Create 4 innovations
        $innovations = [];
        for ($i = 1; $i <= 4; $i++) {
            $innovations[] = $this->createInnovation(['name' => "Inovasi $i", 'innovator_name' => "Inovator $i"]);
        }

        // Try to assign 4 winners when winner_count is 3
        $response = $this->post(route('admin.award-categories.winners.store', $this->awardCategory), [
            'winners' => [
                ['target_type' => 'innovation', 'target_id' => $innovations[0]->id, 'position' => 1],
                ['target_type' => 'innovation', 'target_id' => $innovations[1]->id, 'position' => 2],
                ['target_type' => 'innovation', 'target_id' => $innovations[2]->id, 'position' => 3],
                ['target_type' => 'innovation', 'target_id' => $innovations[3]->id, 'position' => 4],
            ],
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_winner_assignment_validates_unique_positions()
    {
        $this->actingAs($this->superadmin);

        $innovation1 = $this->createInnovation(['name' => 'Inovasi A']);
        $innovation2 = $this->createInnovation(['name' => 'Inovasi B']);

        // Try to assign same position to two innovations
        $response = $this->post(route('admin.award-categories.winners.store', $this->awardCategory), [
            'winners' => [
                ['target_type' => 'innovation', 'target_id' => $innovation1->id, 'position' => 1],
                ['target_type' => 'innovation', 'target_id' => $innovation2->id, 'position' => 1],
            ],
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_winner_assignment_creates_audit_log()
    {
        $this->actingAs($this->superadmin);

        $innovation = $this->createInnovation(['name' => 'Inovasi A']);

        $participation = $this->createJuryParticipation($this->awardCategory);

        $this->createLockedAssessment($innovation, $participation, 90);

        $response = $this->post(route('admin.award-categories.winners.store', $this->awardCategory), [
            'winners' => [
                ['target_type' => 'innovation', 'target_id' => $innovation->id, 'position' => 1],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('innovation_winners', [
            'innovation_id' => $innovation->id,
            'award_category_id' => $this->awardCategory->id,
            'position' => 1,
            'appointed_by' => $this->superadmin->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $this->superadmin->id,
            'action' => 'appoint_winner',
            'auditable_type' => 'innovation_winner',
        ]);
    }

    public function test_pdf_generation_returns_download_response()
    {
        $this->actingAs($this->superadmin);

        $this->createInnovation(['name' => 'Inovasi Test']);

        $response = $this->get(route('admin.reports.pdf', $this->awardCategory));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_publication_updates_timestamp()
    {
        $this->actingAs($this->superadmin);

        $this->assertNull($this->festival->results_published_at);

        $response = $this->post(route('admin.festivals.publish-results', $this->festival));

        $response->assertRedirect();

        $this->festival->refresh();
        $this->assertNotNull($this->festival->results_published_at);
        $this->assertEquals($this->superadmin->id, $this->festival->results_published_by);
    }

    public function test_publication_creates_audit_log()
    {
        $this->actingAs($this->superadmin);

        $response = $this->post(route('admin.festivals.publish-results', $this->festival));

        $response->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $this->superadmin->id,
            'action' => 'publish_results',
            'auditable_type' => 'festival',
            'auditable_id' => $this->festival->id,
        ]);
    }

    public function test_unpublish_clears_timestamp()
    {
        $this->actingAs($this->superadmin);

        // First publish
        $this->festival->update([
            'results_published_at' => now(),
            'results_published_by' => $this->superadmin->id,
        ]);

        // Then unpublish
        $response = $this->post(route('admin.festivals.unpublish-results', $this->festival));

        $response->assertRedirect();

        $this->festival->refresh();
        $this->assertNull($this->festival->results_published_at);
        $this->assertNull($this->festival->results_published_by);
    }

    public function test_public_view_shows_results_when_published()
    {
        $innovation = $this->createInnovation(['name' => 'Inovasi Test']);

        // Publish results
        $this->festival->update(['results_published_at' => now()]);

        // Create a winner
        InnovationWinner::create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $this->awardCategory->id,
            'innovation_id' => $innovation->id,
            'position' => 1,
            'appointed_by' => $this->superadmin->id,
            'appointed_at' => now(),
            'total_score' => 90.5,
        ]);

        $response = $this->get(route('public.category', [$this->festival, $this->awardCategory]));

        $response->assertOk();
        $response->assertSee('Pemenang');
        $response->assertSee('Juara 1');
    }

    public function test_public_view_hides_results_when_not_published()
    {
        $this->createInnovation(['name' => 'Inovasi Test']);

        // results_published_at is null
        $response = $this->get(route('public.category', [$this->festival, $this->awardCategory]));

        $response->assertOk();
        $response->assertSee('Penilaian sedang berlangsung');
        $response->assertDontSee('Juara 1');
    }

    public function test_institution_ranking_calculation_works_correctly()
    {
        $festivalCategory = FestivalCategory::create([
            'festival_id' => $this->festival->id,
            'name' => 'Kategori Instansi',
            'assessment_type' => AssessmentType::Institution,
        ]);

        $awardCategory = AwardCategory::create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $festivalCategory->id,
            'name' => 'Subkategori Instansi Test',
            'winner_count' => 2,
            'scoring_status' => ScoringStatus::Closed,
            'sort_order' => 2,
        ]);

        $institution1 = Institution::create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $awardCategory->id,
            'name' => 'Instansi A',
        ]);

        $institution2 = Institution::create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $awardCategory->id,
            'name' => 'Instansi B',
        ]);

        $participation = $this->createJuryParticipation($awardCategory);

        // Create criteria for institution assessment
        AssessmentCriterion::create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Institution,
            'name' => 'Kriteria Instansi 1',
            'weight' => 100,
        ]);

        // Create locked assessments
        $this->createLockedInstitutionAssessment($institution1, $participation, $awardCategory, 80);
        $this->createLockedInstitutionAssessment($institution2, $participation, $awardCategory, 90);

        $rankingService = app(\App\Services\RankingService::class);
        $rankings = $rankingService->getRankings($awardCategory);

        $this->assertCount(2, $rankings);
        $this->assertEquals(1, $rankings->firstWhere('id', $institution2->id)->rank);
        $this->assertEquals(2, $rankings->firstWhere('id', $institution1->id)->rank);
        $this->assertEquals(90.0, $rankings->firstWhere('id', $institution2->id)->average_score);
        $this->assertEquals(80.0, $rankings->firstWhere('id', $institution1->id)->average_score);
    }

    protected function createLockedAssessment($innovation, $participation, $score): InnovationAssessment
    {
        $assessment = InnovationAssessment::create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $this->awardCategory->id,
            'jury_subcategory_participation_id' => $participation->id,
            'innovation_id' => $innovation->id,
            'status' => AssessmentStatus::Locked,
            'locked_at' => now(),
        ]);

        $criteria = AssessmentCriterion::where('festival_id', $this->festival->id)
            ->where('assessment_type', AssessmentType::Innovation)
            ->get();

        foreach ($criteria as $criterion) {
            InnovationAssessmentDetail::create([
                'festival_id' => $this->festival->id,
                'innovation_assessment_id' => $assessment->id,
                'assessment_criterion_id' => $criterion->id,
                'score' => $score,
            ]);
        }

        return $assessment;
    }

    protected function createLockedInstitutionAssessment($institution, $participation, $awardCategory, $score): InstitutionAssessment
    {
        $assessment = InstitutionAssessment::create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $awardCategory->id,
            'jury_subcategory_participation_id' => $participation->id,
            'institution_id' => $institution->id,
            'status' => AssessmentStatus::Locked,
            'locked_at' => now(),
        ]);

        $criteria = AssessmentCriterion::where('festival_id', $this->festival->id)
            ->where('assessment_type', AssessmentType::Institution)
            ->get();

        foreach ($criteria as $criterion) {
            InstitutionAssessmentDetail::create([
                'festival_id' => $this->festival->id,
                'institution_assessment_id' => $assessment->id,
                'assessment_criterion_id' => $criterion->id,
                'score' => $score,
            ]);
        }

        return $assessment;
    }
}
