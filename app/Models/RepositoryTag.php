<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Cache;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class RepositoryTag extends Pivot implements Sortable
{
    use SortableTrait;

    public $sortable = [
        'order_column_name' => 'order_column',
        'sort_when_creating' => true,
    ];

    /**
     * Group sorting of tags per repository
     */
    public function buildSortQuery(): Builder
    {
        return static::query()->where('repository_id', $this->repository_id);
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saved(function (RepositoryTag $repositoryTag) {
            Cache::tags(['repositories', 'tags'])->flush();
        });
    }
}
