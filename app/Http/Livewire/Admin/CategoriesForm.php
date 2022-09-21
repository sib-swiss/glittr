<?php

namespace App\Http\Livewire\Admin;

use App\Data\CategoryData;
use App\Models\Category;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;

class CategoriesForm extends Component
{
    use InteractsWithBanner;

    public $category = [];

    public $action = 'add';

    public $title = '';

    public $cancelEvent = '';

    public function mount(?int $categoryId, string $cancelEvent)
    {
        if ($categoryId) {
            $this->category = CategoryData::from(Category::find($categoryId))->toArray();
            $this->action = 'edit';
            $this->title = 'Edit tags category';
        } else {
            $this->category = CategoryData::empty();
            $this->action = 'add';
            $this->title = 'Add tags category';
        }

        $this->cancelEvent = $cancelEvent;
    }

    public function save()
    {
        CategoryData::validate($this->category);

        $data = CategoryData::from($this->category);

        $category = Category::updateOrCreate(
            ['id' => $data->id],
            $data->toArray()
        );

        $this->emitUp("{$this->action}CategorySuccess", [
            'category' => $category->id,
        ]);
    }

    public function render()
    {
        return view('livewire.admin.categories-form');
    }
}
