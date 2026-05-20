<?php

namespace Tests\Feature\Contributors;

use App\Jobs\FetchContributorOrcid;
use App\Models\Contributor;
use App\Models\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FetchContributorOrcidTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_clears_repository_json_ld_cache_when_orcid_is_found(): void
    {
        $repository = Repository::factory()->create();
        $contributor = Contributor::factory()->create([
            'username' => 'octocat',
            'orcid' => null,
            'orcid_fetched_at' => null,
        ]);
        $repository->contributors()->attach($contributor, ['contributions' => 1]);

        Cache::forever($repository->jsonLdCacheKey(), ['cached' => 'schema']);

        Http::fake([
            'https://github.com/octocat' => Http::response(
                '<html><a href="https://orcid.org/0000-0002-1825-0097">ORCID</a></html>',
                200,
            ),
        ]);

        (new FetchContributorOrcid($contributor))->handle();

        $this->assertNull(Cache::get($repository->jsonLdCacheKey()));
    }

    /** @test */
    public function it_does_not_clear_repository_json_ld_cache_when_no_orcid_is_found(): void
    {
        $repository = Repository::factory()->create();
        $contributor = Contributor::factory()->create([
            'username' => 'noctocat',
            'orcid' => null,
            'orcid_fetched_at' => null,
        ]);
        $repository->contributors()->attach($contributor, ['contributions' => 1]);

        Cache::forever($repository->jsonLdCacheKey(), ['cached' => 'schema']);

        Http::fake([
            'https://github.com/noctocat' => Http::response('<html>No ORCID here</html>', 200),
        ]);

        (new FetchContributorOrcid($contributor))->handle();

        $this->assertEquals(['cached' => 'schema'], Cache::get($repository->jsonLdCacheKey()));
    }
}
