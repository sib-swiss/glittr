<?php

namespace Tests\Feature\Livewire\Admin;

use App\Jobs\SyncRepositoryContributors;
use App\Livewire\Admin\RepositoriesList;
use App\Models\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class RepositoriesListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(RepositoriesList::class);

        $component->assertStatus(200);
    }

    /** @test */
    public function it_renders_without_error_when_a_repository_has_no_name()
    {
        Queue::fake();
        Repository::factory()->create(['name' => null, 'url' => 'https://github.com/test/repo']);

        $component = Livewire::test(RepositoriesList::class);

        $component->assertStatus(200);
    }

    /** @test */
    public function it_dispatches_contributor_sync_for_a_github_repository()
    {
        Queue::fake();
        $repository = Repository::factory()->create(['name' => null, 'url' => 'https://github.com/test/repo']);
        Queue::fake();

        Livewire::test(RepositoriesList::class)
            ->call('syncContributors', $repository->id)
            ->assertHasNoErrors();

        Queue::assertPushed(
            SyncRepositoryContributors::class,
            fn ($job) => $job->repository->is($repository),
        );
    }

    /** @test */
    public function it_does_not_dispatch_contributor_sync_for_a_non_github_repository()
    {
        Queue::fake();
        $repository = Repository::factory()->create(['name' => null, 'url' => 'https://gitlab.com/test/repo']);

        Livewire::test(RepositoriesList::class)
            ->call('syncContributors', $repository->id);

        Queue::assertNotPushed(SyncRepositoryContributors::class);
    }
}
