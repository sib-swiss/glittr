<?php

namespace Tests\Feature\Feature;

use App\Models\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepositoryLegacyRedirectTest extends TestCase
{
    use RefreshDatabase;

    // ── /repository/{id} redirect ────────────────────────────────────────

    public function test_legacy_id_route_redirects_to_slug_with_301(): void
    {
        $repo = Repository::factory()->create(['enabled' => true, 'name' => 'user/myrepo']);

        $this->get("/repository/{$repo->id}")
            ->assertRedirect(route('repository', $repo->route_params))
            ->assertStatus(301);
    }

    public function test_legacy_id_route_returns_404_for_unknown_id(): void
    {
        $this->get('/repository/99999')->assertStatus(404);
    }

    public function test_legacy_id_route_still_redirects_for_disabled_repository(): void
    {
        $repo = Repository::factory()->create(['enabled' => false, 'name' => 'user/disabled']);

        // The legacy route always redirects; the slug destination will 404.
        $this->get("/repository/{$repo->id}")
            ->assertRedirect(route('repository', $repo->route_params))
            ->assertStatus(301);
    }

    // ── Apicuron entity_uri uses /repository/{id} ────────────────────────

    public function test_apicuron_entity_uri_uses_legacy_id_route(): void
    {
        $repo = Repository::factory()->create(['enabled' => true, 'name' => 'user/myrepo']);

        $expectedUri = route('repository.legacy', $repo);

        $this->assertStringContainsString("/repository/{$repo->id}", $expectedUri);
    }
}
