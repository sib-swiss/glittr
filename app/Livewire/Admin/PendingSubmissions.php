<?php

namespace App\Livewire\Admin;

use App\Concerns\InteractsWithNotifications;
use App\Mail\SubmissionRefused;
use App\Models\Repository;
use App\Models\Submission;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class PendingSubmissions extends Component
{
    use InteractsWithNotifications;
    use WithPagination;

    public $showAccept = false;

    public $acceptingSubmissionId;

    public $showDecline = false;

    public $decliningSubmissionId;

    public $declineComment = '';

    protected $listeners = [
        'addRepositoryCancel',
        'addRepositorySuccess',
    ];

    public function addRepositorySuccess(Repository $repository)
    {
        $this->showAccept = false;
        $this->acceptingSubmissionId = null;
    }

    public function addRepositoryCancel()
    {
        $this->showAccept = false;
        $this->acceptingSubmissionId = null;
    }

    public function acceptSubmission(int $id): void
    {
        $this->showAccept = true;
        $this->acceptingSubmissionId = $id;
    }

    public function declineSubmission(int $id): void
    {
        $this->showDecline = true;
        $this->decliningSubmissionId = $id;
    }

    public function decline(): void
    {
        if ($this->decliningSubmissionId) {
            $submission = Submission::find($this->decliningSubmissionId);
            $submission->validation_message = $this->declineComment;
            $submission->validated = false;
            $submission->validated_by = Auth::user()->id;
            $submission->validated_at = Carbon::now();
            $submission->save();

            $this->notify('Submission marked as declined.');

            // Send email to the submitter
            if ($submission->email && filter_var($submission->email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($submission->email)->bcc(config('glittr.notification_emails'))->send(new SubmissionRefused($submission));
            }

            $this->showDecline = false;
            $this->declineComment = '';
            $this->decliningSubmissionId = null;
        }
    }

    public function cancelDecline(): void
    {
        $this->showDecline = false;
        $this->declineComment = '';
        $this->decliningSubmissionId = null;
    }

    public function render()
    {
        $submissions = Submission::pending();

        return view('livewire.admin.pending-submissions', [
            'submissions' => $submissions->paginate(25),
        ]);
    }
}
