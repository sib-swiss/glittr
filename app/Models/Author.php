<?php

namespace App\Models;

use App\Casts\Url;
use Artesaos\SEOTools\JsonLd;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function getJsonLd()
    {
        $jsonLd = [];

        $jsonLd[] = (new JsonLd())
            ->setType($this->type == 'Organization' ? 'Organization' : 'Person')
            ->addValue('name', $this->display_name);

        if ($this->type != 'Organization' && $this->company != '') {
            $jsonLd[] = (new JsonLd())->setType('Organization')->addValue('name', $this->company);
        }

        return $jsonLd;
    }
}
