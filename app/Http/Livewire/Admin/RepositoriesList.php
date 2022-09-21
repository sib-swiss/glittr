<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

class RepositoriesList extends Component
{
    public $showAdd = false;

    public $addIncrement = 0;

    protected $listeners = [
        'addRepositoryCancel',
        'addRepositorySuccess',
    ];

    public function addRepositoryCancel()
    {
        $this->showAdd = false;
    }

    public function render()
    {
        return view('livewire.admin.repositories-list');
    }
}
