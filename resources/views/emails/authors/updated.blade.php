@component('mail::message')
# Authors update finished with errors.

The following authors could not be updated:

<ul>
@foreach($failures as $failure)
<li>
<strong>{{ $failure['name'] }}</strong> (API: {{ $failure['api'] }})<br />
{{ $failure['error'] }}
</li>
@endforeach
</ul>

@component('mail::button', ['url' => route('admin.dashboard')])
Go to the dashboard
@endcomponent

@endcomponent
