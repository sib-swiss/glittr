@component('mail::message')

Dear {{ $submission->name }},

Thank you for adding **{{ $submission->url }}** to {{ config('app.name') }}. Unfortunately, your submission did not pass our checks.

@if ($submission->validation_message != "")
Here's what the curator wrote:
@component('mail::panel')
{!! nl2br($submission->validation_message) !!}
@endcomponent
@endif

{!! $mail_signature !!}

@endcomponent
