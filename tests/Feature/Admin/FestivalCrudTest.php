<?php

namespace Tests\Feature\Admin;

use App\Models\Festival;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FestivalCrudTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function superadmin_can_view_festivals_index(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        Festival::factory()->count(3)->create();

        $response = $this->actingAs($superadmin)->get(route('admin.festivals.index'));

        $response->assertOk();
        $response->assertViewIs('admin.festivals.index');
    }

    #[Test]
    public function superadmin_can_create_festival(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $response = $this->actingAs($superadmin)->post(route('admin.festivals.store'), [
            'year' => 2024,
            'name' => 'Festival Inovasi 2024',
        ]);

        $response->assertRedirect(route('admin.festivals.index'));
        $this->assertDatabaseHas('festivals', [
            'year' => 2024,
            'name' => 'Festival Inovasi 2024',
        ]);
    }

    #[Test]
    public function festival_year_must_be_unique(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        Festival::factory()->create(['year' => 2024]);

        $response = $this->actingAs($superadmin)->post(route('admin.festivals.store'), [
            'year' => 2024,
            'name' => 'Another Festival 2024',
        ]);

        $response->assertSessionHasErrors('year');
    }

    #[Test]
    public function superadmin_can_update_festival_name_and_status(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $festival = Festival::factory()->create(['name' => 'Old Name', 'status' => \App\Enums\FestivalStatus::Preparation]);

        $response = $this->actingAs($superadmin)->put(route('admin.festivals.update', $festival), [
            'name' => 'New Name',
            'status' => 'ongoing',
        ]);

        $response->assertRedirect(route('admin.festivals.index'));
        $this->assertEquals('New Name', $festival->fresh()->name);
        $this->assertEquals(\App\Enums\FestivalStatus::Ongoing, $festival->fresh()->status);
    }

    #[Test]
    public function jury_cannot_access_festival_crud(): void
    {
        $jury = User::factory()->jury()->create();
        $festival = Festival::factory()->create();

        $this->actingAs($jury)->get(route('admin.festivals.index'))->assertForbidden();
        $this->actingAs($jury)->get(route('admin.festivals.create'))->assertForbidden();
        $this->actingAs($jury)->post(route('admin.festivals.store'), [])->assertForbidden();
        $this->actingAs($jury)->get(route('admin.festivals.edit', $festival))->assertForbidden();
        $this->actingAs($jury)->put(route('admin.festivals.update', $festival), [])->assertForbidden();
    }

    #[Test]
    public function guest_cannot_access_festival_crud(): void
    {
        $festival = Festival::factory()->create();

        $this->get(route('admin.festivals.index'))->assertRedirect(route('login'));
        $this->get(route('admin.festivals.create'))->assertRedirect(route('login'));
        $this->post(route('admin.festivals.store'), [])->assertRedirect(route('login'));
        $this->get(route('admin.festivals.edit', $festival))->assertRedirect(route('login'));
        $this->put(route('admin.festivals.update', $festival), [])->assertRedirect(route('login'));
    }

    #[Test]
    public function festival_requires_name(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $response = $this->actingAs($superadmin)->post(route('admin.festivals.store'), [
            'year' => 2024,
        ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function festival_requires_year(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $response = $this->actingAs($superadmin)->post(route('admin.festivals.store'), [
            'name' => 'Festival 2024',
        ]);

        $response->assertSessionHasErrors('year');
    }

    #[Test]
    public function superadmin_can_view_festivals_index_with_modal_elements(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $festival = Festival::factory()->create(['name' => 'Festival Inovasi Daerah']);

        $response = $this->actingAs($superadmin)->get(route('admin.festivals.index'));

        $response->assertOk();
        $response->assertSee('festival-modal');
        $response->assertSee('delete-festival-modal');
        $response->assertSee('openCreate');
        $response->assertSee('openEdit');
        $response->assertSee('openDelete');
    }

    #[Test]
    public function superadmin_can_create_festival_via_ajax(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $response = $this->actingAs($superadmin)->postJson(route('admin.festivals.store'), [
            'year' => 2027,
            'name' => 'Festival Inovasi 2027',
            'organizer_name' => 'Bappeda',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Festival berhasil dibuat.',
        ]);
        $this->assertDatabaseHas('festivals', [
            'year' => 2027,
            'name' => 'Festival Inovasi 2027',
        ]);
    }

    #[Test]
    public function superadmin_can_update_festival_via_ajax(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $festival = Festival::factory()->create(['name' => 'Old Festival Name']);

        $response = $this->actingAs($superadmin)->putJson(route('admin.festivals.update', $festival), [
            'name' => 'Updated Festival Name',
            'status' => 'ongoing',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Festival berhasil diperbarui.',
        ]);
        $this->assertEquals('Updated Festival Name', $festival->fresh()->name);
    }

    #[Test]
    public function superadmin_can_delete_festival_without_relations(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $festival = Festival::factory()->create();

        $response = $this->actingAs($superadmin)->delete(route('admin.festivals.destroy', $festival));

        $response->assertRedirect(route('admin.festivals.index'));
        $this->assertDatabaseMissing('festivals', ['id' => $festival->id]);
    }

    #[Test]
    public function superadmin_cannot_delete_festival_with_categories(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $festival = Festival::factory()->create();
        \App\Models\FestivalCategory::factory()->create(['festival_id' => $festival->id]);

        $response = $this->actingAs($superadmin)->delete(route('admin.festivals.destroy', $festival));

        $response->assertRedirect(route('admin.festivals.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('festivals', ['id' => $festival->id]);
    }

    #[Test]
    public function superadmin_can_delete_festival_via_ajax(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $festival = Festival::factory()->create();

        $response = $this->actingAs($superadmin)->deleteJson(route('admin.festivals.destroy', $festival));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Festival berhasil dihapus.',
        ]);
        $this->assertDatabaseMissing('festivals', ['id' => $festival->id]);
    }

    #[Test]
    public function jury_cannot_delete_festival(): void
    {
        $jury = User::factory()->jury()->create();
        $festival = Festival::factory()->create();

        $this->actingAs($jury)->delete(route('admin.festivals.destroy', $festival))->assertForbidden();
    }

    #[Test]
    public function guest_cannot_delete_festival(): void
    {
        $festival = Festival::factory()->create();

        $this->delete(route('admin.festivals.destroy', $festival))->assertRedirect(route('login'));
    }
}
