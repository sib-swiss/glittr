<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Repository;
use App\Models\Tag;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Michelf\MarkdownExtra;

class Repositories extends Component
{
    use WithPagination;

    /**
     * List of tags
     *
     * @var array
     */
    public $tags = [];

    /**
     * Categories list
     *
     * @var array
     */
    public $categories = [];

    /**
     * Licence list
     *
     * @var array
     */
    public $licenses = [];

    /**
     * Grouped list of tags with count of filtered results
     *
     * @var array
     */
    public $grouped_tags = [];

    public $max_tags;

    public $split_tags_filter;

    #[Url(except: '', as: 'tags')]
    public $tagIds = '';

    #[Url(except: '')]
    public $search = '';

    #[Url(except: '')]
    public $per_page;

    #[Url(except: '')]
    public $sort_by;

    #[Url(except: '')]
    public $sort_direction;

    public $show_filters = false;

    // Columns filters
    #[Url(except: null)]
    public $name;

    #[Url(except: null)]
    public $author;

    #[Url(except: null)]
    public $minStars;

    #[Url(except: null)]
    public $maxStars;

    #[Url(except: null)]
    public $minPush;

    #[Url(except: null)]
    public $maxPush;

    #[Url(except: null)]
    public $license;

    #[Url]
    public $tags_and;

    protected $sortColumns = [
        'name',
        'author',
        'stargazers',
        'last_push',
        'license',
    ];

    public function mount()
    {
        if (! $this->sort_by) {
            $this->sort_by = config('glittr.default_sort_by', 'name');
        }
        if (! $this->sort_direction) {
            $this->sort_direction = config('glittr.default_sort_direction', 'asc');
        }
        if (! $this->per_page) {
            $this->per_page = config('glittr.default_per_page', 20);
        }

        if (! $this->tags_and) {
            $this->tags_and = config('glittr.tags_default_and_operator', 'OR') == 'AND';
        }

        $this->max_tags = config('glittr.max_tags', 10);
        $this->split_tags_filter = config('glittr.split_tags_filter', false);
        $licences = Repository::where('license', '!=', '')->orderBy('license', 'asc')->select('license')->distinct()->get();
        foreach ($licences as $licence) {
            $this->licenses[$licence->license] = $licence->license;
        }
        $categories = Category::getCategoriesWithTags();
        foreach ($categories as $cat) {
            $this->categories[$cat->id] = [
                'name' => $cat->name,
                'color' => $cat->color,
                'order' => $cat->order_column,
                'selected' => false,
                'total' => 0,
            ];
            foreach ($cat->tags as $tag) {
                $this->tags[$tag->id] = [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'order' => $tag->order_column,
                    'total' => $tag->repositories_count,
                    'filtered' => $tag->repositories_count,
                    'selected' => false,
                    'cid' => $cat->id,
                ];
            }
        }

        if ($this->tagIds != '') {
            $tagIds = explode(',', $this->tagIds);
            foreach ($tagIds as $tagId) {
                if (intval($tagId) > 0 && isset($this->tags[$tagId])) {
                    $this->tags[$tagId]['selected'] = true;
                }
            }
        }

        $this->updateGroupedTags();
    }

    public function sortBy(string $column): void
    {
        if (in_array($column, $this->sortColumns)) {
            if ($this->sort_by == $column) {
                $this->sort_direction = $this->sort_direction == 'asc' ? 'desc' : 'asc';
            } else {
                $this->sort_direction = 'asc';
            }
            $this->sort_by = $column;
        }
    }

