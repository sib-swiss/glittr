@component('mail::message')
# A new repository has been submitted!
@component('mail::button', ['url' => route('admin.dashboard')])
Go to the dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
