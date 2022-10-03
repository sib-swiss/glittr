@component('mail::message')
# Repositories update has finished.

@component('mail::table')
| Field              | Value                  |
| -------------:     | ---------------------- |
| **Duration**       | {{ $update->finished_at->diff($update->started_at)->format('%H:%I:%S') }} |
| **Started**        | {{ $update->started_at->format('Y-m-d H:i:s') }} |
| **Finished**       | {{ $update->finished_at->format('Y-m-d H:i:s') }} |
| **Repositories**   | {{ $update->total }} |
| **Success**        | {{ $update->success }}  |
| **Errors**         | {{ $update->error }}  |
@endcomponent

@if ($update->errors)
## Errors
<ul>
@foreach($update->errors as $error)
<li>
<strong># {{ $error['repository_id'] }}</strong>: {{ $error['error'] }}<br />{{ $error['url']}}
</li>
@endforeach
</ul>
@endif

@component('mail::button', ['url' => route('admin.dashboard')])
Go to the dashboard
@endcomponent

@endcomponent
