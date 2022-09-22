<?php

namespace App\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Date;
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
        #[Date]
        public CarbonImmutable|Optional $last_push,
    ) {
    }

    /**
     * Map data retrieved from github
     *
     * @param array $repoData
     * @param array $userData
     * @return static
     */
    public static function fromGithub(array $repoData): static
    {
        return new self(
            name: $repoData['full_name'] ?? '',
            website: $repoData['homepage'] ?? '',
            stargazers: isset($repoData['stargazers_count']) ? intval($repoData['stargazers_count']) : Optional::create(),
            title: $repoData['name'] ?? '',
            description: $repoData['description'] ?? '',
            license: isset($repoData['license']['name']) && $repoData['license']['name'] ? $repoData['license']['name'] : Optional::create(),
            last_push: isset($repoData['pushed_at']) && $repoData['pushed_at'] ? CarbonImmutable::parse($repoData['pushed_at']) : Optional::create(),
        );
    }
}
