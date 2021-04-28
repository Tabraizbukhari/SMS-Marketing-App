@component('mail::message')


@component('mail::panel')
<h1 class="text-center">Registeration Processing Invoice</h1>
Hello {{ $user['first_name'] }},
@endcomponent
<br />
<table class="table">
    <tr> 
        <th width="60%">Title</th>
        <th width="40%">Amount</th>
    </tr>
    <tr>
        <td width="60%"> Registeration Processing fees</td>
        <td width="40%">200</td>
    </tr>
    <tr>
        <td width="60%">Total Amount </td>
        <td width="40%">200$</td>
    </tr>
</table>

Thanks,<br>
#footer
@endcomponent
