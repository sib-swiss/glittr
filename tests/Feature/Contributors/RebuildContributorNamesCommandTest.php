<?php

namespace Tests\Feature\Contributors;

use App\Models\Contributor;
use App\Models\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RebuildContributorNamesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_rebuilds_contributor_names_from_current_contributors(): void
    {
        $repository = Repository::factory()->create(['contributor_names' => null]);

        $contributor = Contributor::factory()->create(['full_name' => 'Alice Smith', 'is_bot' => false]);
        $repository->contributors()->attach($contributor->id, ['contributions' => 10]);

        $this->artisan('contributors:rebuild-names')
            ->assertExitCode(0);

        $this->assertStringContainsString('Alice Smith', $repository->fresh()->contributor_names);
    }

    public function test_uses_username_when_full_name_is_null(): void
    {
        $repository = Repository::factory()->create(['contributor_names' => null]);

        $contributor = Contributor::factory()->create(['full_name' => null, 'username' => 'alice123', 'is_bot' => false]);
        $repository->contributors()->attach($contributor->id, ['contributions' => 5]);

        $this->artisan('contributors:rebuild-names')
            ->assertExitCode(0);

        $this->assertStringContainsString('alice123', $repository->fresh()->contributor_names);
    }

    public function test_excludes_bots(): void
    {
        $repository = Repository::factory()->create(['contributor_names' => null]);

        $bot = Contributor::factory()->create(['username' => 'dependabot', 'is_bot' => true]);
        $repository->contributors()->attach($bot->id, ['contributions' => 100]);

        $this->artisan('contributors:rebuild-names')
            ->assertExitCode(0);

        $this->assertNull($repository->fresh()->contributor_names);
    }

    public function test_overwrites_stale_names(): void
    {
        $repository = Repository::factory()->create(['contributor_names' => 'Old Name']);

        $contributor = Contributor::factory()->create(['full_name' => 'New Name', 'is_bot' => false]);
        $repository->contributors()->attach($contributor->id, ['contributions' => 1]);

        $this->artisan('contributors:rebuild-names')
            ->assertExitCode(0);

        $this->assertSame('New Name', $repository->fresh()->contributor_names);
    }
}
