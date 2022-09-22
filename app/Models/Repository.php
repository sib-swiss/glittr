<?php

namespace App\Models;

use App\Casts\Url;
use App\Facades\Remote;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsToMany(Tag::class)->using(RepositoryTag::class);
    }

    public function getClient()
    {
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function (Repository $repository) {
            if (! $repository->api || empty($repository->api)) {
                $repository->api = Remote::resolveAPI($repository);
            }
        });
    }
}
