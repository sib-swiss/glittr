<?php

namespace App\Models;

use App\Casts\Url;
use App\Facades\Remote;
use App\Jobs\UpdateRepositoryData;
use Artesaos\SEOTools\JsonLd;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


class Repository extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'website',
        'stargazers',
        'title',
        'description',
        'license',
        'last_push',
        'url',
        'author_id',
        'refreshed_at',
        'repository_created_at',
        'repository_updated_at',
        'version',
        'version_published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'url' => Url::class,
        'website' => Url::class,
        'stargazers' => 'integer',
        'enabled' => 'boolean',
        'last_push' => 'datetime:Y-m-d H:i:s',
        'refreshed_at' => 'datetime:Y-m-d H:i:s',
        'repository_created_at' => 'datetime:Y-m-d H:i:s',
        'repository_updated_at' => 'datetime:Y-m-d H:i:s',
        'version_published_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->using(RepositoryTag::class);
    }

    public function getPushStatusClass()
    {
        if ($this->last_push) {
            $days = $this->last_push->diff(Carbon::now())->days;
            if ($days <= 30) {
                return 'bg-green-400';
            } elseif ($days <= 90) {
                return 'bg-yellow-400';
            } elseif ($days <= 365) {
                return 'bg-orange-400';
            } else {
                return 'bg-red-400';
            }
        }

        return 'bg-gray-400';
    }

    public function scopeEnabled(Builder $query): void
    {
        $query->where('enabled', true);
    }

    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function ($query) use ($search) {
            $terms = explode(' ', $search);
            foreach ($terms as $term) {
                $query->where(function ($query) use ($term) {
                    $query
                        ->where('repositories.url', 'like', '%'.$term.'%')
                        ->orwhere('repositories.name', 'like', '%'.$term.'%')
                        ->orWhere('repositories.description', 'like', '%'.$term.'%')
                        ->orWhere('repositories.license', 'like', '%'.$term.'%')
                        ->orWhereHas('author', function (Builder $query) use ($term) {
                            $query->where('name', 'like', '%'.$term.'%')
                                ->orWhere('display_name', 'like', '%'.$term.'%');
                        })
                        ->orWhereHas('tags', function (Builder $query) use ($term) {
                            $query->where('name', 'like', '%'.$term.'%');
                        });
                });
            }
        });
    }

    public function scopeOrderedBy(Builder $query, string $column, string $direction = 'asc'): void
    {
        if ($column == 'author') {
            $query->select('repositories.*');
            $query->join('authors', 'repositories.author_id', '=', 'authors.id');
            $column = 'authors.display_name';
        }

        if ($column == 'last_push') {
            // Number of days so invert the direction (datetime)
            $direction = $direction == 'asc' ? 'desc' : 'asc';
        }

        $query->orderBy($column, $direction);
    }

    public function getMainCategoryAttribute(): ?Tag
    {
        return $this->tags?->first();
    }

    public function getDaysSinceLastPushAttribute(): ?int
    {
        return $this->last_push?->diffInDays();
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        /**
         * Automatic api resolve from url
         */
        static::creating(function (Repository $repository) {
            if (! $repository->api || empty($repository->api)) {
                $repository->api = Remote::resolveAPI($repository);
            }
        });

        static::created(function (Repository $repository) {
            UpdateRepositoryData::dispatch($repository);
        });

        static::saved(function (Repository $repository) {
            Cache::tags('repositories')->flush();
        });

        /**
         * If url changed the author might have changed need to recheck
         */
        static::updating(function (Repository $repository) {
            $dirty = $repository->getDirty();
            if (isset($dirty['url'])) {
                $repository->api = Remote::resolveAPI($repository);
                $repository->author_id = null;
                UpdateRepositoryData::dispatch($repository);
            }
        });
    }

    public function getJsonLd()
    {
        $jsonLd = new JsonLd();

        $jsonLd->setType('LearningResource');
        $jsonLd->addValue('@id', (string) $this->url);
        $jsonLd->addValue(
            'http://purl.org/dc/terms/conformsTo',
            [
                '@id' => 'https://bioschemas.org/profiles/TrainingMaterial/1.0-RELEASE',
                '@type' => 'CreativeWork',
            ],
        );
        $jsonLd->addValue('description', $this->description);
        $jsonLd->addValue('keywords', $this->tags->pluck('name')->join(', '));
        $jsonLd->addValue('name', $this->name);

        if ($this->author) {
            $jsonLd->addValue('author', $this->author->getJsonLd());
            $jsonLd->addValue('contributor', $this->author->getJsonLd());
        }

        $jsonLd->addValue('url', $this->website && $this->website != '' ? (string) $this->website : (string) $this->url);

        if ($this->repository_created_at) {
            $jsonLd->addValue('dateCreated', $this->repository_created_at->toIso8601String());
        }
        if ($this->repository_updated_at) {
            $jsonLd->addValue('dateModified', $this->repository_updated_at->toIso8601String());
        }
        if ($this->version_published_at) {
            $jsonLd->addValue('datePublished', $this->version_published_at->toIso8601String());
        }

        if ($this->version != '') {
            $jsonLd->addValue('version', $this->version);
        }

        if ($this->license != '') {
            if (isset(config('glittr.licences_url', [])[$this->license])) {
                $jsonLd->addValue('license', [config('glittr.licences_url', [])[$this->license]]);
            } else {
                // do we add if not found?
                $jsonLd->addValue('license', [$this->license]);
            }
        }

        $about = [];

        // Add tags ontology.
        foreach ($this->tags as $tag) {
            if ($tag->link != "") {
                $name = $tag->ontology_class != "" ? $tag->ontology_class : $tag->name;
                $data = [];

                if (Str::contains($tag->ontology->name ?? '', 'EDAM') && ($tag->term_code != '' || Str::contains($tag->link, 'edamontology.org'))) {
                    if (empty($tag->term_code)) {
                        $code = explode('/', $tag->link);
                        $code = end($code);
                    } else {
                        $code = $tag->term_code;
                    }

                    $data['@id'] = 'http://edamontology.org/'.$code;
                    $data['@type'] = 'DefinedTerm';
                    $data['inDefinedTermSet'] = 'http://edamontology.org';

                    $data['termCode'] = $code;
                    if (!Str::contains($tag->link, 'edamontology.org')) {
                        $data['url'] = $tag->link;
                    }
                } else {
                    $data['@id'] = $tag->link;
                    /*
                    $data['@type'] = 'Thing';
                    // termCode is not a valid property for Thing
                    if ($tag->term_code != "") {
                        $data['termCode'] = $tag->term_code;
                    }
                    */
                }

                $data['name'] = $name;

                $about[] = $data;
            }
        }
        if (!empty($about)) {
            $jsonLd->addValue('about', $about);
        }

        return $jsonLd;
    }
}
