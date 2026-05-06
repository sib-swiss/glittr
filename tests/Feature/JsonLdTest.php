<?php

namespace Tests\Feature;

use App\Jobs\FinalizeContributorsSync;
use App\Models\Author;
use App\Models\Contributor;
use App\Models\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class JsonLdTest extends TestCase
{
    use RefreshDatabase;

    private function makeRepository(): Repository
    {
        return Repository::factory()->create([
            'api' => 'github',
            'name' => 'test/repo',
            'url' => 'https://github.com/test/repo',
            'enabled' => true,
            'description' => 'A test repo',
        ]);
    }

    /** @test */
    public function get_json_ld_array_returns_correct_context_and_type(): void
    {
        Queue::fake();
        $repository = $this->makeRepository();

        $jsonLd = $repository->getJsonLdArray();

        $this->assertEquals('https://schema.org', $jsonLd['@context']);
        $this->assertEquals('LearningResource', $jsonLd['@type']);
    }

    /** @test */
    public function get_json_ld_array_includes_contributors_as_persons(): void
    {
        Queue::fake();
        $repository = $this->makeRepository();

        $c1 = Contributor::factory()->create([
            'username' => 'alice',
            'full_name' => 'Alice Smith',
            'profile_url' => 'https://github.com/alice',
            'orcid' => '0000-0002-1825-0097',
        ]);
        $c2 = Contributor::factory()->create([
            'username' => 'bob',
            'full_name' => null,
            'profile_url' => 'https://github.com/bob',
            'orcid' => null,
        ]);

        $repository->contributors()->attach([$c1->id => ['contributions' => 10], $c2->id => ['contributions' => 5]]);

        $jsonLd = $repository->getJsonLdArray();

        $this->assertArrayHasKey('contributor', $jsonLd);
        $contributors = $jsonLd['contributor'];
        $this->assertCount(2, $contributors);

        $alice = collect($contributors)->firstWhere('@id', 'https://github.com/alice');
        $this->assertEquals('Person', $alice['@type']);
        $this->assertEquals('Alice Smith', $alice['name']);
        $this->assertEquals('https://orcid.org/0000-0002-1825-0097', $alice['sameAs']);

        $bob = collect($contributors)->firstWhere('@id', 'https://github.com/bob');
        $this->assertEquals('Person', $bob['@type']);
        $this->assertEquals('bob', $bob['name']);
        $this->assertArrayNotHasKey('sameAs', $bob);
    }

    /** @test */
    public function get_json_ld_array_is_cached(): void
    {
        Queue::fake();
        $repository = $this->makeRepository();

        $this->assertNull(Cache::get($repository->jsonLdCacheKey()));

        $repository->getJsonLdArray();

        $this->assertNotNull(Cache::get($repository->jsonLdCacheKey()));
    }

    /** @test */
    public function cache_is_invalidated_when_repository_is_saved(): void
    {
        Queue::fake();
        $repository = $this->makeRepository();
        $repository->getJsonLdArray();

        $this->assertNotNull(Cache::get($repository->jsonLdCacheKey()));

        $repository->touch();

        $this->assertNull(Cache::get($repository->jsonLdCacheKey()));
    }

    /** @test */
    public function cache_is_invalidated_after_finalize_contributors_sync(): void
    {
        Queue::fake();
        $repository = $this->makeRepository();
        $repository->getJsonLdArray();

        $this->assertNotNull(Cache::get($repository->jsonLdCacheKey()));

        $contributor = Contributor::factory()->create(['orcid_fetched_at' => now()]);
        (new FinalizeContributorsSync($repository, [$contributor->id => 3]))->handle();

        $this->assertNull(Cache::get($repository->jsonLdCacheKey()));
    }

    /** @test */
    public function author_json_ld_includes_enriched_fields(): void
    {
        Queue::fake();

        $author = Author::factory()->create([
            'type' => 'Person',
            'name' => 'alice',
            'display_name' => 'Alice Smith',
            'url' => 'https://github.com/alice',
            'email' => 'alice@example.com',
            'bio' => 'A developer.',
            'avatar_url' => 'https://avatars.github.com/alice',
            'website' => 'https://alice.dev',
            'twitter_username' => 'alicedev',
            'company' => 'ACME Corp',
        ]);

        $nodes = $author->getJsonLd();
        $data = $nodes[0]->convertToArray();

        $this->assertEquals('Person', $data['@type']);
        $this->assertEquals('https://github.com/alice', $data['@id']);
        $this->assertEquals('Alice Smith', $data['name']);
        $this->assertEquals('alice@example.com', $data['email']);
        $this->assertEquals('A developer.', $data['description']);
        $this->assertEquals('https://avatars.github.com/alice', $data['image']);
        $this->assertEquals('https://alice.dev', $data['url']);
        $this->assertContains('https://twitter.com/alicedev', $data['sameAs']);
        $this->assertNotContains('https://alice.dev', $data['sameAs']);
        $this->assertEquals('Organization', $data['affiliation']['@type']);
        $this->assertEquals('ACME Corp', $data['affiliation']['name']);
        $this->assertCount(1, $nodes);
    }

    /** @test */
    public function author_json_ld_omits_empty_fields(): void
    {
        Queue::fake();

        $author = Author::factory()->create([
            'type' => 'Person',
            'display_name' => 'Bob',
            'url' => null,
            'email' => null,
            'bio' => null,
            'avatar_url' => null,
            'website' => null,
            'twitter_username' => null,
            'company' => null,
        ]);

        $data = $author->getJsonLd()[0]->convertToArray();

        $this->assertEquals('Person', $data['@type']);
        $this->assertArrayNotHasKey('@id', $data);
        $this->assertArrayNotHasKey('email', $data);
        $this->assertArrayNotHasKey('description', $data);
        $this->assertArrayNotHasKey('image', $data);
        $this->assertArrayNotHasKey('url', $data);
        $this->assertArrayNotHasKey('sameAs', $data);
        $this->assertArrayNotHasKey('affiliation', $data);
    }

    /** @test */
    public function cache_is_invalidated_when_author_is_saved(): void
    {
        Queue::fake();

        $repository = $this->makeRepository();
        $repository->getJsonLdArray();
        $this->assertNotNull(Cache::get($repository->jsonLdCacheKey()));

        $author = Author::factory()->create();
        $repository->author()->associate($author)->save();
        Cache::put($repository->jsonLdCacheKey(), ['cached' => true]);

        $author->touch();

        $this->assertNull(Cache::get($repository->jsonLdCacheKey()));
    }

    /** @test */
    public function repository_detail_page_includes_json_ld_in_head(): void
    {
        Queue::fake();
        $repository = $this->makeRepository();

        $contributor = Contributor::factory()->create([
            'username' => 'alice',
            'profile_url' => 'https://github.com/alice',
            'orcid' => '0000-0002-1825-0097',
        ]);
        $repository->contributors()->attach([$contributor->id => ['contributions' => 5]]);

        $response = $this->get(route('repository', $repository->route_params));

        $response->assertStatus(200);
        $response->assertSee('application/ld+json', false);
        $response->assertSee('https://github.com/alice', false);
        $response->assertSee('https://orcid.org/0000-0002-1825-0097', false);
    }
}
