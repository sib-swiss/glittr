<?php

namespace App\Livewire\Admin;

use App\Data\TagData;
use App\Models\Category;
use App\Models\Ontology;
use App\Models\Tag;
use Livewire\Component;

class TagForm extends Component
{
    /**
     * List of categories for form select
     *
     * @var array
     */
    public $categories = [];

    /**
     * List of related ontologies for form select
     *
     * @var array
     */
    public $ontologies = [];

    /**
     * Tag data array
     *
     * @var array
     */
    public $tag = [];

    /**
     * add or update form action depnding on recieved id
     *
     * @var string
     */
    public $action = 'add';

    /**
     * Modal title
     *
     * @var string
     */
    public $title = '';

    /**
     * Cancel button event to emit
     *
     * @var string
     */
    public $cancelEvent = '';

    public function mount(?int $tagId, ?string $cancelEvent)
    {
        if ($tagId) {
            $this->tag = TagData::from(Tag::find($tagId))->toArray();
            $this->action = 'edit';
            $this->title = 'Edit tag';
        } else {
            $this->category = TagData::empty();
            $this->action = 'add';
            $this->title = 'Add tag';
        }
        $this->categories = Category::select('id', 'name')->ordered()->get()->mapWithKeys(fn ($cat) => [$cat->id => $cat->name]);
        $this->ontologies = Ontology::select('id', 'name')->orderBy('name')->get()->mapWithKeys(fn ($ont) => [$ont->id => $ont->name]);
        $this->cancelEvent = $cancelEvent;
    }

    public function save()
    {
        TagData::validate($this->tag);

        $data = TagData::from($this->tag);

        $currentTag = null;
        if ($data->id) {
            $currentTag = Tag::find($data->id);
        }

        $tag = Tag::updateOrCreate(
            ['id' => $data->id],
            $data->toArray()
        );

        if ($currentTag && $currentTag->category_id != $tag->category_id) {
            $tag->moveToStart();
        }

        $this->dispatch("{$this->action}TagSuccess",
            tag: $tag->id,
        );
    }

    public function render()
    {
        return view('livewire.admin.tag-form');
    }
}
