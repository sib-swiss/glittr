<?php

namespace App\Actions;

use App\Data\SubmissionData;
use App\Models\Submission;

class CreateSubmission
{
    public function execute(SubmissionData $submissionData): ?Submission
    {
        $submission = Submission::create($submissionData->toArray());

        if ($submission) {
            $submission->tags()->sync($submissionData->tags);
        }

        return $submission;
    }
}
