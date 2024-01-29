<?php

namespace App\Data;

use App\Utils;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class AuthorData extends Data
{
    public function __construct(
        public ?string $remote_id,
        public string $name,
        public string|Optional $url,
        public string|Optional $display_name,
        public string|Optional $location,
        public string|Optional $type,
        public string|Optional $company,
        public string|Optional $email,
        public string|Optional $bio,
        public string|Optional $avatar_url,
        public string|Optional $twitter_username,
        public string|Optional $website,
    ) {
    }

    public static function fromGithub(array $userData): static
    {
        $name = $userData['name'] ?? $userData['login'];

        return new self(
            remote_id: isset($userData['id']) ? strval($userData['id']) : null,
            name: $userData['login'],
            url: $userData['html_url'] ?? '',
            display_name: $name ?? '',
            location: $userData['location'] ?? '',
            type: $userData['type'] ?? '',
            company: $userData['company'] ?? '',
            email: $userData['email'] ?? '',
            bio: $userData['bio'] ?? '',
            avatar_url: $userData['avatar_url'] ?? '',
            twitter_username: $userData['twitter_username'] ?? '',
            website: $userData['blog'] ? Utils::ensureUrl($userData['blog']) : '',
        );
    }

    public static function fromGitLabUser(array $userData): static
    {
        return new self(
            remote_id: isset($userData['id']) ? strval($userData['id']) : null,
            name: $userData['username'] ?? '',
            url: $userData['web_url'] ?? '',
            display_name: $userData['name'] ?? '',
            location: $userData['location'] ?? '',
            type: 'user',
            company: $userData['organization'] ?? '',
            email: $userData['public_email'] ?? '',
            bio: $userData['bio'] ?? '',
            avatar_url: $userData['avatar_url'] ?? '',
            twitter_username: $userData['twitter'] ?? '',
            website: $userData['website_url'] ? Utils::ensureUrl($userData['website_url']) : '',
        );
    }

    public static function fromGitLabGroup(array $userData): static
    {
        return new self(
            remote_id: isset($userData['id']) ? strval($userData['id']) : null,
            name: $userData['name'] ?? '',
            url: $userData['web_url'] ?? '',
            display_name: $userData['full_name'] ?? '',
            location: '',
            type: 'group', // not provided?
            company: '',
            email: '',
            bio: $userData['description'] ?? '',
            avatar_url: '',
            twitter_username: '',
            website: '',
        );
    }
}
