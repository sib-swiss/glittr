<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Repository;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

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
     * Grouped list of tags with count of filtered results
     *
     * @var array
     */
    public $grouped_tags = [];

    public $search;

    public $per_page = 20;

    public $sort_by = 'name';

    public $sort_direction = 'asc';

    protected $queryString = ['search', 'per_page', 'sort_by', 'sort_direction'];

    protected $sortColumns = [
        'name',
        'author',
        'stargazers',
        'last_push',
        'license',
    ];

    public function mount()
    {
        $categories = Category::with(['tags' => function ($query) {
            $query->ordered()->withCount('repositories');
        }])->ordered()->get();

        foreach ($categories as $cat) {
            $this->categories[$cat->id] = [
                'name' => $cat->name,
                'color' => $cat->color,
                'order' => $cat->order_column,
                'selected' => false,
                'total' => 0,
            ];
            foreach ($cat->tags as $tag) {
                $this->tags[] = [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'total' => $tag->repositories_count,
                    'filtered' => $tag->repositories_count,
                    'selected' => false,
                    'cid' => $cat->id,
                ];
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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->updateGroupedTags();
    }

    public function toggleCategory(int $categoryId): void
    {
        $status = null;

        if (isset($this->categories[$categoryId])) {
            $this->categories[$categoryId]['selected'] = ! $this->categories[$categoryId]['selected'];
            $status = $this->categories[$categoryId]['selected'];
        }

        if (! is_null($status)) {
            foreach ($this->tags as $tagId => $tag) {
                if ($tag['cid'] == $categoryId) {
                    $this->tags[$tagId]['selected'] = $status;
                }
            }
        }

        $this->resetPage();
        $this->groupTags();
    }

    public function toggleTag(int $tagIndex): void
    {
        if (isset($this->tags[$tagIndex])) {
            $this->tags[$tagIndex]['selected'] = ! $this->tags[$tagIndex]['selected'];
        }

        $this->resetPage();
        $this->groupTags();
    }

    public function render()
    {
        $repositories = Repository::with(['tags', 'author'])->enabled();

        if ($this->search != '') {
            $repositories->search($this->search);
        }

        // Apply selected tags to selection.
        $selected_tags = collect($this->tags)
            ->filter(fn ($tag) => $tag['selected'])
            ->pluck('id');

        if (count($selected_tags) > 0) {
            $repositories->whereHas('tags', function (Builder $query) use ($selected_tags) {
                $query->whereIn('id', $selected_tags);
            });
        }

        // Order the results.
        if (in_array($this->sort_by, $this->sortColumns)) {
            $repositories->orderedBy($this->sort_by, $this->sort_direction);
        }

        return view('livewire.repositories', [
            'repositories' => $repositories->paginate(intval($this->per_page)),
        ]);
    }

    protected function updateGroupedTags()
    {
        $search = $this->search;
        // Keep in cache for 30 min.
        [$countTags, $countCategories] = Cache::tags(['repositories', 'tags', 'categories'])->remember("count.{$this->search}", (60 * 30), function () use ($search) {
            $ids = Repository::enabled();
            if ($search != '') {
                $ids->search($search);
            }

            //Get tags filter counts
            $ids = $ids->select('id')->get()->pluck('id')->toArray();

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
            ->groupBy('cid', true)
            ->map(fn ($tags, $cid) => [
                'category' => $this->categories[$cid],
                'tags' => $tags,
                'order' => $this->categories[$cid]['order'],
            ])->sortBy('order');
    }
}
