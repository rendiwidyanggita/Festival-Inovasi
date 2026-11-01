<?php

namespace Tests\Feature\Admin;

use App\Enums\AssessmentStatus;
use App\Enums\AssessmentType;
use App\Enums\ScoringStatus;
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
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MasterDataCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;

    private Festival $festival;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->superadmin()->create();
        $this->festival = Festival::factory()->create();
    }

    // === FestivalCategory Tests ===

    #[Test]
    public function superadmin_can_create_festival_category(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->post(route('admin.festival-categories.store'), [
                'name' => 'Kategori Inovasi',
                'assessment_type' => 'innovation',
                'sort_order' => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('festival_categories', [
            'festival_id' => $this->festival->id,
            'name' => 'Kategori Inovasi',
        ]);
    }

    #[Test]
    public function cannot_delete_festival_category_with_subcategories(): void
    {
        $category = FestivalCategory::factory()->create(['festival_id' => $this->festival->id]);
        AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $category->id,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->delete(route('admin.festival-categories.destroy', $category));

        $response->assertForbidden();
    }

    // === AwardCategory Tests ===

    #[Test]
    public function cannot_delete_award_category_with_participants(): void
    {
        $category = FestivalCategory::factory()->create(['festival_id' => $this->festival->id]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $category->id,
            'scoring_status' => ScoringStatus::Preparation,
        ]);
        Institution::factory()->create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $awardCategory->id,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->delete(route('admin.award-categories.destroy', $awardCategory));

        $response->assertForbidden();
    }

    #[Test]
    public function cannot_delete_award_category_after_scoring_opened(): void
    {
        $category = FestivalCategory::factory()->create(['festival_id' => $this->festival->id]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $category->id,
            'scoring_status' => ScoringStatus::Open,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->delete(route('admin.award-categories.destroy', $awardCategory));

        $response->assertForbidden();
    }

    // === Institution Tests ===

    #[Test]
    public function superadmin_can_create_institution_when_preparation(): void
    {
        $category = FestivalCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => 'institution',
        ]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $category->id,
            'scoring_status' => ScoringStatus::Preparation,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->post(route('admin.institutions.store'), [
                'award_category_id' => $awardCategory->id,
                'name' => 'Instansi Contoh',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('institutions', [
            'festival_id' => $this->festival->id,
            'name' => 'Instansi Contoh',
        ]);
    }

    #[Test]
    public function cannot_delete_institution_after_scoring_opened(): void
    {
        $category = FestivalCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => 'institution',
        ]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $category->id,
            'scoring_status' => ScoringStatus::Open,
        ]);
        $institution = Institution::factory()->create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $awardCategory->id,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->delete(route('admin.institutions.destroy', $institution));

        $response->assertForbidden();
    }

    #[Test]
    public function can_update_institution_name_after_scoring_opened(): void
    {
        $category = FestivalCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => 'institution',
        ]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $category->id,
            'scoring_status' => ScoringStatus::Open,
        ]);
        $institution = Institution::factory()->create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $awardCategory->id,
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($this->superadmin)
            ->put(route('admin.institutions.update', $institution), [
                'name' => 'New Name',
                'award_category_id' => $awardCategory->id,
            ]);

        $response->assertRedirect();
        $this->assertEquals('New Name', $institution->fresh()->name);
    }

    // === Innovation Tests ===

    #[Test]
    public function superadmin_can_create_innovation_when_preparation(): void
    {
        $category = FestivalCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => 'innovation',
        ]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $category->id,
            'scoring_status' => ScoringStatus::Preparation,
        ]);
        $institution = Institution::factory()->create([
            'festival_id' => $this->festival->id,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->post(route('admin.innovations.store'), [
                'award_category_id' => $awardCategory->id,
                'institution_id' => $institution->id,
                'name' => 'Inovasi Contoh',
                'innovator_name' => 'John Doe',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('innovations', [
            'festival_id' => $this->festival->id,
            'name' => 'Inovasi Contoh',
        ]);
    }

    #[Test]
    public function cannot_delete_innovation_after_scoring_opened(): void
    {
        $category = FestivalCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => 'innovation',
        ]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $category->id,
            'scoring_status' => ScoringStatus::Open,
        ]);
        $institution = Institution::factory()->create([
            'festival_id' => $this->festival->id,
        ]);
        $innovation = Innovation::factory()->create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $awardCategory->id,
            'institution_id' => $institution->id,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->delete(route('admin.innovations.destroy', $innovation));

        $response->assertForbidden();
    }

    #[Test]
    public function can_update_innovation_name_after_scoring_opened(): void
    {
        $category = FestivalCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => 'innovation',
        ]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $category->id,
            'scoring_status' => ScoringStatus::Open,
        ]);
        $institution = Institution::factory()->create([
            'festival_id' => $this->festival->id,
        ]);
        $innovation = Innovation::factory()->create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $awardCategory->id,
            'institution_id' => $institution->id,
            'name' => 'Old Innovation',
            'innovator_name' => 'Old Innovator',
        ]);

        $response = $this->actingAs($this->superadmin)
            ->put(route('admin.innovations.update', $innovation), [
                'name' => 'New Innovation',
                'innovator_name' => 'New Innovator',
                'award_category_id' => $awardCategory->id,
                'institution_id' => $institution->id,
            ]);

        $response->assertRedirect();
        $this->assertEquals('New Innovation', $innovation->fresh()->name);
        $this->assertEquals('New Innovator', $innovation->fresh()->innovator_name);
    }

    // === AssessmentCriterion Tests ===

    #[Test]
    public function superadmin_can_create_assessment_criterion(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->post(route('admin.assessment-criteria.store'), [
                'assessment_type' => 'innovation',
                'name' => 'Kriteria Originalitas',
                'weight' => 30,
                'sort_order' => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('assessment_criteria', [
            'festival_id' => $this->festival->id,
            'name' => 'Kriteria Originalitas',
            'weight' => 30,
        ]);
    }

    #[Test]
    public function cannot_update_assessment_criterion_with_assessment_details(): void
    {
        $category = FestivalCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Innovation,
        ]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $category->id,
        ]);
        $criterion = AssessmentCriterion::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Innovation,
            'name' => 'Original Name',
            'weight' => 30,
        ]);
        $juryUser = User::factory()->jury()->create();
        $festivalJury = FestivalJury::create([
            'festival_id' => $this->festival->id,
            'user_id' => $juryUser->id,
            'status' => 'active',
        ]);
        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $festivalJury->id,
            'award_category_id' => $awardCategory->id,
            'status' => 'selected',
            'selected_at' => now(),
        ]);
        $institutionCategory = FestivalCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Institution,
        ]);
        $institutionAwardCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $institutionCategory->id,
        ]);
        $institution = Institution::factory()->create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $institutionAwardCategory->id,
        ]);
        $innovation = Innovation::factory()->create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $awardCategory->id,
            'institution_id' => $institution->id,
        ]);
        $assessment = InnovationAssessment::create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $awardCategory->id,
            'jury_subcategory_participation_id' => $participation->id,
            'innovation_id' => $innovation->id,
            'status' => AssessmentStatus::Draft,
        ]);
        InnovationAssessmentDetail::create([
            'festival_id' => $this->festival->id,
            'assessment_type' => 'innovation',
            'innovation_assessment_id' => $assessment->id,
            'assessment_criterion_id' => $criterion->id,
            'score' => 8,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->put(route('admin.assessment-criteria.update', $criterion), [
                'name' => 'Updated Name',
                'assessment_type' => $criterion->assessment_type->value,
                'weight' => 40,
                'sort_order' => 1,
            ]);

        $response->assertForbidden();
    }

    #[Test]
    public function cannot_delete_assessment_criterion_with_assessment_details(): void
    {
        $category = FestivalCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Innovation,
        ]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $category->id,
        ]);
        $criterion = AssessmentCriterion::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Innovation,
        ]);
        $juryUser = User::factory()->jury()->create();
        $festivalJury = FestivalJury::create([
            'festival_id' => $this->festival->id,
            'user_id' => $juryUser->id,
            'status' => 'active',
        ]);
        $participation = JurySubcategoryParticipation::create([
            'festival_id' => $this->festival->id,
            'festival_jury_id' => $festivalJury->id,
            'award_category_id' => $awardCategory->id,
            'status' => 'selected',
            'selected_at' => now(),
        ]);
        $institutionCategory = FestivalCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Institution,
        ]);
        $institutionAwardCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $institutionCategory->id,
        ]);
        $institution = Institution::factory()->create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $institutionAwardCategory->id,
        ]);
        $innovation = Innovation::factory()->create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $awardCategory->id,
            'institution_id' => $institution->id,
        ]);
        $assessment = InnovationAssessment::create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $awardCategory->id,
            'jury_subcategory_participation_id' => $participation->id,
            'innovation_id' => $innovation->id,
            'status' => AssessmentStatus::Draft,
        ]);
        InnovationAssessmentDetail::create([
            'festival_id' => $this->festival->id,
            'assessment_type' => 'innovation',
            'innovation_assessment_id' => $assessment->id,
            'assessment_criterion_id' => $criterion->id,
            'score' => 8,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->delete(route('admin.assessment-criteria.destroy', $criterion));

        $response->assertForbidden();
    }

    // === Jury Management Tests ===

    #[Test]
    public function superadmin_can_create_jury(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->post(route('admin.juries.store'), [
                'name' => 'Ahmad Juri',
                'email' => 'ahmad@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'Ahmad Juri',
            'email' => 'ahmad@example.com',
        ]);
    }

    #[Test]
    public function superadmin_can_update_jury(): void
    {
        $jury = User::factory()->jury()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->superadmin)
            ->put(route('admin.juries.update', $jury), [
                'name' => 'New Name',
                'email' => $jury->email,
            ]);

        $response->assertRedirect();
        $this->assertEquals('New Name', $jury->fresh()->name);
    }

    #[Test]
    public function superadmin_cannot_delete_self(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->delete(route('admin.juries.destroy', $this->superadmin));

        $response->assertForbidden();
    }

    #[Test]
    public function jury_cannot_access_master_data_crud(): void
    {
        $jury = User::factory()->jury()->create();
        $category = FestivalCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Innovation,
        ]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $category->id,
        ]);
        $institutionCategory = FestivalCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'assessment_type' => AssessmentType::Institution,
        ]);
        $institutionAwardCategory = AwardCategory::factory()->create([
            'festival_id' => $this->festival->id,
            'festival_category_id' => $institutionCategory->id,
        ]);
        $institution = Institution::factory()->create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $institutionAwardCategory->id,
        ]);
        $innovation = Innovation::factory()->create([
            'festival_id' => $this->festival->id,
            'award_category_id' => $awardCategory->id,
            'institution_id' => $institution->id,
        ]);
        $criterion = AssessmentCriterion::factory()->create(['festival_id' => $this->festival->id]);

        // Festival Categories
        $this->actingAs($jury)
            ->withSession(['selected_festival_id' => $this->festival->id])
            ->get(route('admin.festival-categories.index'))
            ->assertForbidden();

        // Award Categories
        $this->actingAs($jury)
            ->get(route('admin.festival-categories.award-categories.index', $category))
            ->assertForbidden();
        // Institutions
        $this->actingAs($jury)
            ->get(route('admin.institutions.index'))
            ->assertForbidden();

        // Innovations
        $this->actingAs($jury)
            ->get(route('admin.innovations.index'))
            ->assertForbidden();

        // Assessment Criteria
        $this->actingAs($jury)
            ->get(route('admin.assessment-criteria.index'))
            ->assertForbidden();

        // Juries
        $this->actingAs($jury)
            ->get(route('admin.juries.index'))
            ->assertForbidden();
    }
}
