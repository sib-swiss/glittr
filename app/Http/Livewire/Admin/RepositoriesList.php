<?php

namespace App\Http\Livewire\Admin;

use App\Concerns\InteractsWithNotifications;
use App\Models\Repository;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class RepositoriesList extends Component
{
    use WithPagination;
    use InteractsWithNotifications;

    /**
     * Display repository add form modal
     *
     * @var bool
     */
    public $showAdd = false;

    /**
     * Auto increment to create new empty render form in create modal
     *
     * @var integer
     */
    public $addIncrement = 0;

    /**
     * Display repository edit form modal
     *
     * @var bool
     */
    public $showEdit = false;

    /**
     * Id of the repository being updated
     *
     * @var int
     */
    public $repositoryBeingUpdated;


    /**
     * Show confirm dialog for repository deletion
     *
     * @var bool
     */
    public $confirmingRepositoryDeletion = false;

    /**
     * Repository id for deletion modal action
     *
     * @var int
     */
    public $repositoryIdBeingDeleted;


    protected $listeners = [
        'addRepositoryCancel',
        'addRepositorySuccess',
        'editRepositorySuccess',
        'editRepositoryCancel',
    ];

    public function addRepositorySuccess(Repository $repository): void
    {
        $this->showAdd = false;
        $this->addIncrement++;
    }

    public function addRepositoryCancel(): void
    {
        $this->showAdd = false;
        $this->addIncrement++;
    }

    public function editRepositorySuccess(Repository $repository): void
    {
        $this->showEdit = false;
        $this->repositoryBeingUpdated = null;
    }

    public function editRepositoryCancel(): void
    {
        $this->showEdit = false;
    }

    public function editRepository(int $repositoryId): void
    {
        $this->repositoryBeingUpdated = $repositoryId;
        $this->showEdit = true;
    }

    public function disableRepository(Repository $repository): void
    {
        $repository->enabled = false;
        if ($repository->save()) {
            $this->notify("Repository {$repository->url} successfully enabled");
        }
    }

    public function enableRepository(Repository $repository): void
    {
        $repository->enabled = true;
        if ($repository->save()) {
            $this->notify("Repository {$repository->url} successfully enabled");
        }
    }

    public function confirmRepositoryDeletion(int $repositoryId)
    {
        $this->confirmingRepositoryDeletion = true;
        $this->repositoryIdBeingDeleted = $repositoryId;
    }

    public function deleteRepository()
    {
        $repository = Repository::find($this->repositoryIdBeingDeleted);

        if ($repository && $repository->delete()) {
            $this->notify("Repository {$repository->url} successfully deleted.");
        } else {
            $this->errorNotification('There was a problem deleting repository');
        }

        $this->confirmingRepositoryDeletion = false;
        $this->repositoryIdBeingDeleted = null;
    }

    public function render(): View
    {
        $repositories = Repository::orderByDesc('id')->with('tags.category');

        return view('livewire.admin.repositories-list', [
            'repositories' => $repositories->paginate(25),
        ]);
    }
}
