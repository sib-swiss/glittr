<?php

declare(strict_types=1);

namespace App\Livewire;

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
     * @var ?int
     */
    public $add;

    /**
     * Customizable event name when list is updated
     *
     * @var string
     */
    public $eventName;

    /**
     * Main tag
     *
     * @var array
     */
    public $mainTag = null;

    /**
     * List of categories for select
     *
     * @var array
     */
    public $categories = [];

    protected $rules = [
        'add' => 'required|int',
    ];

    /**
     * Undocumented function
     *
     * @param  array  $selected  Array of ordered selected tag ids [2, 1, 5]
     */
    public function mount(array $values = [], ?string $eventName = 'tagsUpdated'): void
    {
        foreach ($values as $tagId) {
            $this->addTag($tagId, false);
        }

        $this->categories = Category::select('id', 'name', 'color')
            ->ordered()
            ->with(['tags' => function ($query) {
                $query->select('id', 'category_id', 'name')
                    ->ordered();
            }])->get()
            ->filter(fn ($category) => count($category->tags) > 0)
            ->toArray();

        $this->updateData();

        $this->eventName = $eventName;
    }

    public function addTagAction(): void
    {
        $validatedData = $this->validate();
        $this->addTag(intval($validatedData['add']));
        $this->updateData();
        $this->add = null;
    }

    public function render(): View
    {
        return view('livewire.tag-select');
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

        $this->updateData();
        $this->emitUpdate();
    }

    public function remove(int $index): void
    {
        if (isset($this->selected[$index])) {
            unset($this->selected[$index]);
        }

        $this->updateData();
        $this->emitUpdate();
    }

    protected function updateData()
    {
        $selectedIds = array_keys($this->selected);
        foreach ($this->categories as $i => $category) {
            $visible = false;
            foreach ($category['tags'] as $j => $tag) {
                $tag_visible = ! in_array($tag['id'], $selectedIds);
                $this->categories[$i]['tags'][$j]['visible'] = $tag_visible;
                if ($tag_visible) {
                    $visible = true;
                }
            }
            $this->categories[$i]['visible'] = $visible;
        }

        if (count($this->selected) > 0) {
            $this->mainTag = reset($this->selected);
        } else {
            $this->mainTag = null;
        }
    }

    protected function addTag(int $tagId, bool $emit = true): void
    {

        if (! isset($this->selected[$tagId])) {
            $tag = Tag::select('id', 'category_id', 'name')->with('category:id,name,color')->where('id', $tagId)->first();
            if ($tag) {
                $this->selected[$tagId] = [
                    'id' => $tag->id,
                    'tag' => $tag->name,
                    'category' => $tag->category->name ?? '',
                    'color' => $tag->category->color ?? '#ff1986',
                ];
                if ($emit) {
                    $this->emitUpdate();
                }
            }
        }
    }

    protected function emitUpdate(): void
    {
        $this->dispatch($this->eventName, tagIds: $this->getIds());
    }
}
