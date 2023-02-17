<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Category extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    public $sortable = [
        'order_column_name' => 'order_column',
        'sort_when_creating' => true,
    ];

    protected $fillable = [
        'name',
        'color',
    ];

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public static function getCategoriesWithTags()
    {
        return Cache::tags(['categories', 'tags', 'repositories'])
            ->remember('categories_list', (30 * 60), function () {
                return self::with(['tags' => function ($query) {
                    $query->ordered()->withCount('repositories');
                }])->ordered()->get();
            });
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saved(function (Category $category) {
            Cache::tags('categories')->flush();
        });
    }
}
