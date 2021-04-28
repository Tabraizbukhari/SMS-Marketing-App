@component('mail::message')


@component('mail::panel')
<h1 class="text-center">Registeration Processing Invoice</h1>
Hello {{ $user['first_name'] }},
   

@endcomponent

@component('mail:table')
    <tr> 
        <th width="70%">Title</th>
        <th>Amount</th>
    </tr>
    <tr>
        <td width="70%"> Registeration Processing fees</td>
        <td>200</td>
    </tr>
    <tr>
        <td colspan="2">200$</td>
    </tr>
@endcomponent

@component('mail::subcopy')
@endcomponent


Thanks,<br>
#footer
@endcomponent