    public function changeSort(string $column, string $direction)
    {
        if (in_array($column, $this->sortColumns) && in_array($direction, ['asc', 'desc'])) {
            $this->sort_by = $column;
            $this->sort_direction = $direction;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->updateGroupedTags();
    }

    public function updatedName()
    {
        $this->updateGroupedTags();
    }

    public function updatedAuthor()
    {
        $this->updateGroupedTags();
    }

    public function updatedMinStars()
    {
        $this->updateGroupedTags();
    }

    public function updatedMaxStars()
    {
        $this->updateGroupedTags();
    }

    public function updatedMinPush()
    {
        $this->updateGroupedTags();
    }

    public function updatedMaxPush()
    {
        $this->updateGroupedTags();
    }

    public function updatedLicense()
    {
        $this->updateGroupedTags();
    }

    public function updatedTagsAnd($isAnd)
    {
        if ($isAnd) {
            foreach ($this->tags as $tagId => $tag) {
                $this->tags[$tagId]['selected'] = false;
            }
        }
        $this->updateGroupedTags();
    }

    public function updated($name, $value)
    {
        $splitted = explode('.', $name);
        if (count($splitted) === 3 && $splitted[0] == 'categories' && $splitted[2] == 'selected') {
            $this->toggleCategory(intval($splitted[1]), $value);
        } elseif (count($splitted) === 3 && $splitted[0] == 'tags' && $splitted[2] == 'selected') {
            $this->resetPage();
            $this->groupTags();
            $this->setTagsIds();
            if ($this->tags_and) {
                $this->updateGroupedTags();
            }
        }
    }

    public function toggleCategory(int $categoryId, $status): void
    {
        if (! is_null($status)) {
            foreach ($this->tags as $tagId => $tag) {
                if ($tag['cid'] == $categoryId) {
                    $this->tags[$tagId]['selected'] = $status;
                }
            }
        }

        $this->setTagsIds();
        $this->resetPage();
        $this->groupTags();
    }

    public function setTagsIds(): void
    {
        $tagIds = [];
        foreach ($this->tags as $tagId => $tag) {
            if ($tag['selected']) {
                $tagIds[] = $tagId;
            }
        }
        $this->tagIds = implode(',', $tagIds);
    }

    public function clearTags()
    {
        foreach ($this->tags as $tagIndex => $tag) {
            if ($tag['selected']) {
                $this->tags[$tagIndex]['selected'] = false;
            }
        }
        foreach ($this->categories as $cid => $category) {
            if ($category['selected']) {
                $this->categories[$cid]['selected'] = false;
            }
        }
        $this->tagIds = '';
    }

    public function render()
    {
        $repositories = Repository::with(['tags', 'author'])->enabled();
        $repositories = $this->filterRepositories($repositories);

        $selected_tags = collect($this->tags)
            ->filter(fn ($tag) => $tag['selected']);

        // Display filters if any is set.
        if ($this->name != '' || $this->author != '' || $this->minStars != '' || $this->maxStars != '' || $this->minPush != '' || $this->maxPush != '' || $this->license != '') {
            $this->show_filters = true;
        }

        if (! $this->tags_and) {
            $this->applyTagsSelection($repositories);
        }

        // Order the results.
        if (in_array($this->sort_by, $this->sortColumns)) {
            $repositories->orderedBy($this->sort_by, $this->sort_direction);
        }

        $sorting_columns = [];
        foreach ($this->sortColumns as $column) {
            foreach (['asc', 'desc'] as $direction) {
                $sorting_columns[] = [
                    'column' => $column,
                    'direction' => $direction,
                    'label' => Str::headline($column . ' ' . $direction),
                    'selected' => ($this->sort_by == $column && $this->sort_direction == $direction),
                ];
            }
        }
        $parser = new MarkdownExtra();
        $parser->hard_wrap = true;
        return view('livewire.repositories', [
            'repositories' => $repositories->paginate(intval($this->per_page)),
            'selected_tags' => $selected_tags,
            'sorting_columns' => $sorting_columns,
            'header_text' => $parser->transform(app(GeneralSettings::class)->header_text),
        ]);
    }

    protected function applyTagsSelection(&$repositories): void
    {
        // Apply selected tags to selection.
        $selected_tags = collect($this->tags)
            ->filter(fn ($tag) => $tag['selected']);

        if (count($selected_tags) > 0) {
            $selectedTagIds = $selected_tags->pluck('id');
            $operator = config('glittr.tags_operator', 'OR');
            $repositories->whereHas('tags', function (Builder $query) use ($selectedTagIds) {
                $query->whereIn('id', $selectedTagIds);
                if ($this->tags_and) {
                    $query->havingRaw('COUNT(id) = ?', [count($selectedTagIds)]);
                }
            });
        }
    }

    protected function filterRepositories($repositories)
    {
        if ($this->search != '') {
            $repositories->search($this->search);
        }
        if ($this->name != '') {
            $repositories->where('repositories.name', 'like', '%' . $this->name . '%');
        }
        if ($this->author != '') {
            $repositories->whereHas('author', function (Builder $query) {
                $query->where('name', 'like', '%' . $this->author . '%')
                    ->orWhere('display_name', 'like', '%' . $this->author . '%');
            });
        }
        if ($this->minStars != '' && intval($this->minStars) > 0) {
            $repositories->where('stargazers', '>=', intval($this->minStars));
        }
        if ($this->maxPush != '' && intval($this->maxPush) >= 0) {
            $lastPush = Carbon::now()->setHour(0)->subDays(intval($this->maxPush));
            $repositories->where('last_push', '>=', $lastPush);
        }
        if ($this->license != '') {
            $repositories->where('license', $this->license);
        }

        if ($this->tags_and) {
            $this->applyTagsSelection($repositories);
        }

        return $repositories;
    }

    protected function updateGroupedTags()
    {
        $searchMeta = implode('.', [
            $this->search,
            $this->name,
            $this->author,
            $this->minStars,
            $this->maxStars,
            $this->minPush,
            $this->maxPush,
            $this->license,
            $this->tags_and ? $this->tagIds : '',
        ]);

        // Keep in cache for 30 min.
        [$countTags, $countCategories] = Cache::tags(['repositories', 'tags', 'categories'])->remember("count.{$searchMeta}", (60 * 30), function () {
            $repositories = Repository::enabled();
            $repositories = $this->filterRepositories($repositories);

            //Get tags filter counts
            $ids = $repositories->select('id')->get()->pluck('id')->toArray();

            //Filter tags
            $countTags = Tag::select('id', 'category_id')
                ->withCount(['repositories' => function (Builder $query) use ($ids) {
                    $query->whereIn('id', $ids);
                }])
                ->having('repositories_count', '>', 0)
                ->get()
                ->mapWithKeys(fn ($item) => [$item->id => ['nb' => $item->repositories_count, 'cid' => $item->category_id]])
                ->all();

            $countCategories = [];

            // Categories repositories count
            foreach (collect($countTags)->pluck('cid')->unique() as $cid) {
                $countCategories[$cid] = Repository::whereHas('tags.category', function (Builder $query) use ($cid) {
                    $query->where('id', $cid);
                })->whereIn('id', $ids)->count();
            }

            return [
                $countTags,
                $countCategories,
            ];
        });

        foreach ($this->tags as $tid => $tag) {
            $nb = $countTags[$tag['id']]['nb'] ?? 0;
            $this->tags[$tid]['filtered'] = $nb;
        }

        foreach ($countCategories as $cid => $total) {
            $this->categories[$cid]['total'] = $total;
        }

        $this->groupTags();
    }

    protected function groupTags()
    {
        $tags = collect($this->tags);
        $this->grouped_tags = $tags
            ->filter(fn ($tag) => $tag['filtered'] > 0 && isset($this->categories[$tag['cid']]))
            ->sortBy('order')
            ->groupBy('cid', true)
            ->map(fn ($tags, $cid) => [
                'category' => $this->categories[$cid],
                'tags' => $tags,
                'order' => $this->categories[$cid]['order'],
            ])->sortBy('order');
    }
}
