<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\StartsWith;
use Spatie\LaravelData\Data;

class SubmissionData extends Data
{
    public function __construct(
        #[StartsWith('http://', 'https://')]
        public ?string $url,
        #[ArrayType]
        public ?array $tags,
        public string $name,
        public string $email,
        public ?string $comment,
    ) {
    }
}
