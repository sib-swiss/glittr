<?php

namespace App\Livewire\Admin;

use App\Concerns\InteractsWithNotifications;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
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

    /**
     * Show confirm dialog for category deletion
     *
     * @var bool
     */
    public $confirmingCategoryDeletion = false;

    /**
     * Category id for deletion modal action
     *
     * @var int
     */
    public $categoryIdBeingDeleted;


    /**
     * Display category edit form modal
     *
     * @var bool
     */
    public $showEditTag = false;

    /**
     * Display category add form modal
     *
     * @var bool
     */
    public $showAddTag = false;

    /**
     * Id of the tag editing
     *
     * @var int
     */
    public $tagIdBeingUpdated = null;

    /**
     * Auto-increment to create new empty render of form after each save/cancel
     *
     * @var int
     */
    public $tagAddIncrement = 0;

    /**
     * Show confirm dialog for tag deletion
     *
     * @var bool
     */
    public $confirmingTagDeletion = false;

    /**
     * tag id for deletion modal action
     *
     * @var int
     */
    public $tagIdBeingDeleted;

    protected $listeners = [
        'editCategoryCancel',
        'addCategoryCancel',
        'editCategorySuccess',
        'addCategorySuccess',

        'editTagCancel',
        'addTagCancel',
        'editTagSuccess',
        'addTagSuccess',
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
        } else {
            $this->errorNotification('There was a problem deleting category');
        }

        $this->confirmingCategoryDeletion = false;
        $this->categoryIdBeingDeleted = null;
    }

    public function sortCategories(array $categoryIds): void
    {
        if (! empty($categoryIds)) {
            Category::setNewOrder($categoryIds);
            Cache::tags('categories')->flush();
            $this->notify(__('Categories order successfully updated.'));
        }
    }

    public function addTagCancel(): void
    {
        $this->showAddTag = false;
        $this->tagAddIncrement++;
    }

    public function editTagCancel(): void
    {
        $this->tagIdBeingUpdated = null;
        $this->showEditTag = false;
    }

    public function addTagSuccess(Tag $tag): void
    {
        $this->notify(__("Tag {$tag->name} created successfully."));

        $this->showAddTag = false;
        $this->tagAddIncrement++;
    }

    public function editTagSuccess(Tag $tag): void
    {
        $this->notify(__("Tag {$tag->name} updated successfully."));

        $this->tagIdBeingUpdated = null;
        $this->showEditTag = false;
    }

    public function editTag(int $tagId): void
    {
        $this->showEditTag = true;
        $this->tagIdBeingUpdated = $tagId;
    }

    public function confirmTagDeletion(int $tagId): void
    {
        $this->confirmingTagDeletion = true;
        $this->tagIdBeingDeleted = $tagId;
    }

    public function deleteTag(): void
    {
        $tag = Tag::find($this->tagIdBeingDeleted);

        if ($tag && $tag->delete()) {
            $this->notify("Tag {$tag->name} successfully deleted.");
        } else {
            $this->errorNotification('There was a problem deleting tag');
        }

        $this->confirmingTagDeletion = false;
        $this->tagIdBeingDeleted = null;
    }

    public function sortTags(array $tagIds)
    {
        if (! empty($tagIds)) {
            Tag::setNewOrder($tagIds);
            Cache::tags('tags')->flush();
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
