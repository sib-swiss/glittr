<?php

namespace App\Http\Livewire\Admin;

use App\Models\Submission;
use Livewire\Component;
use Livewire\WithPagination;

class PendingSubmissions extends Component
{
    use WithPagination;

    public function render()
    {
        $submissions = Submission::pending();
        return view('livewire.admin.pending-submissions', [
            'submissions' => $submissions->paginate(25),
        ]);
    }
}
