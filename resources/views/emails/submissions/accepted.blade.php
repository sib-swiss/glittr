@component('mail::message')

Dear {{ $submission->name }},

Thank you for adding **{{ $submission->url }}** to Glittr.org! These contributions are very much appreciated by the community.

@if ($submission->validation_message != "")
Here's what the curator wrote:
@component('mail::panel')
{!! nl2br($submission->validation_message) !!}
@endcomponent
@endif

Best wishes,
Geert from Glittr.org

@endcomponent
