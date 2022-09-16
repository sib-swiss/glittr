<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Spatie\Url\Url as SpatieUrl;

class Url implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?SpatieUrl
    {
        return isset($value) ? SpatieUrl::fromString($value) : null;
    }

    public function set($model, string $key, $value, array $attributes): ?string
    {
        return isset($value) ? (string) $value : null;
    }
}
