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
            foreach (config('glittr.notification_emails', []) as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($email)->send(new SubmissionAdded($submission));
                }
            }
        }

        return $submission;
    }
}
