<?php

namespace App\Console\Commands;

use App\ApicuronClient;
use App\Models\Submission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestApicuron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-apicuron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test APICURON API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new ApicuronClient();

        $submission_id = $this->ask('Enter the submission ID');
        $submission = Submission::find($submission_id);
        if ($submission === null) {
            $this->error('Submission not found');
            return;
        }
        if (!$submission->apicuron_orcid) {
            $this->error('Submission does not have an ORCID');
            return;
        }
        $response = $client->sendNewSubmission($submission);
        Log::info('Artisan Test APICURON submission response #' . $submission_id, ['response' => $response->json()]);
        dd($response->json());
    }
}
