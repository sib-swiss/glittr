<?php

namespace App\Data;

use App\Models\Category;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class CategoryData extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $color,
        public ?int $order_column,
    ) {
    }
}
