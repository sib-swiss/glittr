<?php

namespace App\Models;

use App\Casts\Url;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'api',
        'remote_id',
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
}
