<?php

namespace App\Http\Livewire\Admin;

use App\Models\Repository;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class RepositoriesList extends Component
{
    use WithPagination;

    public $showAdd = false;

    public $addIncrement = 0;

    public $showEdit = false;

    public $repositoryBeingUpdated;

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

    public function render(): View
    {
        $repositories = Repository::orderByDesc('id')->with('tags.category');

        return view('livewire.admin.repositories-list', [
            'repositories' => $repositories->paginate(25),
        ]);
    }
}
