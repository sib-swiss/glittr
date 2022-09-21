<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class TagData extends Data
{
    public function __construct(
        public ?int $id,
        public int $category_id,
        public string $name,
        public ?int $order_column,
    ) {
    }
}
