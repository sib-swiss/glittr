<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function sitemap_returns_xml_with_correct_content_type(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringStartsWith('text/xml', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function sitemap_includes_static_pages(): void
    {
        $response = $this->get('/sitemap.xml');

        $content = $response->getContent();

        $this->assertStringContainsString(url('/'), $content);
        $this->assertStringContainsString(url('/contribute'), $content);
        $this->assertStringContainsString(url('/terms-of-use'), $content);
    }

    /** @test */
    public function sitemap_includes_enabled_repositories(): void
    {
        Queue::fake();

        Repository::factory()->create([
            'api' => 'github',
            'name' => 'acme/my-repo',
            'url' => 'https://github.com/acme/my-repo',
            'enabled' => true,
        ]);

        $response = $this->get('/sitemap.xml');

        $this->assertStringContainsString('acme/my-repo', $response->getContent());
    }

    /** @test */
    public function sitemap_excludes_disabled_repositories(): void
    {
        Queue::fake();

        Repository::factory()->create([
            'api' => 'github',
            'name' => 'acme/hidden-repo',
            'url' => 'https://github.com/acme/hidden-repo',
            'enabled' => false,
        ]);

        $response = $this->get('/sitemap.xml');

        $this->assertStringNotContainsString('hidden-repo', $response->getContent());
    }

    /** @test */
    public function sitemap_includes_authors_with_enabled_repositories(): void
    {
        Queue::fake();

        $author = Author::factory()->create([
            'api' => 'github',
            'name' => 'glittrauthor',
        ]);

        Repository::factory()->create([
            'api' => 'github',
            'name' => 'glittrauthor/some-repo',
            'url' => 'https://github.com/glittrauthor/some-repo',
            'enabled' => true,
            'author_id' => $author->id,
        ]);

        $response = $this->get('/sitemap.xml');

        $this->assertStringContainsString('glittrauthor', $response->getContent());
    }

    /** @test */
    public function sitemap_excludes_authors_with_no_enabled_repositories(): void
    {
        Queue::fake();

        $author = Author::factory()->create([
            'api' => 'github',
            'name' => 'ghostauthor',
        ]);

        Repository::factory()->create([
            'api' => 'github',
            'name' => 'ghostauthor/hidden-repo',
            'url' => 'https://github.com/ghostauthor/hidden-repo',
            'enabled' => false,
            'author_id' => $author->id,
        ]);

        $response = $this->get('/sitemap.xml');

        $this->assertStringNotContainsString('ghostauthor', $response->getContent());
    }

    /** @test */
    public function sitemap_includes_repository_lastmod_when_available(): void
    {
        Queue::fake();

        Repository::factory()->create([
            'api' => 'github',
            'name' => 'acme/dated-repo',
            'url' => 'https://github.com/acme/dated-repo',
            'enabled' => true,
            'repository_updated_at' => '2025-01-15 10:00:00',
        ]);

        $response = $this->get('/sitemap.xml');

        $this->assertStringContainsString('2025-01-15', $response->getContent());
    }
}
