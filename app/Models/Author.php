<?php

namespace App\Models;

use App\Casts\Url;
use Artesaos\SEOTools\JsonLd;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'api',
        'remote_id',
        'url',
        'name',
        'display_name',
        'location',
        'type',
        'company',
        'email',
        'bio',
        'avatar_url',
        'twitter_username',
        'website',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'url' => Url::class,
        'website' => Url::class,
    ];

    public function repositories()
    {
        return $this->hasMany(Repository::class);
    }

    protected static function booted(): void
    {
        static::saved(function (Author $author) {
            $author->repositories()->select('id')->each(
                fn (Repository $repository) => Cache::forget($repository->jsonLdCacheKey())
            );
        });
    }

    public function getSlugAttribute(): string
    {
        return $this->api === 'gitlab'
            ? 'gitlab-' . strtolower($this->name)
            : strtolower($this->name);
    }

    public static function findBySlug(string $slug): ?static
    {
        if (str_starts_with($slug, 'gitlab-')) {
            $name = substr($slug, 7);

            return static::where('api', 'gitlab')
                ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                ->first();
        }

        return static::where('api', '!=', 'gitlab')
            ->whereRaw('LOWER(name) = ?', [strtolower($slug)])
            ->first();
    }

    public function getJsonLd(): array
    {
        $isOrg = $this->type === 'Organization';

        $node = (new JsonLd())->setType($isOrg ? 'Organization' : 'Person');

        $node->addValue('name', $this->display_name ?: $this->name);

        if ($this->url) {
            $node->addValue('@id', (string) $this->url);
        }

        if ($this->email) {
            $node->addValue('email', $this->email);
        }

        if ($this->bio) {
            $node->addValue('description', $this->bio);
        }

        if ($this->avatar_url) {
            $node->addValue('image', $this->avatar_url);
        }

        if ($this->website && (string) $this->website !== '') {
            $node->addValue('url', (string) $this->website);
        }

        $sameAs = [];
        if ($this->twitter_username) {
            $sameAs[] = 'https://twitter.com/' . $this->twitter_username;
        }
        if (! empty($sameAs)) {
            $node->addValue('sameAs', $sameAs);
        }

        if (! $isOrg && $this->company) {
            $node->addValue('affiliation', ['@type' => 'Organization', 'name' => $this->company]);
        }

        return [$node];
    }
}
