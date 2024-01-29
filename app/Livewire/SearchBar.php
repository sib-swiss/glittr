<?php

namespace App\Livewire;

use Livewire\Component;

class SearchBar extends Component
{
    public $search;

    protected $queryString = ['search'];

    public function updatedSearch($value)
    {
        $this->dispatch('searchUpdated', value: $value);
    }

    public function render()
    {
        return view('livewire.search-bar');
    }
}
