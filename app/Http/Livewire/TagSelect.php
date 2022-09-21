<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\View\View;
use Livewire\Component;

class TagSelect extends Component
{
    /**
     * List of selcted items
     *
     * @var array
     */
    public $selected = [];

    /**
     * Livewire model attached to select categorie to add new tags
     *
     * @var string|int
     */
    public $add;

    /**
     * Customizable event name when list is updated
     *
     * @var string
     */
    public $eventName;

    /**
     * Undocumented function
     *
     * @param  array  $selected Array of ordered selected tag ids [2, 1, 5]
     * @return void
     */
    public function mount(array $selected = [], ?string $eventName = 'tagsUpdated'): void
    {
        foreach ($selected as $tagId) {
            $this->addTag($tagId, false);
        }
        $this->eventName = $eventName;
    }

    public function updatedAdd(string $value): void
    {
        $this->addTag(intval($value));
        $this->add = null;
    }

    public function render(): View
    {
        $selectedIds = $this->getIds();
        $categories = Category::select('id', 'name', 'color')
            ->ordered()
            ->with(['tags' => function ($query) use ($selectedIds) {
                $query->select('id', 'category_id', 'name')
                ->ordered()
                ->whereNotIn('id', $selectedIds);
            }])->get()
            ->filter(fn ($category) => count($category->tags) > 0);

        if (! empty($this->selected)) {
            $mainTag = reset($this->selected);
        } else {
            $mainTag = null;
        }

        return view('livewire.tag-select', compact('categories', 'mainTag'));
    }

    protected function getIds()
    {
        return collect($this->selected)->pluck('id');
    }

    public function sort(array $keyOrders)
    {
        $startOrder = 0;
        $sortArray = [];

        foreach ($keyOrders as $key) {
            $sortArray[$key] = $startOrder;
            $startOrder++;
        }

        ksort($sortArray);
        array_multisort($sortArray, SORT_ASC, SORT_NUMERIC, $this->selected);
    }

    public function remove(int $index): void
    {
        if (isset($this->selected[$index])) {
            unset($this->selected[$index]);
        }
    }

    protected function addTag(int $tagId, bool $emit = true): void
    {
        if (! $this->getIds()->contains($tagId)) {
            $tag = Tag::select('id', 'category_id', 'name')->with('category:id,name,color')->where('id', $tagId)->first();
            if ($tag) {
                array_push($this->selected, [
                    'id' => $tag->id,
                    'tag' => $tag->name,
                    'category' => $tag->category->name ?? '',
                    'color' => $tag->category->color ?? '#ff1986',
                ]);

                if ($emit) {
                    $this->emitUpdate();
                }
            }
        }
    }

    protected function emitUpdate(): void
    {
        $this->emit($this->eventName, $this->getIds());
    }
}
