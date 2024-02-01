<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Tag extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    public $sortable = [
        'order_column_name' => 'order_column',
        'sort_when_creating' => true,
    ];

    protected $fillable = [
        'name',
        'ontology_id',
        'ontology_class',
        'term_code',
        'link',
        'description',
        'category_id',
    ];

    public function ontology()
    {
        return $this->belongsTo(Ontology::class);
    }

    public function repositories()
    {
        return $this->belongsToMany(Repository::class)->using(RepositoryTag::class);
    }

    public function submissions()
    {
        return $this->belongsToMany(Submission::class)->using(SubmissionTag::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Group sortable with the category
     */
    public function buildSortQuery(): Builder
    {
        return static::query()->where('category_id', $this->category_id);
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saved(function (Tag $tag) {
            Cache::tags(['tags'])->flush();
        });
    }
}
