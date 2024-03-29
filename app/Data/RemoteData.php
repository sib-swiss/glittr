<?php

namespace App\Data;

use App\Utils;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class RemoteData extends Data
{
    public function __construct(
        public string|Optional $name,
        public string|Optional $website,
        public int|Optional $stargazers,
        public string|Optional $title,
        public string|Optional $description,
        public string|Optional $license,
        public string|Optional $author_id,
        #[Date]
        public CarbonImmutable|Optional $last_push,
        #[Date]
        public CarbonImmutable|Optional $repository_created_at,
        #[Date]
        public CarbonImmutable|Optional $repository_updated_at,
        public string|Optional $version,
        #[Date]
        public CarbonImmutable|Optional $version_published_at,
    ) {
    }

    /**
     * Map data retrieved from github api
     */
    public static function fromGithub(array $repoData): static
    {
        return new self(
            name: $repoData['full_name'] ?? '',
            website: $repoData['homepage'] ? Utils::ensureUrl($repoData['homepage']) : Optional::create(), // May be manually added if not present
            stargazers: isset($repoData['stargazers_count']) ? intval($repoData['stargazers_count']) : Optional::create(),
            title: $repoData['name'] ?? '',
            description: $repoData['description'] ?? '',
            license: isset($repoData['license']['key']) && $repoData['license']['key'] ? $repoData['license']['key'] : Optional::create(),
            author_id: isset($repoData['owner']['id']) ? $repoData['owner']['id'] : Optional::create(),
            last_push: isset($repoData['pushed_at']) && $repoData['pushed_at'] ? CarbonImmutable::parse($repoData['pushed_at']) : Optional::create(),
            repository_created_at: isset($repoData['created_at']) && $repoData['created_at'] ? CarbonImmutable::parse($repoData['created_at']) : Optional::create(),
            repository_updated_at: isset($repoData['updated_at']) && $repoData['updated_at'] ? CarbonImmutable::parse($repoData['updated_at']) : Optional::create(),
            version: $repoData['version'] ?? Optional::create(),
            version_published_at: isset($repoData['version_published_at']) && $repoData['version_published_at'] ? CarbonImmutable::parse($repoData['version_published_at']) : Optional::create(),
        );
    }

    /**
     * Map data retrieved from gitlab api
     */
    public static function fromGitLab(array $repoData): static
    {
        return new self(
            name: $repoData['path_with_namespace'] ?? '',
            website: Optional::create(), //not available don't include to avoid manual setup override
            stargazers: isset($repoData['star_count']) ? intval($repoData['star_count']) : Optional::create(),
            title: $repoData['name'] ?? '',
            description: $repoData['description'] ?? '',
            license: Optional::create(),
            author_id: isset($repoData['owner']['id']) ? $repoData['owner']['id'] : Optional::create(),
            last_push: isset($repoData['last_activity_at']) && $repoData['last_activity_at'] ? CarbonImmutable::parse($repoData['last_activity_at']) : Optional::create(), //last activity? not found better...
            repository_created_at: isset($repoData['created_at']) && $repoData['created_at'] ? CarbonImmutable::parse($repoData['created_at']) : Optional::create(),
            repository_updated_at: isset($repoData['updated_at']) && $repoData['updated_at'] ? CarbonImmutable::parse($repoData['updated_at']) : Optional::create(),
            version: $repoData['version'] ?? Optional::create(),
            version_published_at: isset($repoData['version_published_at']) && $repoData['version_published_at'] ? CarbonImmutable::parse($repoData['version_published_at']) : Optional::create(),
        );
    }
}
