@component('mail::message')
# A new repository has been submitted!

@component('mail::table')
| Field          | Value                  |
| -------------: | ---------------------- |
| **Url**        | [{{ $submission->url }}]({{ $submission->url }}) |
| **Tags**       | {{ $submission->tags->implode('name', ', ') }} |
| **Submitter**  | {{ $submission->name }} ({{ $submission->email }}) |
@endcomponent

@if ($submission->comment != '')
## Comment
@component('mail::panel')
{{ $submission->comment }}
@endcomponent
@endif

@component('mail::button', ['url' => route('admin.dashboard')])
Go to the dashboard
@endcomponent

@endcomponent
