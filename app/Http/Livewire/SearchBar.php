<?php

namespace App\Http\Livewire;

use Livewire\Component;

class SearchBar extends Component
{

    public $search;
    protected $queryString = ['search'];

    public function updatedSearch($value)
    {
        $this->emit('searchUpdated', $value);
    }

    public function render()
    {
        return view('livewire.search-bar');
    }
}
