<?php

namespace App\Http\Livewire\Admin;

use App\Data\TagData;
use App\Models\Category;
use App\Models\Tag;
use Livewire\Component;

class TagForm extends Component
{

    public $categories = [];
    public $tag = [];

    public $action = 'add';

    public $title = '';

    public $cancelEvent = '';

    public function mount(?int $tagId, string $cancelEvent)
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

        $this->emitUp("{$this->action}TagSuccess", [
            'tag' => $tag->id,
        ]);
    }

    public function render()
    {
        return view('livewire.admin.tag-form');
    }
}
