<?php

namespace App\Actions;

use App\Data\SubmissionData;
use App\Mail\SubmissionAdded;
use App\Models\Submission;
use Illuminate\Support\Facades\Mail;

class CreateSubmission
{
    public function execute(SubmissionData $submissionData): ?Submission
    {
        $submission = Submission::create($submissionData->toArray());

        if ($submission) {
            $submission->tags()->sync($submissionData->tags);

            //Send email
            foreach (config('repositories.notification_emails', []) as $email) {
                Mail::to($email)->send(new SubmissionAdded($submission));
            }
        }

        return $submission;
    }
}
