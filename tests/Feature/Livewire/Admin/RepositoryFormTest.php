<?php

namespace Tests\Feature\Livewire\Admin;

use App\Jobs\SyncRepositoryContributors;
use App\Livewire\Admin\RepositoryForm;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class RepositoryFormTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render(): void
    {
        $component = Livewire::test(RepositoryForm::class, ['id' => null, 'cancelEvent' => null]);

        $component->assertStatus(200);
    }

    /** @test */
    public function adding_a_github_repository_dispatches_contributor_sync(): void
    {
        Queue::fake();

        $tag = Tag::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(RepositoryForm::class, ['id' => null, 'cancelEvent' => null])
            ->set('repository.url', 'https://github.com/test/repo')
            ->set('repository.tags', [$tag->id])
            ->call('save');

        Queue::assertPushed(SyncRepositoryContributors::class);
    }

    /** @test */
    public function adding_a_non_github_repository_does_not_dispatch_contributor_sync(): void
    {
        Queue::fake();

        $tag = Tag::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(RepositoryForm::class, ['id' => null, 'cancelEvent' => null])
            ->set('repository.url', 'https://gitlab.com/test/repo')
            ->set('repository.tags', [$tag->id])
            ->call('save');

        Queue::assertNotPushed(SyncRepositoryContributors::class);
    }
}
