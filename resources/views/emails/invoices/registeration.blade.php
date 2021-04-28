@component('mail::message')


@component('mail::panel')
<h1 class="text-center">Registeration Processing Invoice</h1>
Hello {{ $user['first_name'] }},
@endcomponent
<br />
<table class="table">
    <tr> 
        <th>Title</th>
        <th>Amount</th>
    </tr>
    <tr>
        <td> Registeration Processing fees</td>
        <td>200</td>
    </tr>
    <tr>
        <td>Total Amount </td>
        <td>200$</td>
    </tr>
</table>

Thanks,<br>
#footer
@endcomponent
