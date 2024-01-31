<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Data;

class OntologyData extends Data
{
    public function __construct(
        public string $name,
        #[Url]
        public ?string $term_set,
    ) {
    }
}
