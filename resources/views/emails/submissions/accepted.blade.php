@component('mail::message')

Dear {{ $submission->name }},

Thank you for adding **{{ $submission->url }}** to {{ config('app.name') }}! These contributions are very much appreciated by the community.

@if ($submission->validation_message != "")
Here's what the curator wrote:
@component('mail::panel')
{!! nl2br($submission->validation_message) !!}
@endcomponent
@endif

{!! $mail_signature !!}

@endcomponent
