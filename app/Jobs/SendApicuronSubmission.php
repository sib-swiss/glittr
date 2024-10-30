<?php

namespace App\Jobs;

use App\ApicuronClient;
use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendApicuronSubmission implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The submission to send
     *
     * @var Submission
     */
    public $submission;

    /**
     * Number of allowed tries
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(Submission $submission)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->submission->refresh();

        // Ensure the submission has orcid, submit flag and not already submitted.
        if ($this->submission->apicuron_orcid && $this->submission->apicuron_submit && $this->submission->repository && !$this->submission->apicuron_submitted_at) {
            // Send the submission to Apicuron .
            $client = new ApicuronClient();
            $response = $client->sendNewSubmission($this->submission->apicuron_orcid, $this->submission->repository->url);
        }
    }
}
