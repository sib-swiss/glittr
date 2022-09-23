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

    protected $listeners = ['searchUpdated'];
    protected $queryString = ['search'];

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
                'all_selected' => false,
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
    }

    public function toggleTag(int $tagIndex): void
    {
        if (isset($this->tags[$tagIndex])) {
            $this->tags[$tagIndex]['selected'] = !$this->tags[$tagIndex]['selected'];
        }
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
        $countTags = Tag::select('id')->withCount('repositories')->whereHas('repositories', function (Builder $query) use($ids) {
            $query->whereIn('id', $ids);
        })
        ->get()
        ->mapWithKeys(fn($item) => [$item->id => $item->repositories_count])
        ->all();

        foreach($this->tags as $tid => $tag) {
            $nb = $countTags[$tag['id']] ?? 0;
            $this->tags[$tid]['filtered'] = $nb;
            // deselect tags fitleted out by search terms?
            //if (0 === $nb) {
            //    $this->tags[$tid]['selected'] = false;
            //}
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
            ->pluck('id')->toArray();

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
