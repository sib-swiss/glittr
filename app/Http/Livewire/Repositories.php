<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Repository;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Repositories extends Component
{

    use WithPagination;

    public $tags = [];
    public $categories= [];

    public $search;
    public $selected_tags;
    public $selected_categories;
    public $author;

    protected $listeners = ['searchUpdated'];
    protected $queryString = ['search', 'selected_tags', 'selected_categories', 'author'];

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
    }

    public function searchUpdated(string $value): void
    {
        $this->search = $value;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleCategory(int $categoryId): void
    {
        $status = null;

        if (isset($this->categories[$categoryId])) {
            $this->categories[$categoryId]['selected'] = !$this->categories[$categoryId]['selected'];
            $status = $this->categories[$categoryId]['selected'];
        }

        if (!is_null($status)) {
            foreach($this->tags as $tagId => $tag) {
                if ($tag['cid'] == $categoryId) {
                    $this->tags[$tagId]['selected'] = $status;
                }
            }
        }

        $this->resetPage();
    }

    public function toggleTag(int $tagIndex): void
    {
        if (isset($this->tags[$tagIndex])) {
            $this->tags[$tagIndex]['selected'] = !$this->tags[$tagIndex]['selected'];
        }

        $this->resetPage();
    }

    public function render()
    {
        $repositories = Repository::enabled()->withTags();

        if ($this->search != '') {
            $repositories->search($this->search);
        }

        //Get tags filter counts
        $ids = $repositories->clone()->select('id')->get()->pluck('id')->toArray();

        //Filter tags
        $countTags = Tag::select('id', 'category_id')
        ->withCount(['repositories' => function(Builder $query) use($ids) {
            $query->whereIn('id', $ids);
        }])
        ->having('repositories_count', '>', 0)
        ->get()
        ->mapWithKeys(fn($item) => [$item->id => ['nb' => $item->repositories_count, 'cid' => $item->category_id]])
        ->all();

        foreach($this->tags as $tid => $tag) {
            $nb = $countTags[$tag['id']]['nb'] ?? 0;
            $this->tags[$tid]['filtered'] = $nb;
        }

        // Categories repositories count
        foreach(collect($countTags)->pluck('cid')->unique() as $cid) {
            $this->categories[$cid]['total'] = Repository::whereHas('tags.category', function(Builder $query) use($cid) {
                $query->where('id', $cid);
            })->whereIn('id', $ids)->count();
        }

        $tags = collect($this->tags);
        $filter_tags = $tags
            ->filter(fn($tag) => $tag['filtered'] > 0 && isset($this->categories[$tag['cid']]))
            ->groupBy('cid', true)
            ->map(fn($tags, $cid) => [
                'category' => $this->categories[$cid],
                'tags' => $tags,
                'order' => $this->categories[$cid]['order'],
            ])->sortBy('order');

        // apply selected tags to selection
        $selected_tags = collect($this->tags)
            ->filter(fn($tag) => $tag['selected'])
            ->pluck('id');


        if (count($selected_tags) > 0) {
            $repositories->whereHas('tags', function (Builder $query) use($selected_tags) {
                $query->whereIn('id', $selected_tags);
            });
        }

        return view('livewire.repositories', [
            'filter_tags' => $filter_tags,
            'repositories' => $repositories->paginate(20),
        ]);
    }
}
