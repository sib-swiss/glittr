<?php

namespace Tests\Feature\Admin;

use App\Actions\FetchContributorInfo;
use App\Livewire\Admin\ContributorsList;
use App\Models\Contributor;
use App\Models\Repository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContributorsListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_view_the_contributors_index_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.contributors.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(ContributorsList::class);
    }

    /** @test */
    public function the_component_can_render(): void
    {
        Livewire::test(ContributorsList::class)->assertStatus(200);
    }

    /** @test */
    public function search_filter_matches_contributors_by_username(): void
    {
        Contributor::factory()->create(['username' => 'alice-dev']);
        Contributor::factory()->create(['username' => 'bob-coder']);

        Livewire::test(ContributorsList::class)
            ->set('search', 'alice')
            ->assertSee('alice-dev')
            ->assertDontSee('bob-coder');
    }

    /** @test */
    public function search_filter_matches_contributors_by_full_name(): void
    {
        Contributor::factory()->create(['username' => 'user-a', 'full_name' => 'Alice Wonderland']);
        Contributor::factory()->create(['username' => 'user-b', 'full_name' => 'Bob Builder']);

        Livewire::test(ContributorsList::class)
            ->set('search', 'Wonderland')
            ->assertSee('user-a')
            ->assertDontSee('user-b');
    }

    /** @test */
    public function search_filter_matches_contributors_by_orcid(): void
    {
        Contributor::factory()->create(['username' => 'with-orcid', 'orcid' => '0000-0001-2345-6789']);
        Contributor::factory()->create(['username' => 'without-orcid', 'orcid' => null]);

        Livewire::test(ContributorsList::class)
            ->set('search', '0000-0001-2345-6789')
            ->assertSee('with-orcid')
            ->assertDontSee('without-orcid');
    }

    /** @test */
    public function repository_filter_limits_contributors_to_a_repository(): void
    {
        $repositoryA = Repository::factory()->create();
        $repositoryB = Repository::factory()->create();

        $contributorA = Contributor::factory()->create(['username' => 'in-repo-a']);
        $contributorB = Contributor::factory()->create(['username' => 'in-repo-b']);

        $repositoryA->contributors()->attach($contributorA, ['contributions' => 1]);
        $repositoryB->contributors()->attach($contributorB, ['contributions' => 1]);

        Livewire::test(ContributorsList::class)
            ->set('repositoryFilter', (string) $repositoryA->id)
            ->assertSee('in-repo-a')
            ->assertDontSee('in-repo-b');
    }

    /** @test */
    public function repository_select_displays_repository_name_slug(): void
    {
        $repository = Repository::factory()->create(['name' => 'owner/my-repo']);

        Livewire::test(ContributorsList::class)
            ->assertSee('owner/my-repo');
    }

    /** @test */
    public function repository_select_excludes_repositories_without_a_name(): void
    {
        Repository::factory()->create(['name' => null]);
        Repository::factory()->create(['name' => '']);
        $named = Repository::factory()->create(['name' => 'owner/named-repo']);

        $component = Livewire::test(ContributorsList::class);

        $viewData = $component->viewData('repositories');
        $ids = $viewData->pluck('id')->all();

        $this->assertContains($named->id, $ids);
        $this->assertCount(1, $ids);
    }

    /** @test */
    public function orcid_fetched_at_is_shown_in_table_when_set(): void
    {
        Contributor::factory()->create([
            'username' => 'with-date',
            'orcid_fetched_at' => '2025-03-15 10:30:00',
        ]);

        Livewire::test(ContributorsList::class)
            ->assertSee('2025-03-15 10:30');
    }

    /** @test */
    public function sort_by_repositories_desc_orders_by_count_descending(): void
    {
        $many = Contributor::factory()->create(['username' => 'many-repos']);
        $few = Contributor::factory()->create(['username' => 'few-repos']);
        $repos = Repository::factory()->count(3)->create();
        $repos->each(fn ($r) => $r->contributors()->attach($many, ['contributions' => 1]));
        $repos->first()->contributors()->attach($few, ['contributions' => 1]);

        $component = Livewire::test(ContributorsList::class)
            ->set('sortBy', 'repositories_desc');

        $usernames = $component->viewData('contributors')->pluck('username')->all();
        $this->assertTrue(array_search('many-repos', $usernames) < array_search('few-repos', $usernames));
    }

    /** @test */
    public function sort_by_bot_desc_puts_flagged_contributors_first(): void
    {
        Contributor::factory()->create(['username' => 'not-flagged', 'is_bot' => false]);
        Contributor::factory()->create(['username' => 'flagged', 'is_bot' => true]);

        $component = Livewire::test(ContributorsList::class)
            ->set('sortBy', 'bot_desc');

        $usernames = $component->viewData('contributors')->pluck('username')->all();
        $this->assertEquals('flagged', $usernames[0]);
    }

    /** @test */
    public function select_all_only_selects_contributors_on_the_current_page(): void
    {
        Contributor::factory()->count(30)->create();

        Livewire::test(ContributorsList::class)
            ->set('selectAll', true)
            ->assertSet('selectedIds', fn ($ids) => count($ids) <= 25);
    }

    /** @test */
    public function github_app_badge_is_shown_for_apps_profiles(): void
    {
        Contributor::factory()->create([
            'username' => 'dependabot',
            'profile_url' => 'https://github.com/apps/dependabot',
        ]);

        Livewire::test(ContributorsList::class)
            ->assertSee('GitHub App');
    }

    /** @test */
    public function manual_badge_is_shown_for_manually_flagged_bot(): void
    {
        Contributor::factory()->create([
            'username' => 'manual-bot',
            'is_bot' => true,
            'profile_url' => 'https://github.com/manual-bot',
        ]);

        Livewire::test(ContributorsList::class)
            ->assertSee('Manual');
    }

    /** @test */
    public function toggle_bot_flags_and_unflags_a_contributor(): void
    {
        $contributor = Contributor::factory()->create([
            'username' => 'maybe-bot',
            'is_bot' => false,
        ]);

        Livewire::test(ContributorsList::class)
            ->call('toggleBot', $contributor->id);

        $this->assertTrue($contributor->fresh()->is_bot);

        Livewire::test(ContributorsList::class)
            ->call('toggleBot', $contributor->id);

        $this->assertFalse($contributor->fresh()->is_bot);
    }

    /** @test */
    public function fetch_info_calls_action_and_notifies_on_success(): void
    {
        $contributor = Contributor::factory()->create(['username' => 'some-user']);

        $this->mock(FetchContributorInfo::class)
            ->expects('execute')
            ->with(\Mockery::on(fn ($c) => $c->id === $contributor->id))
            ->andReturn(true);

        Livewire::test(ContributorsList::class)
            ->call('fetchInfo', $contributor->id)
            ->assertDispatched('notify');
    }

    /** @test */
    public function fetch_info_shows_error_notification_on_rate_limit(): void
    {
        $contributor = Contributor::factory()->create(['username' => 'some-user']);

        $this->mock(FetchContributorInfo::class)
            ->expects('execute')
            ->andReturn(false);

        Livewire::test(ContributorsList::class)
            ->call('fetchInfo', $contributor->id)
            ->assertDispatched('notify', type: 'error');
    }

    /** @test */
    public function bulk_flag_as_bots_flags_selected_contributors(): void
    {
        $a = Contributor::factory()->create(['is_bot' => false]);
        $b = Contributor::factory()->create(['is_bot' => false]);
        $c = Contributor::factory()->create(['is_bot' => false]);

        Livewire::test(ContributorsList::class)
            ->set('selectedIds', [(string) $a->id, (string) $b->id])
            ->call('bulkFlagAsBots');

        $this->assertTrue($a->fresh()->is_bot);
        $this->assertTrue($b->fresh()->is_bot);
        $this->assertFalse($c->fresh()->is_bot);
    }

    /** @test */
    public function bulk_unflag_as_bots_unflags_selected_contributors(): void
    {
        $a = Contributor::factory()->create(['is_bot' => true]);
        $b = Contributor::factory()->create(['is_bot' => true]);

        Livewire::test(ContributorsList::class)
            ->set('selectedIds', [(string) $a->id, (string) $b->id])
            ->call('bulkUnflagAsBots');

        $this->assertFalse($a->fresh()->is_bot);
        $this->assertFalse($b->fresh()->is_bot);
    }

    /** @test */
    public function bulk_flag_clears_selection_after_action(): void
    {
        $contributor = Contributor::factory()->create(['is_bot' => false]);

        Livewire::test(ContributorsList::class)
            ->set('selectedIds', [(string) $contributor->id])
            ->call('bulkFlagAsBots')
            ->assertSet('selectedIds', [])
            ->assertSet('selectAll', false);
    }

    /** @test */
    public function bulk_fetch_info_calls_action_for_each_selected_contributor(): void
    {
        $a = Contributor::factory()->create();
        $b = Contributor::factory()->create();

        $this->mock(FetchContributorInfo::class)
            ->expects('execute')
            ->twice()
            ->andReturn(true);

        Livewire::test(ContributorsList::class)
            ->set('selectedIds', [(string) $a->id, (string) $b->id])
            ->call('bulkFetchInfo');
    }

    /** @test */
    public function select_all_populates_selected_ids_with_contributors_on_current_page(): void
    {
        $contributors = Contributor::factory()->count(3)->create();

        Livewire::test(ContributorsList::class)
            ->set('selectAll', true)
            ->assertSet('selectedIds', $contributors->sortBy('username')->pluck('id')->map(fn ($id) => (string) $id)->values()->all());
    }

    /** @test */
    public function updated_search_resets_pagination_and_clears_selection(): void
    {
        $contributor = Contributor::factory()->create();

        Livewire::test(ContributorsList::class)
            ->set('selectedIds', [(string) $contributor->id])
            ->set('search', 'something')
            ->assertSet('selectedIds', []);
    }

    /** @test */
    public function updated_repository_filter_resets_pagination_and_clears_selection(): void
    {
        $repository = Repository::factory()->create();
        $contributor = Contributor::factory()->create();

        Livewire::test(ContributorsList::class)
            ->set('selectedIds', [(string) $contributor->id])
            ->set('repositoryFilter', (string) $repository->id)
            ->assertSet('selectedIds', []);
    }

    /** @test */
    public function excluding_bots_scope_excludes_both_url_and_manually_flagged_bots(): void
    {
        $human = Contributor::factory()->create([
            'username' => 'human',
            'profile_url' => 'https://github.com/human',
            'is_bot' => false,
        ]);

        $urlBot = Contributor::factory()->create([
            'username' => 'dependabot',
            'profile_url' => 'https://github.com/apps/dependabot',
            'is_bot' => false,
        ]);

        $manualBot = Contributor::factory()->create([
            'username' => 'manual-bot',
            'profile_url' => 'https://github.com/manual-bot',
            'is_bot' => true,
        ]);

        $ids = Contributor::query()->excludingBots()->pluck('id')->all();

        $this->assertContains($human->id, $ids);
        $this->assertNotContains($urlBot->id, $ids);
        $this->assertNotContains($manualBot->id, $ids);
    }
}
