<?php

namespace App\Http\Livewire\Admin;

use App\Models\Category;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;

class TagsList extends Component
{
    use InteractsWithBanner;

    public $showEditCategory = false;

    public $showAddCategory = false;

    public $categoryEditId = null;

    public $categoryAddId = 0;

    protected $listeners = [
        'editCategoryCancel',
        'addCategoryCancel',
        'editCategorySuccess',
        'addCategorySuccess',
    ];

    public function addCategoryCancel()
    {
        $this->showAddCategory = false;
    }

    public function editCategoryCancel()
    {
        $this->categoryEditId = null;
        $this->showEditCategory = false;
    }

    public function addCategorySuccess()
    {
        $this->showAddCategory = false;
    }

    public function editCategorySuccess()
    {
        $this->categoryEditId = null;
        $this->showEditCategory = false;
    }

    public function editCategory($categoryId)
    {
        $this->showEditCategory = true;
        $this->categoryEditId = $categoryId;
    }

    public function sortCategories(array $order)
    {
        if (! empty($order)) {
            Category::setNewOrder($order);
            $this->banner(__('Categories order successfully updated.'));
        }
    }

    public function render()
    {
        $categories = Category::with('tags')->ordered()->get();

        return view('livewire.admin.tags-list', [
            'categories' => $categories,
        ]);
    }
}
