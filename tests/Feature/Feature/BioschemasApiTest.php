<?php

namespace Tests\Feature\Feature;

use App\Models\Author;
use App\Models\Contributor;
use App\Models\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BioschemasApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeRepository(array $overrides = []): Repository
    {
        return Repository::factory()->create(array_merge([
            'api' => 'github',
            'url' => 'https://github.com/test/repo',
            'enabled' => true,
            'description' => 'A test repo',
        ], $overrides));
    }

    /** @test */
    public function bioschemas_endpoint_returns_paginated_json(): void
    {
        Queue::fake();
        $this->makeRepository();

        $response = $this->getJson(route('api.bioschemas'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            'links' => ['first', 'last', 'prev', 'next'],
        ]);
    }

    /** @test */
    public function bioschemas_endpoint_defaults_to_25_per_page(): void
    {
        Queue::fake();
        Repository::factory()->count(30)->create(['enabled' => true]);

        $response = $this->getJson(route('api.bioschemas'));

        $response->assertOk();
        $response->assertJsonPath('meta.per_page', 25);
        $this->assertCount(25, $response->json('data'));
    }

    /** @test */
    public function bioschemas_endpoint_respects_per_page_parameter(): void
    {
        Queue::fake();
        Repository::factory()->count(10)->create(['enabled' => true]);

        $response = $this->getJson(route('api.bioschemas') . '?per_page=5');

        $response->assertOk();
        $response->assertJsonPath('meta.per_page', 5);
        $this->assertCount(5, $response->json('data'));
    }

    /** @test */
    public function bioschemas_endpoint_caps_per_page_at_50(): void
    {
        Queue::fake();

        $response = $this->getJson(route('api.bioschemas') . '?per_page=999');

        $response->assertOk();
        $response->assertJsonPath('meta.per_page', 50);
    }

    /** @test */
    public function bioschemas_endpoint_paginates_correctly(): void
    {
        Queue::fake();
        Repository::factory()->count(10)->create(['enabled' => true]);

        $page1 = $this->getJson(route('api.bioschemas') . '?per_page=6&page=1');
        $page2 = $this->getJson(route('api.bioschemas') . '?per_page=6&page=2');

        $page1->assertJsonPath('meta.current_page', 1);
        $page2->assertJsonPath('meta.current_page', 2);
        $this->assertCount(6, $page1->json('data'));
        $this->assertCount(4, $page2->json('data'));
        $this->assertNotNull($page1->json('links.next'));
        $this->assertNull($page2->json('links.next'));
    }

    /** @test */
    public function bioschemas_endpoint_excludes_disabled_repositories(): void
    {
        Queue::fake();
        $this->makeRepository(['enabled' => true]);
        $this->makeRepository(['enabled' => false]);

        $response = $this->getJson(route('api.bioschemas'));

        $response->assertOk();
        $response->assertJsonPath('meta.total', 1);
    }

    /** @test */
    public function bioschemas_endpoint_includes_contributors(): void
    {
        Queue::fake();
        $repository = $this->makeRepository();

        $contributor = Contributor::factory()->create([
            'username' => 'alice',
            'full_name' => 'Alice Smith',
            'profile_url' => 'https://github.com/alice',
            'avatar_url' => 'https://avatars.github.com/alice',
            'company' => 'ACME Corp',
            'orcid' => '0000-0002-1825-0097',
        ]);
        $repository->contributors()->attach([$contributor->id => ['contributions' => 10]]);

        $response = $this->getJson(route('api.bioschemas'));

        $response->assertOk();
        $item = $response->json('data.0');

        $this->assertArrayHasKey('contributor', $item);
        $alice = collect($item['contributor'])->firstWhere('@id', 'https://orcid.org/0000-0002-1825-0097');
        $this->assertEquals('Alice Smith', $alice['name']);
        $this->assertEquals('https://github.com/alice', $alice['sameAs']);
        $this->assertEquals('https://avatars.github.com/alice', $alice['image']);
        $this->assertEquals('ACME Corp', $alice['affiliation']['name']);
    }

    /** @test */
    public function bioschemas_endpoint_uses_orcid_as_author_id_when_contributor_matches(): void
    {
        Queue::fake();

        $author = Author::factory()->create([
            'type' => 'Person',
            'name' => 'alice',
            'url' => 'https://github.com/alice',
        ]);
        $repository = $this->makeRepository();
        $repository->author()->associate($author)->save();

        $contributor = Contributor::factory()->create([
            'profile_url' => 'https://github.com/alice',
            'orcid' => '0000-0002-1825-0097',
        ]);
        $repository->contributors()->attach([$contributor->id => ['contributions' => 5]]);

        $response = $this->getJson(route('api.bioschemas'));

        $response->assertOk();
        $item = $response->json('data.0');

        $authorNode = $item['author'][0];
        $this->assertEquals('https://orcid.org/0000-0002-1825-0097', $authorNode['@id']);
        $this->assertContains('https://github.com/alice', (array) $authorNode['sameAs']);
    }

    /** @test */
    public function bioschemas_endpoint_includes_schema_context_in_each_item(): void
    {
        Queue::fake();
        $this->makeRepository();

        $response = $this->getJson(route('api.bioschemas'));

        $response->assertOk();
        $item = $response->json('data.0');
        $this->assertEquals('https://schema.org', $item['@context']);
        $this->assertEquals('LearningResource', $item['@type']);
    }
}
