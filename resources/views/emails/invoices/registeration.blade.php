@component('mail::message')


@component('mail::panel')
<h1 class="text-center">Registeration Processing Invoice</h1>
Hello {{ $user->full_name }},
@endcomponent

@component('mail::subcopy')
{{$data['message']}}
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
