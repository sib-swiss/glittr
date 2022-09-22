<?php

namespace App\Models;

use App\Casts\Url;
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
        'decription',
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
}
