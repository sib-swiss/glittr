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

    public function mount(?int $categoryId, string $action)
    {
        if ($categoryId) {
            $this->category = CategoryData::from(Category::find($categoryId))->toArray();
        } else {
            $this->category = CategoryData::empty();
        }

        $this->action = $action;
    }

    public function save()
    {
        CategoryData::validate($this->category);

        $data = CategoryData::from($this->category);

        Category::updateOrCreate(
            ['id' => $data->id],
            $data->toArray()
        );

        $this->banner('Sucess');
        $this->emitUp($this->action.'CategorySuccess');
    }

    public function cancel()
    {
        $this->emitUp($this->action.'CategoryCancel');
    }

    public function render()
    {
        return view('livewire.admin.categories-form');
    }
}
