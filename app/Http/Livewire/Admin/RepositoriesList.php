<?php

namespace App\Http\Livewire\Admin;

use App\Models\Repository;
use Livewire\Component;

class RepositoriesList extends Component
{
    public $showAdd = false;

    public $addIncrement = 0;

    protected $listeners = [
        'addRepositoryCancel',
        'AddRepositorySuccess',
    ];

    public function AddRepositorySuccess(Repository $repository)
    {
        $this->showAdd = false;
    }

    public function addRepositoryCancel()
    {
        $this->showAdd = false;
    }

    public function render()
    {
        return view('livewire.admin.repositories-list');
    }
}
