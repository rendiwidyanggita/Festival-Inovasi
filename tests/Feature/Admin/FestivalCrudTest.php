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
    public function superadmin_can_update_festival_name(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $festival = Festival::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($superadmin)->put(route('admin.festivals.update', $festival), [
            'name' => 'New Name',
        ]);

        $response->assertRedirect(route('admin.festivals.index'));
        $this->assertEquals('New Name', $festival->fresh()->name);
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
}
