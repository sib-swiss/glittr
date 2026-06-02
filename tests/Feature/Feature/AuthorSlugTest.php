<?php

namespace Tests\Feature\Feature;

use App\Models\Author;
use App\Models\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorSlugTest extends TestCase
{
    use RefreshDatabase;

    // ── Author::getSlugAttribute ─────────────────────────────────────────

    public function test_github_author_slug_is_lowercase_name(): void
    {
        $author = Author::factory()->make(['api' => 'github', 'name' => 'JakeVdP']);

        $this->assertSame('jakevdp', $author->slug);
    }

    public function test_gitlab_author_slug_has_gitlab_prefix(): void
    {
        $author = Author::factory()->make(['api' => 'gitlab', 'name' => 'SomeUser']);

        $this->assertSame('gitlab-someuser', $author->slug);
    }

    // ── Author::findBySlug ───────────────────────────────────────────────

    public function test_find_github_author_by_slug(): void
    {
        $author = Author::factory()->create(['api' => 'github', 'name' => 'jakevdp']);

        $found = Author::findBySlug('jakevdp');

        $this->assertNotNull($found);
        $this->assertTrue($author->is($found));
    }

    public function test_find_github_author_by_slug_is_case_insensitive(): void
    {
        $author = Author::factory()->create(['api' => 'github', 'name' => 'JakeVdP']);

        $found = Author::findBySlug('jakevdp');

        $this->assertNotNull($found);
        $this->assertTrue($author->is($found));
    }

    public function test_find_gitlab_author_by_slug(): void
    {
        $author = Author::factory()->create(['api' => 'gitlab', 'name' => 'someuser']);

        $found = Author::findBySlug('gitlab-someuser');

        $this->assertNotNull($found);
        $this->assertTrue($author->is($found));
    }

    public function test_find_gitlab_author_does_not_match_without_prefix(): void
    {
        Author::factory()->create(['api' => 'gitlab', 'name' => 'someuser']);

        $found = Author::findBySlug('someuser');

        $this->assertNull($found);
    }

    public function test_find_github_author_does_not_match_gitlab_prefix_slug(): void
    {
        Author::factory()->create(['api' => 'github', 'name' => 'someuser']);

        $found = Author::findBySlug('gitlab-someuser');

        $this->assertNull($found);
    }

    public function test_findbyslug_returns_null_for_unknown_slug(): void
    {
        $this->assertNull(Author::findBySlug('nobody-here'));
    }

    // ── Author page route ────────────────────────────────────────────────

    public function test_author_page_returns_200_for_known_github_author(): void
    {
        $author = Author::factory()->create(['api' => 'github', 'name' => 'jakevdp']);

        $this->get('/jakevdp')->assertStatus(200);
    }

    public function test_author_page_returns_200_for_known_gitlab_author(): void
    {
        $author = Author::factory()->create(['api' => 'gitlab', 'name' => 'someuser']);

        $this->get('/gitlab-someuser')->assertStatus(200);
    }

    public function test_author_page_returns_404_for_unknown_slug(): void
    {
        $this->get('/nobody-here')->assertStatus(404);
    }

    public function test_author_page_shows_author_display_name(): void
    {
        $author = Author::factory()->create([
            'api' => 'github',
            'name' => 'jakevdp',
            'display_name' => 'Jake VanderPlas',
        ]);

        $this->get('/jakevdp')->assertSee('Jake VanderPlas');
    }

    public function test_author_page_shows_only_that_authors_repositories(): void
    {
        $author = Author::factory()->create(['api' => 'github', 'name' => 'jakevdp']);
        $other = Author::factory()->create(['api' => 'github', 'name' => 'other']);

        Repository::factory()->create(['author_id' => $author->id, 'enabled' => true, 'name' => 'jakevdp/myrepo']);
        Repository::factory()->create(['author_id' => $other->id, 'enabled' => true, 'name' => 'other/otherrepo']);

        $this->get('/jakevdp')->assertSee('jakevdp/myrepo')->assertDontSee('other/otherrepo');
    }

    // ── Repository list: name links to detail page ───────────────────────

    public function test_repository_name_in_list_links_to_detail_page(): void
    {
        $repo = Repository::factory()->create(['enabled' => true, 'name' => 'test/repo']);

        $response = $this->get('/');

        $response->assertSee(route('repository', $repo->route_params), false);
    }

    // ── Repository model: readme fillable ────────────────────────────────

    public function test_repository_readme_is_fillable(): void
    {
        $repo = Repository::factory()->create(['readme' => '# Hello']);

        $this->assertSame('# Hello', $repo->fresh()->readme);
    }

    // ── Author::slug does not break for empty api ────────────────────────

    public function test_non_gitlab_api_author_has_no_prefix(): void
    {
        $author = Author::factory()->make(['api' => 'github', 'name' => 'testuser']);

        $this->assertStringNotContainsString('gitlab-', $author->slug);
    }
}
