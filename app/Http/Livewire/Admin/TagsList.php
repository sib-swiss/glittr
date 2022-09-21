<?php

namespace App\Http\Livewire\Admin;

use App\Concerns\InteractsWithNotifications;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\View\View;
use Livewire\Component;

class TagsList extends Component
{
    use InteractsWithNotifications;

    /**
     * Display category edit form modal
     *
     * @var bool
     */
    public $showEditCategory = false;

    /**
     * Display category add form modal
     *
     * @var bool
     */
    public $showAddCategory = false;

    /**
     * Id of the category editing
     *
     * @var int
     */
    public $categoryIdBeingUpdated = null;

    /**
     * Auto-increment to create new empty render of form after each save/cancel
     *
     * @var int
     */
    public $categoryAddIncrement = 0;

    public $confirmingCategoryDeletion = false;

    public $categoryIdBeingDeleted;

    protected $listeners = [
        'editCategoryCancel',
        'addCategoryCancel',
        'editCategorySuccess',
        'addCategorySuccess',
    ];


    public function addCategoryCancel(): void
    {
        $this->showAddCategory = false;
        $this->categoryAddIncrement++;
    }

    public function editCategoryCancel(): void
    {
        $this->categoryIdBeingUpdated = null;
        $this->showEditCategory = false;
    }

    public function addCategorySuccess(Category $category): void
    {
        $this->notify(__("Category {$category->name} created successfully."));

        $this->showAddCategory = false;
        $this->categoryAddIncrement++;
    }

    public function editCategorySuccess(Category $category): void
    {
        $this->notify(__("Category {$category->name} updated successfully."));

        $this->categoryIdBeingUpdated = null;
        $this->showEditCategory = false;
    }

    public function editCategory(int $categoryId): void
    {

        $this->showEditCategory = true;
        $this->categoryIdBeingUpdated = $categoryId;
    }

    public function sortCategories(array $categoryIds): void
    {
        if (! empty($categoryIds)) {
            Category::setNewOrder($categoryIds);
            $this->notify(__('Categories order successfully updated.'));
        }
    }

    public function confirmCategoryDeletion(int $categoryId): void
    {
        $this->confirmingCategoryDeletion = true;
        $this->categoryIdBeingDeleted = $categoryId;
    }

    public function deleteCategory(): void
    {
        $category = Category::find($this->categoryIdBeingDeleted);

        if ($category && $category->delete()) {
            $this->notify("Category {$category->name} successfully deleted.");
        }

        $this->confirmingCategoryDeletion = false;
        $this->categoryIdBeingDeleted = null;
    }

    public function sortTags(array $tagIds)
    {
        if (! empty($tagIds)) {
            Tag::setNewOrder($tagIds);
            $this->notify(__('Tags order successfully updated.'));
        }
    }

    public function render(): View
    {
        $categories = Category::with(['tags' => function ($query) {
            $query->ordered()->withCount('repositories');
        }])->ordered()->get();

        return view('livewire.admin.tags-list', compact('categories'));
    }
}
