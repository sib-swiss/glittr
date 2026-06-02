<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ContributorData extends Data
{
    public function __construct(
        public string $remote_id,
        public string $username,
        public ?string $full_name,
        public string $profile_url,
        public ?string $avatar_url,
        public int $contributions,
    ) {
    }

    public static function fromGithub(array $data): static
    {
        return new self(
            remote_id: strval($data['id']),
            username: $data['login'],
            full_name: $data['name'] ?? null,
            profile_url: $data['html_url'],
            avatar_url: $data['avatar_url'] ?? null,
            contributions: $data['contributions'],
        );
    }
}
