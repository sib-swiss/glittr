<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class Contributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'remote_id',
        'api',
        'username',
        'full_name',
        'profile_url',
        'avatar_url',
        'company',
        'orcid',
        'orcid_fetched_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'orcid_fetched_at' => 'datetime',
    ];

    public function repositories(): BelongsToMany
    {
        return $this->belongsToMany(Repository::class)->withPivot('contributions');
    }

    protected static function booted(): void
    {
        static::saved(function (Contributor $contributor) {
            if ($contributor->wasChanged('orcid')) {
                $contributor->repositories()->select('id')->each(
                    fn (Repository $repository) => Cache::forget($repository->jsonLdCacheKey())
                );
            }
        });
    }

    public function scopeExcludingBots(Builder $query): void
    {
        $query->where('profile_url', 'not like', 'https://github.com/apps/%');
    }
}
