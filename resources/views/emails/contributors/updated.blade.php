@component('mail::message')
# Contributors sync finished with errors.

The following repositories could not have their contributors synced:

<ul>
@foreach($failures as $failure)
<li>
<strong>{{ $failure['url'] }}</strong><br />
{{ $failure['error'] }}
</li>
@endforeach
</ul>

@component('mail::button', ['url' => route('admin.dashboard')])
Go to the dashboard
@endcomponent

@endcomponent
