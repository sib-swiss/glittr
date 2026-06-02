<?php

namespace App\Livewire;

use App\Models\Repository;
use Livewire\Component;
use Livewire\WithPagination;

class RepositoryContributors extends Component
{
    use WithPagination;

    public Repository $repository;

    public function render()
    {
        return view('livewire.repository-contributors', [
            'contributors' => $this->repository
                ->contributors()
                ->excludingBots()
                ->orderByPivot('contributions', 'desc')
                ->paginate(15),
        ]);
    }
}
