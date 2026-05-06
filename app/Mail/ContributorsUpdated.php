<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContributorsUpdated extends Mailable
{
    use Queueable;
    use SerializesModels;

    public array $failures;

    public function __construct(array $failures)
    {
        $this->failures = $failures;
    }

    public function build()
    {
        $count = count($this->failures);

        return $this
            ->subject("Contributors sync finished with {$count} error(s)")
            ->markdown('emails.contributors.updated', ['failures' => $this->failures]);
    }
}
