<?php

namespace App\Data;

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
        return new self(
            remote_id: isset($userData['id']) ? strval($userData['id']) : null,
            name: $userData['login'] ?? '',
            url: $userData['html_url'] ?? '',
            display_name: $userData['name'] ?? '',
            location: $userData['location'] ?? '',
            type: $userData['type'] ?? '',
            company:  $userData['company'] ?? '',
            email: $userData['email'] ?? '',
            bio: $userData['bio'] ?? '',
            avatar_url: $userData['avatar_url'] ?? '',
            twitter_username: $userData['twitter_username'] ?? '',
            website: $userData['blog'] ?? '',
        );
    }

    public static function fromGitLab(array $userData): static
    {
        return new self(
            remote_id: isset($userData['id']) ? strval($userData['id']) : null,
            name: $userData['username'] ?? '',
            url: $userData['web_url'] ?? '',
            display_name: $userData['name'] ?? '',
            location: $userData['location'] ?? '',
            type: Optional::create(), // not provided?
            company:  $userData['organization'] ?? '',
            email: $userData['public_email'] ?? '',
            bio: $userData['bio'] ?? '',
            avatar_url: $userData['avatar_url'] ?? '',
            twitter_username: $userData['twitter'] ?? '',
            website: $userData['website_url'] ?? '',
        );
    }
}
