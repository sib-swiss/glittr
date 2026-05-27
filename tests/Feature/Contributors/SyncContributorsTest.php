<?php

namespace Tests\Feature\Contributors;

use App\Actions\FetchContributorInfo;
use App\Actions\SyncContributors;
use App\Data\ContributorData;
use App\Jobs\FetchContributorOrcid;
use App\Jobs\FetchContributorsPage;
use App\Jobs\FinalizeContributorsSync;
use App\Jobs\SyncRepositoryContributors;
use App\Models\Contributor;
use App\Models\Repository;
use App\Remote\Drivers\GithubDriver;
use GrahamCampbell\GitHub\GitHubManager;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class SyncContributorsTest extends TestCase
{
    use RefreshDatabase;

    private function makeGithubRepository(): Repository
    {
        return Repository::factory()->create([
            'api' => 'github',
            'url' => 'https://github.com/test/repo',
            'enabled' => true,
        ]);
    }

    private function makeContributorData(array $overrides = []): ContributorData
    {
        return new ContributorData(
            remote_id: $overrides['remote_id'] ?? '12345',
            username: $overrides['username'] ?? 'octocat',
            full_name: array_key_exists('full_name', $overrides) ? $overrides['full_name'] : 'The Octocat',
            profile_url: $overrides['profile_url'] ?? 'https://github.com/octocat',
            avatar_url: $overrides['avatar_url'] ?? 'https://github.com/octocat.png',
            contributions: $overrides['contributions'] ?? 42,
        );
    }

    /**
     * Build a GitHubManager mock whose HTTP client returns the given pages.
     * Pages is a map of page number => array of contributor items.
     *
     * @param array<int, array<mixed>> $pages
     */
    private function mockGithubClientWithPages(Repository $repository, array $pages): GitHubManager
    {
        $httpClientMock = Mockery::mock();

        foreach ($pages as $page => $items) {
            $path = '/repos/test/repo/contributors?'
                . http_build_query(['per_page' => 100, 'page' => $page], '', '&', PHP_QUERY_RFC3986);

            $httpClientMock->shouldReceive('get')
                ->with($path)
                ->andReturn(new PsrResponse(200, ['Content-Type' => 'application/json'], json_encode($items)));
        }

        $clientMock = Mockery::mock(GitHubManager::class);
        $clientMock->shouldReceive('connection')->with('main')->andReturn($clientMock);
        $clientMock->shouldReceive('getHttpClient')->andReturn($httpClientMock);

        return $clientMock;
    }

    private function mockDriverForRepository(Repository $repository, array $contributors): void
    {
        $driver = $this->mock(GithubDriver::class);
        $driver->shouldReceive('setRepository')->andReturnSelf();
        $driver->shouldReceive('getContributors')->andReturn($contributors);

        $this->mock(\App\Remote\RemoteManager::class)
            ->shouldReceive('for')
            ->with($repository)
            ->andReturn($driver);
    }

    /** @test */
    public function it_creates_contributors_and_attaches_them_to_a_repository(): void
    {
        Queue::fake();

        $repository = $this->makeGithubRepository();
        $this->mockDriverForRepository($repository, [$this->makeContributorData()]);

        app(SyncContributors::class)->execute($repository);

        $this->assertDatabaseHas('contributors', [
            'remote_id' => '12345',
            'api' => 'github',
            'username' => 'octocat',
            'full_name' => 'The Octocat',
        ]);

        $contributor = Contributor::where('remote_id', '12345')->first();
        $this->assertNotNull($contributor);
        $this->assertTrue($repository->contributors->contains($contributor));
        $this->assertEquals(42, $repository->contributors()->find($contributor->id)->pivot->contributions);
    }

    /** @test */
    public function it_updates_existing_contributors_without_duplicating(): void
    {
        Queue::fake();

        $repository = $this->makeGithubRepository();

        Contributor::factory()->create([
            'remote_id' => '12345',
            'api' => 'github',
            'username' => 'octocat',
            'full_name' => 'Old Name',
        ]);

        $this->mockDriverForRepository($repository, [$this->makeContributorData(['full_name' => 'Updated Name'])]);

        app(SyncContributors::class)->execute($repository);

        $this->assertDatabaseCount('contributors', 1);
        $this->assertDatabaseHas('contributors', [
            'remote_id' => '12345',
            'full_name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function it_dispatches_fetch_orcid_job_for_contributors_without_orcid_fetched_at(): void
    {
        Queue::fake();

        $repository = $this->makeGithubRepository();
        $this->mockDriverForRepository($repository, [$this->makeContributorData()]);

        app(SyncContributors::class)->execute($repository);

        Queue::assertPushed(FetchContributorOrcid::class);
    }

    /** @test */
    public function it_dispatches_fetch_orcid_job_even_when_already_fetched(): void
    {
        Queue::fake();

        $repository = $this->makeGithubRepository();

        Contributor::factory()->create([
            'remote_id' => '12345',
            'api' => 'github',
            'username' => 'octocat',
            'orcid_fetched_at' => now(),
        ]);

        $this->mockDriverForRepository($repository, [$this->makeContributorData()]);

        app(SyncContributors::class)->execute($repository);

        Queue::assertPushed(FetchContributorOrcid::class);
    }

    /** @test */
    public function it_skips_non_github_repositories_gracefully(): void
    {
        Queue::fake();

        $repository = Repository::factory()->create([
            'api' => 'gitlab',
            'url' => 'https://gitlab.com/test/repo',
        ]);

        app(SyncContributors::class)->execute($repository);

        $this->assertDatabaseCount('contributors', 0);
        Queue::assertNotPushed(FetchContributorOrcid::class);
    }

    /** @test */
    public function fetch_orcid_job_saves_orcid_when_found_in_profile(): void
    {
        $contributor = Contributor::factory()->create([
            'username' => 'octocat',
            'orcid' => null,
            'orcid_fetched_at' => null,
        ]);

        Http::fake([
            'https://github.com/octocat' => Http::response(
                '<html><a href="https://orcid.org/0000-0002-1825-0097">ORCID</a></html>',
                200,
            ),
        ]);

        (new FetchContributorOrcid($contributor))->handle(new FetchContributorInfo());

        $this->assertDatabaseHas('contributors', [
            'id' => $contributor->id,
            'orcid' => '0000-0002-1825-0097',
        ]);
        $this->assertNotNull($contributor->fresh()->orcid_fetched_at);
    }

    /** @test */
    public function fetch_orcid_job_sets_fetched_at_even_when_no_orcid_found(): void
    {
        $contributor = Contributor::factory()->create([
            'username' => 'noctocat',
            'orcid' => null,
            'orcid_fetched_at' => null,
        ]);

        Http::fake([
            'https://github.com/noctocat' => Http::response('<html>No ORCID here</html>', 200),
        ]);

        (new FetchContributorOrcid($contributor))->handle(new FetchContributorInfo());

        $this->assertDatabaseHas('contributors', [
            'id' => $contributor->id,
            'orcid' => null,
        ]);
        $this->assertNotNull($contributor->fresh()->orcid_fetched_at);
    }

    /** @test */
    public function fetch_orcid_job_extracts_full_name_from_profile_when_missing(): void
    {
        $contributor = Contributor::factory()->create([
            'username' => 'octocat',
            'full_name' => null,
            'orcid_fetched_at' => null,
        ]);

        Http::fake([
            'https://github.com/octocat' => Http::response(
                '<html><span itemprop="name">The Octocat</span></html>',
                200,
            ),
        ]);

        (new FetchContributorOrcid($contributor))->handle(new FetchContributorInfo());

        $this->assertDatabaseHas('contributors', [
            'id' => $contributor->id,
            'full_name' => 'The Octocat',
        ]);
    }

    /** @test */
    public function fetch_orcid_job_overwrites_existing_full_name_with_updated_profile_name(): void
    {
        $contributor = Contributor::factory()->create([
            'username' => 'octocat',
            'full_name' => 'Original Name',
            'orcid_fetched_at' => null,
        ]);

        Http::fake([
            'https://github.com/octocat' => Http::response(
                '<html><span itemprop="name">Updated Name</span></html>',
                200,
            ),
        ]);

        (new FetchContributorOrcid($contributor))->handle(new FetchContributorInfo());

        $this->assertDatabaseHas('contributors', [
            'id' => $contributor->id,
            'full_name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function fetch_orcid_job_keeps_existing_full_name_when_no_name_span_found_in_profile(): void
    {
        $contributor = Contributor::factory()->create([
            'username' => 'octocat',
            'full_name' => 'Original Name',
            'orcid_fetched_at' => null,
        ]);

        Http::fake([
            'https://github.com/octocat' => Http::response('<html>No name span here</html>', 200),
        ]);

        (new FetchContributorOrcid($contributor))->handle(new FetchContributorInfo());

        $this->assertDatabaseHas('contributors', [
            'id' => $contributor->id,
            'full_name' => 'Original Name',
        ]);
    }

    /** @test */
    public function fetch_orcid_job_extracts_company_from_profile(): void
    {
        $contributor = Contributor::factory()->create([
            'username' => 'octocat',
            'company' => null,
            'orcid_fetched_at' => null,
        ]);

        Http::fake([
            'https://github.com/octocat' => Http::response(
                '<html><span class="p-org"><span>ACME Corp</span></span></html>',
                200,
            ),
        ]);

        (new FetchContributorOrcid($contributor))->handle(new FetchContributorInfo());

        $this->assertDatabaseHas('contributors', [
            'id' => $contributor->id,
            'company' => 'ACME Corp',
        ]);
    }

    /** @test */
    public function fetch_orcid_job_strips_at_prefix_from_github_org_company(): void
    {
        $contributor = Contributor::factory()->create([
            'username' => 'octocat',
            'company' => null,
            'orcid_fetched_at' => null,
        ]);

        Http::fake([
            'https://github.com/octocat' => Http::response(
                '<html><span class="p-org">@SomeOrg</span></html>',
                200,
            ),
        ]);

        (new FetchContributorOrcid($contributor))->handle(new FetchContributorInfo());

        $this->assertDatabaseHas('contributors', [
            'id' => $contributor->id,
            'company' => 'SomeOrg',
        ]);
    }

    /** @test */
    public function fetch_orcid_job_sets_company_to_null_when_not_found(): void
    {
        $contributor = Contributor::factory()->create([
            'username' => 'octocat',
            'company' => null,
            'orcid_fetched_at' => null,
        ]);

        Http::fake([
            'https://github.com/octocat' => Http::response('<html>No company here</html>', 200),
        ]);

        (new FetchContributorOrcid($contributor))->handle(new FetchContributorInfo());

        $this->assertDatabaseHas('contributors', [
            'id' => $contributor->id,
            'company' => null,
        ]);
    }

    /** @test */
    public function sync_contributors_does_not_overwrite_fetched_full_name_with_null(): void
    {
        Queue::fake();

        $repository = $this->makeGithubRepository();

        Contributor::factory()->create([
            'remote_id' => '12345',
            'api' => 'github',
            'username' => 'octocat',
            'full_name' => 'Name From Profile',
        ]);

        $this->mockDriverForRepository($repository, [
            $this->makeContributorData(['full_name' => null]),
        ]);

        app(SyncContributors::class)->execute($repository);

        $this->assertDatabaseHas('contributors', [
            'remote_id' => '12345',
            'full_name' => 'Name From Profile',
        ]);
    }

    /** @test */
    public function fetch_orcid_job_releases_itself_on_rate_limit_response(): void
    {
        $contributor = Contributor::factory()->create([
            'username' => 'octocat',
            'orcid_fetched_at' => null,
        ]);

        Http::fake([
            'https://github.com/octocat' => Http::response('', 429),
        ]);

        $job = $this->getMockBuilder(FetchContributorOrcid::class)
            ->setConstructorArgs([$contributor])
            ->onlyMethods(['release'])
            ->getMock();

        $job->expects($this->once())->method('release')->with(120);

        $job->handle(new FetchContributorInfo());

        $this->assertNull($contributor->fresh()->orcid_fetched_at);
    }

    /** @test */
    public function fetch_contributors_page_job_has_rate_limited_middleware(): void
    {
        Queue::fake();

        $repository = $this->makeGithubRepository();
        $job = new FetchContributorsPage($repository, 1);

        $middlewareClasses = array_map(fn ($m) => get_class($m), $job->middleware());
        $this->assertContains(RateLimited::class, $middlewareClasses);
    }

    /** @test */
    public function fetch_contributor_orcid_job_has_rate_limited_middleware(): void
    {
        $contributor = Contributor::factory()->create();
        $job = new FetchContributorOrcid($contributor);

        $middlewareClasses = array_map(fn ($m) => get_class($m), $job->middleware());
        $this->assertContains(RateLimited::class, $middlewareClasses);
    }

    /** @test */
    public function sync_contributors_command_dispatches_job_for_single_repository(): void
    {
        Queue::fake();

        $repository = $this->makeGithubRepository();

        $this->artisan('repo:sync-contributors', ['repository' => $repository->id])
            ->assertExitCode(0);

        Queue::assertPushed(SyncRepositoryContributors::class, fn ($job) => $job->repository->is($repository));
    }

    /** @test */
    public function sync_contributors_command_dispatches_job_for_all_repositories(): void
    {
        Queue::fake();

        $repo1 = $this->makeGithubRepository();
        $repo2 = $this->makeGithubRepository();

        $this->artisan('repo:sync-contributors')->assertExitCode(0);

        Queue::assertPushed(SyncRepositoryContributors::class, fn ($job) => $job->repository->is($repo1));
        Queue::assertPushed(SyncRepositoryContributors::class, fn ($job) => $job->repository->is($repo2));
    }

    /** @test */
    public function sync_repository_contributors_job_dispatches_first_page_job(): void
    {
        Queue::fake();

        $repository = $this->makeGithubRepository();

        (new SyncRepositoryContributors($repository))->handle();

        Queue::assertPushed(FetchContributorsPage::class, function ($job) use ($repository) {
            return $job->repository->is($repository)
                && $job->page === 1
                && $job->accumulated === [];
        });
    }

    /** @test */
    public function fetch_contributors_page_upserts_contributors_and_dispatches_next_page(): void
    {
        Queue::fake();

        $repository = $this->makeGithubRepository();

        $clientMock = $this->mockGithubClientWithPages($repository, [
            1 => [
                ['id' => 1, 'login' => 'alice', 'html_url' => 'https://github.com/alice', 'avatar_url' => null, 'contributions' => 10],
                ['id' => 2, 'login' => 'bob', 'html_url' => 'https://github.com/bob', 'avatar_url' => null, 'contributions' => 5],
            ],
        ]);

        $driver = new GithubDriver($clientMock, ['connection' => 'main']);
        $driver->setRepository($repository);

        $this->mock(\App\Remote\RemoteManager::class)
            ->shouldReceive('for')->with($repository)->andReturn($driver);

        (new FetchContributorsPage($repository, 1))->handle();

        $this->assertDatabaseHas('contributors', ['username' => 'alice']);
        $this->assertDatabaseHas('contributors', ['username' => 'bob']);

        Queue::assertPushed(FetchContributorsPage::class, function ($job) use ($repository) {
            return $job->repository->is($repository) && $job->page === 2 && count($job->accumulated) === 2;
        });
        Queue::assertNotPushed(FinalizeContributorsSync::class);
    }

    /** @test */
    public function fetch_contributors_page_dispatches_finalize_when_page_is_empty(): void
    {
        Queue::fake();

        $repository = $this->makeGithubRepository();

        $clientMock = $this->mockGithubClientWithPages($repository, [3 => []]);

        $driver = new GithubDriver($clientMock, ['connection' => 'main']);
        $driver->setRepository($repository);

        $this->mock(\App\Remote\RemoteManager::class)
            ->shouldReceive('for')->with($repository)->andReturn($driver);

        $accumulated = [42 => 10, 43 => 5];
        (new FetchContributorsPage($repository, 3, $accumulated))->handle();

        Queue::assertPushed(FinalizeContributorsSync::class, function ($job) use ($repository, $accumulated) {
            return $job->repository->is($repository) && $job->accumulated === $accumulated;
        });
        Queue::assertNotPushed(FetchContributorsPage::class);
    }

    /** @test */
    public function finalize_contributors_sync_attaches_contributors_and_dispatches_orcid_jobs(): void
    {
        Queue::fake();

        $repository = $this->makeGithubRepository();

        $c1 = Contributor::factory()->create(['orcid_fetched_at' => null]);
        $c2 = Contributor::factory()->create(['orcid_fetched_at' => now()]);

        $accumulated = [$c1->id => 20, $c2->id => 5];

        (new FinalizeContributorsSync($repository, $accumulated))->handle();

        $this->assertTrue($repository->contributors->contains($c1));
        $this->assertTrue($repository->contributors->contains($c2));
        $this->assertEquals(20, $repository->contributors()->find($c1->id)->pivot->contributions);
        $this->assertEquals(5, $repository->contributors()->find($c2->id)->pivot->contributions);

        Queue::assertPushed(FetchContributorOrcid::class, 2);
        Queue::assertPushed(FetchContributorOrcid::class, fn ($job) => $job->contributor->is($c1));
        Queue::assertPushed(FetchContributorOrcid::class, fn ($job) => $job->contributor->is($c2));
    }

    /** @test */
    public function refresh_command_dispatches_jobs_for_all_non_bot_contributors(): void
    {
        Queue::fake();

        Contributor::factory()->create(['username' => 'unfetched', 'orcid_fetched_at' => null]);
        Contributor::factory()->create(['username' => 'already-fetched', 'orcid_fetched_at' => now()]);

        $this->artisan('contributors:refresh')->assertExitCode(0);

        Queue::assertPushed(FetchContributorOrcid::class, 2);
        Queue::assertPushed(FetchContributorOrcid::class, fn ($job) => $job->contributor->username === 'unfetched');
        Queue::assertPushed(FetchContributorOrcid::class, fn ($job) => $job->contributor->username === 'already-fetched');
    }

    /** @test */
    public function refresh_command_skips_bots(): void
    {
        Queue::fake();

        Contributor::factory()->create([
            'username' => 'dependabot',
            'profile_url' => 'https://github.com/apps/dependabot',
            'orcid_fetched_at' => null,
        ]);

        $this->artisan('contributors:refresh')->assertExitCode(0);

        Queue::assertNotPushed(FetchContributorOrcid::class);
    }

    /** @test */
    public function refresh_command_dispatches_jobs_for_all_contributors_regardless_of_fetch_status(): void
    {
        Queue::fake();

        $c1 = Contributor::factory()->create(['orcid_fetched_at' => now()]);
        $c2 = Contributor::factory()->create(['orcid_fetched_at' => now()]);

        $this->artisan('contributors:refresh')->assertExitCode(0);

        Queue::assertPushed(FetchContributorOrcid::class, 2);
        Queue::assertPushed(FetchContributorOrcid::class, fn ($job) => $job->contributor->is($c1));
        Queue::assertPushed(FetchContributorOrcid::class, fn ($job) => $job->contributor->is($c2));
    }

    /** @test */
    public function get_contributors_skips_anonymous_and_bot_entries(): void
    {
        Queue::fake();

        $repository = Repository::factory()->create([
            'api' => 'github',
            'url' => 'https://github.com/test/repo',
        ]);

        $clientMock = $this->mockGithubClientWithPages($repository, [
            1 => [
                ['id' => 42, 'login' => 'octocat', 'type' => 'User', 'html_url' => 'https://github.com/octocat', 'avatar_url' => null, 'contributions' => 10],
                ['type' => 'Anonymous', 'name' => 'Ghost', 'email' => 'ghost@example.com', 'contributions' => 3],
                ['id' => 99, 'login' => 'dependabot[bot]', 'type' => 'Bot', 'html_url' => 'https://github.com/apps/dependabot', 'avatar_url' => null, 'contributions' => 1],
            ],
            2 => [],
        ]);

        $driver = new GithubDriver($clientMock, ['connection' => 'main']);
        $driver->setRepository($repository);

        $contributors = $driver->getContributors();

        $this->assertCount(1, $contributors);
        $this->assertEquals('octocat', $contributors[0]->username);
    }

    /** @test */
    public function finalize_contributors_sync_populates_contributor_names_ordered_by_contributions(): void
    {
        Queue::fake();

        $repository = $this->makeGithubRepository();

        $c1 = Contributor::factory()->create([
            'username' => 'alice',
            'full_name' => 'Alice Smith',
            'profile_url' => 'https://github.com/alice',
            'orcid_fetched_at' => now(),
        ]);
        $c2 = Contributor::factory()->create([
            'username' => 'bob',
            'full_name' => null,
            'profile_url' => 'https://github.com/bob',
            'orcid_fetched_at' => now(),
        ]);

        $accumulated = [$c1->id => 5, $c2->id => 20];

        (new FinalizeContributorsSync($repository, $accumulated))->handle();

        $this->assertDatabaseHas('repositories', [
            'id' => $repository->id,
            'contributor_names' => 'bob, Alice Smith',
        ]);
    }

    /** @test */
    public function finalize_contributors_sync_excludes_bots_from_contributor_names(): void
    {
        Queue::fake();

        $repository = $this->makeGithubRepository();

        $human = Contributor::factory()->create([
            'username' => 'alice',
            'full_name' => 'Alice Smith',
            'profile_url' => 'https://github.com/alice',
            'orcid_fetched_at' => now(),
        ]);
        $bot = Contributor::factory()->create([
            'username' => 'dependabot',
            'full_name' => null,
            'profile_url' => 'https://github.com/apps/dependabot',
            'orcid_fetched_at' => now(),
        ]);

        $accumulated = [$human->id => 10, $bot->id => 50];

        (new FinalizeContributorsSync($repository, $accumulated))->handle();

        $this->assertDatabaseHas('repositories', [
            'id' => $repository->id,
            'contributor_names' => 'Alice Smith',
        ]);
    }

    /** @test */
    public function sync_contributors_action_populates_contributor_names(): void
    {
        Queue::fake();

        $repository = $this->makeGithubRepository();

        $this->mockDriverForRepository($repository, [
            $this->makeContributorData(['username' => 'alice', 'full_name' => 'Alice Smith', 'contributions' => 42]),
        ]);

        app(SyncContributors::class)->execute($repository);

        $this->assertDatabaseHas('repositories', [
            'id' => $repository->id,
            'contributor_names' => 'Alice Smith',
        ]);
    }

    /** @test */
    public function repository_search_scope_matches_contributor_names(): void
    {
        Queue::fake();

        $repository = $this->makeGithubRepository();
        $repository->update(['contributor_names' => 'Alice Smith, Bob Jones']);

        $other = $this->makeGithubRepository();
        $other->update(['contributor_names' => 'Charlie Brown']);

        $results = \App\Models\Repository::enabled()->search('alice')->get();

        $this->assertTrue($results->contains($repository));
        $this->assertFalse($results->contains($other));
    }
}
