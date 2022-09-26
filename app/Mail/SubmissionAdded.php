<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubmissionAdded extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The submission
     *
     * @var Submission
     */
    protected $submission;

    /**
     * Create a new message instance.
     *
     * @param  Submission  $submission
     * @return void
     */
    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->markdown('emails.submissions.added', [
                'submission' => $this->submission,
            ]);
    }
}
