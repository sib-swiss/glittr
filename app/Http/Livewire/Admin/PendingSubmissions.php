<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;

class PendingSubmissions extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.pending-submissions');
    }
}
