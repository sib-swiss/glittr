<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Data;

class TagData extends Data
{
    public function __construct(
        public ?int $id,
        public int $category_id,
        public string $name,
        public ?int $order_column,
        public ?int $ontology_id,
        public ?string $ontology_class,
        #[Url]
        public ?string $link,
        public ?string $description,
    ) {
    }
}
