<?php

namespace App\Mail;

use App\Models\Update;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RepositoriesUpdated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The update status
     *
     * @var Update
     */
    protected $update;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Update $update)
    {
        $this->update = $update;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject((0 == $this->update->error) ? 'Updated finished without error' : "Error updating ({$this->update->error} errors)")
            ->markdown('emails.repositories.updated', [
                'update' => $this->update,
            ]
        );
    }
}
