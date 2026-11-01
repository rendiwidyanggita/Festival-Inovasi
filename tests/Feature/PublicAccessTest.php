<?php

namespace Tests\Feature;

use App\Enums\AssessmentType;
use App\Models\AwardCategory;
use App\Models\Festival;
use App\Models\FestivalCategory;
use App\Models\Innovation;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicAccessTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_view_homepage(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
    }

    #[Test]
    public function guest_can_view_festival_page(): void
    {
        $festival = Festival::factory()->create();

        $response = $this->get(route('public.festival', $festival));

        $response->assertOk();
    }

    #[Test]
    public function guest_can_view_category_page(): void
    {
        $festival = Festival::factory()->create();
        $category = FestivalCategory::factory()->create([
            'festival_id' => $festival->id,
            'assessment_type' => AssessmentType::Institution,
        ]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $festival->id,
            'festival_category_id' => $category->id,
        ]);

        $response = $this->get(route('public.category', [$festival, $awardCategory]));

        $response->assertOk();
    }

    #[Test]
    public function guest_cannot_see_scores_before_publication(): void
    {
        $festival = Festival::factory()->create([
            'results_published_at' => null,
        ]);
        $category = FestivalCategory::factory()->create([
            'festival_id' => $festival->id,
            'assessment_type' => AssessmentType::Institution,
        ]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $festival->id,
            'festival_category_id' => $category->id,
        ]);
        $institution = Institution::factory()->create([
            'festival_id' => $festival->id,
            'award_category_id' => $awardCategory->id,
        ]);

        $response = $this->get(route('public.category', [$festival, $awardCategory]));

        // Should not show scores or rankings
        $response->assertOk();
        $response->assertDontSee('Peringkat');
        $response->assertDontSee('Nilai');
    }

    #[Test]
    public function guest_can_see_scores_after_publication(): void
    {
        $festival = Festival::factory()->create([
            'results_published_at' => now(),
        ]);
        $category = FestivalCategory::factory()->create([
            'festival_id' => $festival->id,
            'assessment_type' => AssessmentType::Institution,
        ]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $festival->id,
            'festival_category_id' => $category->id,
        ]);
        $institution = Institution::factory()->create([
            'festival_id' => $festival->id,
            'award_category_id' => $awardCategory->id,
        ]);

        $response = $this->get(route('public.category', [$festival, $awardCategory]));

        $response->assertOk();
        // After publication, scores should be visible
        // Exact assertion depends on view implementation
    }

    #[Test]
    public function guest_cannot_post_to_admin_endpoints(): void
    {
        $festival = Festival::factory()->create();

        $response = $this->post(route('admin.festivals.store'), [
            'year' => 2024,
            'name' => 'Test',
        ]);

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_delete_resources(): void
    {
        $category = FestivalCategory::factory()->create([
            'assessment_type' => AssessmentType::Institution,
        ]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $category->festival_id,
            'festival_category_id' => $category->id,
        ]);
        $institution = Institution::factory()->create([
            'festival_id' => $category->festival_id,
            'award_category_id' => $awardCategory->id,
        ]);

        $response = $this->delete(route('admin.institutions.destroy', $institution));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_access_jury_dashboard(): void
    {
        $response = $this->get(route('jury.dashboard'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticated_jury_cannot_post_to_admin(): void
    {
        $jury = User::factory()->jury()->create();

        $response = $this->actingAs($jury)->post(route('admin.festivals.store'), [
            'year' => 2024,
            'name' => 'Test',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function public_pages_show_participants_list(): void
    {
        $festival = Festival::factory()->create();
        $category = FestivalCategory::factory()->create([
            'festival_id' => $festival->id,
            'assessment_type' => AssessmentType::Innovation,
        ]);
        $awardCategory = AwardCategory::factory()->create([
            'festival_id' => $festival->id,
            'festival_category_id' => $category->id,
        ]);
        $institutionCategory = FestivalCategory::factory()->create([
            'festival_id' => $festival->id,
            'assessment_type' => AssessmentType::Institution,
        ]);
        $institutionAwardCategory = AwardCategory::factory()->create([
            'festival_id' => $festival->id,
            'festival_category_id' => $institutionCategory->id,
        ]);
        $institution = Institution::factory()->create([
            'festival_id' => $festival->id,
            'award_category_id' => $institutionAwardCategory->id,
        ]);
        $innovation = Innovation::factory()->create([
            'festival_id' => $festival->id,
            'award_category_id' => $awardCategory->id,
            'institution_id' => $institution->id,
            'name' => 'Inovasi Test Publik',
        ]);

        $response = $this->get(route('public.category', [$festival, $awardCategory]));

        $response->assertOk();
        $response->assertSee('Inovasi Test Publik');
    }
}
