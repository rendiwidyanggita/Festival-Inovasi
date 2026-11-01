<?php

namespace Tests\Feature;

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
use App\Models\User;
use App\Models\InstitutionAssessment;
use App\Models\InstitutionAssessmentDetail;
use App\Models\JurySubcategoryParticipation;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Services\ScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScoringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a superadmin
        $this->superadmin = User::factory()->create([
            'role' => UserRole::Superadmin,
            'is_active' => true,
        ]);

        // Create a festival
        $this->festival = Festival::factory()->create();

        // Create festival category (innovation type)
        $this->festivalCategory = FestivalCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Innovation,
        ]);

        // Create award category
        $this->awardCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $this->festivalCategory->id,
            'scoring_status' => ScoringStatus::Preparation,
        ]);

        // Create assessment criteria with total weight = 100
        $this->criteria1 = AssessmentCriterion::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Innovation,
            'weight' => 30,
            'sort_order' => 1,
        ]);

        $this->criteria2 = AssessmentCriterion::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Innovation,
            'weight' => 70,
            'sort_order' => 2,
        ]);

        // Create jury users
        $this->jury1 = User::factory()->create([
            'role' => UserRole::Jury,
            'is_active' => true,
        ]);

        $this->jury2 = User::factory()->create([
            'role' => UserRole::Jury,
            'is_active' => true,
        ]);

        // Create festival juries
        $this->festivalJury1 = FestivalJury::create([
            'festival_id' => $this->festival->id,
            'user_id' => $this->jury1->id,
            'status' => FestivalJuryStatus::Active,
        ]);

        $this->festivalJury2 = FestivalJury::create([
            'festival_id' => $this->festival->id,
            'user_id' => $this->jury2->id,
            'status' => FestivalJuryStatus::Active,
        ]);

        // Create innovations (participants)
        $this->innovation1 = Innovation::factory()->create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $this->awardCategory->id,
        ]);

        $this->innovation2 = Innovation::factory()->create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $this->awardCategory->id,
        ]);

        $this->scoringService = app(ScoringService::class);
    }
    #[Test]
    public function jury_can_only_select_subcategory_during_preparation()
    {
        $this->actingAs($this->jury1);

        // Can select when status is Preparation
        $response = $this->post(route('jury.participations.store', $this->festival), [
            'award_categories' => [$this->awardCategory->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('jury_subcategory_participations', [
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
        ]);

        // Open scoring
        $this->awardCategory->update(['scoring_status' => ScoringStatus::Open]);

        // Create another jury and try to select - should fail
        $jury3 = User::factory()->create(['role' => UserRole::Jury, 'is_active' => true]);
        $festivalJury3 = FestivalJury::create([
            'festival_id' => $this->festival->id,
            'user_id' => $jury3->id,
            'status' => FestivalJuryStatus::Active,
        ]);

        $this->actingAs($jury3);
        $response = $this->post(route('jury.participations.store', $this->festival), [
            'award_categories' => [$this->awardCategory->id],
        ]);

        $response->assertSessionHasErrors();
    }
    #[Test]
    public function open_scoring_locks_roster_permanently()
    {
        // Jury selects subcategory
        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
            'status' => ParticipationStatus::Selected,
        ]);

        // Open scoring
        $this->scoringService->openScoring($this->awardCategory, $this->superadmin);

        // Verify status changed
        $this->awardCategory->refresh();
        $this->assertEquals(ScoringStatus::Open, $this->awardCategory->scoring_status);
        $this->assertNotNull($this->awardCategory->opened_at);
        $this->assertEquals($this->superadmin->id, $this->awardCategory->opened_by);

        // Verify roster is locked
        $this->assertTrue($this->awardCategory->isRosterLocked());
        $participation->refresh();
        $this->assertEquals(ParticipationStatus::InProgress, $participation->status);

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'open_scoring',
            'auditable_type' => 'award_category',
            'auditable_id' => $this->awardCategory->id,
            'actor_user_id' => $this->superadmin->id,
        ]);
    }
    #[Test]
    public function open_scoring_validates_requirements()
    {
        // Test: No participants
        $emptyCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $this->festivalCategory->id,
            'scoring_status' => ScoringStatus::Preparation,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tidak ada peserta');
        $this->scoringService->openScoring($emptyCategory, $this->superadmin);

        // Test: No juries
        $categoryNoJury = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $this->festivalCategory->id,
            'scoring_status' => ScoringStatus::Preparation,
        ]);
        Innovation::factory()->create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $categoryNoJury->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tidak ada juri');
        $this->scoringService->openScoring($categoryNoJury, $this->superadmin);

        // Test: Weight != 100
        $this->criteria1->update(['weight' => 50]);
        $this->criteria2->update(['weight' => 40]); // Total = 90

        JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Total bobot kriteria harus 100');
        $this->scoringService->openScoring($this->awardCategory, $this->superadmin);
    }
    #[Test]
    public function score_entry_must_be_between_6_and_10()
    {
        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
        ]);

        $this->scoringService->openScoring($this->awardCategory, $this->superadmin);

        // Test: Score below 6 should fail
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Nilai harus berupa bilangan bulat antara 6 sampai 10');
        
        $this->scoringService->submitAssessment(
            $participation,
            $this->innovation1,
            [
                $this->criteria1->id => 5, // Invalid
                $this->criteria2->id => 8,
            ],
            $this->jury1
        );
    }
    #[Test]
    public function draft_can_have_incomplete_scores()
    {
        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
        ]);

        $this->scoringService->openScoring($this->awardCategory, $this->superadmin);

        // Save draft with partial scores
        $assessment = $this->scoringService->saveDraft(
            $participation,
            $this->innovation1,
            [
                $this->criteria1->id => 8, // Only one criterion filled
            ],
            $this->jury1
        );

        $this->assertEquals(AssessmentStatus::Draft, $assessment->status);
        $this->assertDatabaseHas('innovation_assessments', [
            'id' => $assessment->id,
            'status' => AssessmentStatus::Draft->value,
        ]);

        // Verify only one detail saved
        $this->assertEquals(1, $assessment->details()->count());
    }
    #[Test]
    public function submit_requires_all_criteria_filled()
    {
        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
        ]);

        $this->scoringService->openScoring($this->awardCategory, $this->superadmin);

        // Try to submit with missing criteria
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Semua kriteria harus diisi');
        
        $this->scoringService->submitAssessment(
            $participation,
            $this->innovation1,
            [
                $this->criteria1->id => 8, // Missing criteria2
            ],
            $this->jury1
        );
    }
    #[Test]
    public function submit_saves_all_criteria_and_updates_status()
    {
        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
        ]);

        $this->scoringService->openScoring($this->awardCategory, $this->superadmin);

        $assessment = $this->scoringService->submitAssessment(
            $participation,
            $this->innovation1,
            [
                $this->criteria1->id => 8,
                $this->criteria2->id => 9,
            ],
            $this->jury1
        );

        $this->assertEquals(AssessmentStatus::Submitted, $assessment->status);
        $this->assertNotNull($assessment->submitted_at);

        // Verify both criteria saved
        $this->assertEquals(2, $assessment->details()->count());
        $this->assertDatabaseHas('innovation_assessment_details', [
            'innovation_assessment_id' => $assessment->id,
            'assessment_criterion_id' => $this->criteria1->id,
            'score' => 8,
        ]);
        $this->assertDatabaseHas('innovation_assessment_details', [
            'innovation_assessment_id' => $assessment->id,
            'assessment_criterion_id' => $this->criteria2->id,
            'score' => 9,
        ]);

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'submit_assessment',
            'auditable_type' => 'innovation_assessment',
            'auditable_id' => $assessment->id,
        ]);
    }
    #[Test]
    public function weighted_score_calculation()
    {
        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
        ]);

        $this->scoringService->openScoring($this->awardCategory, $this->superadmin);

        $assessment = $this->scoringService->submitAssessment(
            $participation,
            $this->innovation1,
            [
                $this->criteria1->id => 8, // weight 30
                $this->criteria2->id => 10, // weight 70
            ],
            $this->jury1
        );

        // Weighted score: (8 * 30 + 10 * 70) / 100 = (240 + 700) / 100 = 9.4
        $weightedScore = $this->scoringService->calculateWeightedScore($assessment);
        $this->assertEquals(9.4, $weightedScore);

        // Also test model method
        $this->assertEquals(9.4, $assessment->weightedTotal());
    }
    #[Test]
    public function average_score_calculation()
    {
        $participation1 = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
        ]);

        $participation2 = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury2->id,
            'award_category_id' => $this->awardCategory->id,
        ]);

        $this->scoringService->openScoring($this->awardCategory, $this->superadmin);

        // Jury 1: score = 9.4
        $this->scoringService->submitAssessment(
            $participation1,
            $this->innovation1,
            [
                $this->criteria1->id => 8,
                $this->criteria2->id => 10,
            ],
            $this->jury1
        );

        // Jury 2: score = 8.0
        $this->scoringService->submitAssessment(
            $participation2,
            $this->innovation1,
            [
                $this->criteria1->id => 8,
                $this->criteria2->id => 8,
            ],
            $this->jury2
        );

        // Average: (9.4 + 8.0) / 2 = 8.7
        $averageScore = $this->scoringService->calculateAverageScore($this->innovation1);
        $this->assertEquals(8.7, $averageScore);
    }
    #[Test]
    public function lock_assessment()
    {
        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
        ]);

        $this->scoringService->openScoring($this->awardCategory, $this->superadmin);

        $assessment = $this->scoringService->submitAssessment(
            $participation,
            $this->innovation1,
            [
                $this->criteria1->id => 8,
                $this->criteria2->id => 9,
            ],
            $this->jury1
        );

        // Lock assessment
        $this->scoringService->lockAssessment('innovation', $assessment->id, $this->superadmin);

        $assessment->refresh();
        $this->assertEquals(AssessmentStatus::Locked, $assessment->status);
        $this->assertNotNull($assessment->locked_at);

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'lock_assessment',
            'auditable_type' => 'innovation_assessment',
            'auditable_id' => $assessment->id,
        ]);
    }
    #[Test]
    public function request_revision_opens_assessment_for_editing()
    {
        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
        ]);

        $this->scoringService->openScoring($this->awardCategory, $this->superadmin);

        $assessment = $this->scoringService->submitAssessment(
            $participation,
            $this->innovation1,
            [
                $this->criteria1->id => 8,
                $this->criteria2->id => 9,
            ],
            $this->jury1
        );

        // Request revision
        $this->scoringService->requestRevision(
            'innovation',
            $assessment->id,
            'Nilai perlu diperbaiki',
            $this->superadmin
        );

        $assessment->refresh();
        $this->assertEquals(AssessmentStatus::RevisionRequested, $assessment->status);

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'request_revision',
            'auditable_type' => 'innovation_assessment',
            'auditable_id' => $assessment->id,
        ]);

        // Jury can now resubmit
        $newAssessment = $this->scoringService->submitAssessment(
            $participation,
            $this->innovation1,
            [
                $this->criteria1->id => 9,
                $this->criteria2->id => 9,
            ],
            $this->jury1
        );

        $this->assertEquals(AssessmentStatus::Submitted, $newAssessment->status);
    }
    #[Test]
    public function close_scoring()
    {
        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
        ]);

        $this->scoringService->openScoring($this->awardCategory, $this->superadmin);
        $this->scoringService->closeScoring($this->awardCategory, $this->superadmin);

        $this->awardCategory->refresh();
        $this->assertEquals(ScoringStatus::Closed, $this->awardCategory->scoring_status);
        $this->assertNotNull($this->awardCategory->closed_at);
        $this->assertEquals($this->superadmin->id, $this->awardCategory->closed_by);

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'close_scoring',
            'auditable_type' => 'award_category',
            'auditable_id' => $this->awardCategory->id,
        ]);
    }
    #[Test]
    public function participation_completed_when_all_objects_assessed()
    {
        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
        ]);

        $this->scoringService->openScoring($this->awardCategory, $this->superadmin);

        // Assess first innovation
        $this->scoringService->submitAssessment(
            $participation,
            $this->innovation1,
            [
                $this->criteria1->id => 8,
                $this->criteria2->id => 9,
            ],
            $this->jury1
        );

        $participation->refresh();
        $this->assertNull($participation->completed_at);

        // Assess second innovation
        $this->scoringService->submitAssessment(
            $participation,
            $this->innovation2,
            [
                $this->criteria1->id => 7,
                $this->criteria2->id => 8,
            ],
            $this->jury1
        );

        $participation->refresh();
        $this->assertNotNull($participation->completed_at);
    }
    #[Test]
    public function institution_assessment_works_similarly()
    {
        // Create institution category
        $institutionCategory = FestivalCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Institution,
        ]);

        $awardCategoryInst = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $institutionCategory->id,
            'scoring_status' => ScoringStatus::Preparation,
        ]);

        // Create institution criteria
        $critInst1 = AssessmentCriterion::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Institution,
            'weight' => 40,
        ]);

        $critInst2 = AssessmentCriterion::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Institution,
            'weight' => 60,
        ]);

        // Create institution
        $institution = Institution::factory()->create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $awardCategoryInst->id,
        ]);

        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $awardCategoryInst->id,
        ]);

        $this->scoringService->openScoring($awardCategoryInst, $this->superadmin);

        $assessment = $this->scoringService->submitAssessment(
            $participation,
            $institution,
            [
                $critInst1->id => 9,
                $critInst2->id => 8,
            ],
            $this->jury1
        );

        $this->assertInstanceOf(InstitutionAssessment::class, $assessment);
        $this->assertEquals(AssessmentStatus::Submitted, $assessment->status);

        // Weighted: (9 * 40 + 8 * 60) / 100 = (360 + 480) / 100 = 8.4
        $this->assertEquals(8.4, $assessment->weightedTotal());
    }

    #[Test]
    public function jury_participations_index_displays_correctly(): void
    {
        $this->actingAs($this->jury1);

        $response = $this->get(route('jury.participations.index', $this->festival));
        $response->assertOk();
        $response->assertViewIs('jury.participations.index');
    }

    #[Test]
    public function jury_subcategories_show_displays_correctly(): void
    {
        JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
            'status' => ParticipationStatus::Selected,
        ]);

        $this->actingAs($this->jury1);

        $response = $this->get(route('jury.subcategories.show', $this->awardCategory));
        $response->assertOk();
        $response->assertViewIs('jury.subcategories.show');
    }

    #[Test]
    public function jury_assessments_show_form_displays_correctly(): void
    {
        JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
            'status' => ParticipationStatus::Selected,
        ]);

        $this->actingAs($this->jury1);

        $response = $this->get(route('jury.assessments.show', [
            'awardCategory' => $this->awardCategory,
            'type' => 'innovation',
            'id' => $this->innovation1->id,
        ]));

        $response->assertOk();
        $response->assertViewIs('jury.assessments.form');
    }

    #[Test]
    public function jury_can_save_draft_via_http(): void
    {
        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
            'status' => ParticipationStatus::Selected,
        ]);

        $this->scoringService->openScoring($this->awardCategory, $this->superadmin);

        $this->actingAs($this->jury1);

        $response = $this->post(route('jury.assessments.draft', [
            'awardCategory' => $this->awardCategory,
            'type' => 'innovation',
            'id' => $this->innovation1->id,
        ]), [
            'scores' => [
                $this->criteria1->id => 7,
            ],
        ]);

        $response->assertRedirect(route('jury.subcategories.show', $this->awardCategory));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('innovation_assessments', [
            'innovation_id' => $this->innovation1->id,
            'status' => AssessmentStatus::Draft->value,
        ]);
    }

    #[Test]
    public function jury_can_submit_assessment_via_http(): void
    {
        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
            'status' => ParticipationStatus::Selected,
        ]);

        $this->scoringService->openScoring($this->awardCategory, $this->superadmin);

        $this->actingAs($this->jury1);

        $response = $this->post(route('jury.assessments.submit', [
            'awardCategory' => $this->awardCategory,
            'type' => 'innovation',
            'id' => $this->innovation1->id,
        ]), [
            'scores' => [
                $this->criteria1->id => 8,
                $this->criteria2->id => 9,
            ],
        ]);

        $response->assertRedirect(route('jury.subcategories.show', $this->awardCategory));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('innovation_assessments', [
            'innovation_id' => $this->innovation1->id,
            'status' => AssessmentStatus::Submitted->value,
        ]);
    }

    #[Test]
    public function admin_can_open_and_close_scoring_via_http(): void
    {
        JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
            'status' => ParticipationStatus::Selected,
        ]);

        $this->actingAs($this->superadmin);

        // Open scoring
        $response = $this->post(route('admin.award-categories.open-scoring', $this->awardCategory));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->awardCategory->refresh();
        $this->assertEquals(ScoringStatus::Open, $this->awardCategory->scoring_status);

        // Close scoring
        $response = $this->post(route('admin.award-categories.close-scoring', $this->awardCategory));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->awardCategory->refresh();
        $this->assertEquals(ScoringStatus::Closed, $this->awardCategory->scoring_status);
    }

    #[Test]
    public function admin_can_lock_and_request_revision_via_http(): void
    {
        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $this->festivalJury1->id,
            'award_category_id' => $this->awardCategory->id,
            'status' => ParticipationStatus::Selected,
        ]);

        $this->scoringService->openScoring($this->awardCategory, $this->superadmin);
        $assessment = $this->scoringService->submitAssessment(
            $participation,
            $this->innovation1,
            [
                $this->criteria1->id => 8,
                $this->criteria2->id => 9,
            ],
            $this->jury1
        );

        $this->actingAs($this->superadmin);

        // Lock assessment
        $response = $this->post(route('admin.assessments.lock', [
            'type' => 'innovation',
            'id' => $assessment->id,
        ]));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $assessment->refresh();
        $this->assertEquals(AssessmentStatus::Locked, $assessment->status);

        // Request revision
        $response = $this->post(route('admin.assessments.request-revision', [
            'type' => 'innovation',
            'id' => $assessment->id,
        ]), [
            'reason' => 'Perbaiki nilai kriteria 1',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $assessment->refresh();
        $this->assertEquals(AssessmentStatus::RevisionRequested, $assessment->status);
        $this->assertEquals('Perbaiki nilai kriteria 1', $assessment->revision_note);
    }

    #[Test]
    public function admin_scoring_monitoring_matrix_is_accessible(): void
    {
        $this->actingAs($this->superadmin);

        $response = $this->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.scoring.monitoring'));

        $response->assertOk();
        $response->assertViewIs('admin.scoring.monitoring');
    }
}
