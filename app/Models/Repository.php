<?php

namespace App\Models;

use App\Casts\Url;
use App\Facades\Remote;
use App\Jobs\UpdateRepositoryData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

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
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->using(RepositoryTag::class)->ordered();
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

        $query->orderBy($column, $direction);
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
}
