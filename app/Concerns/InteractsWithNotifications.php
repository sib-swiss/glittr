<?php

namespace App\Concerns;

trait InteractsWithNotifications
{
    protected function notify(string $message, string $title = '', string $type = 'success', $persisted = false)
    {
        $this->dispatch('notify',
            message: $message,
            title: $title,
            type: $type,
            persisted: $persisted
        );
    }

    protected function errorNotification($message, $title = null)
    {
        $this->dispatch('notify',
            message: $message,
            title: $title ?? 'Ther was a problem',
            type: 'error',
            persisted: true,
        );
    }
}
