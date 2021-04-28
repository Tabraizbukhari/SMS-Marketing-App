<x-dashboard>

<main class="content">
	<div class="container-fluid p-0">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title text-capitalize">{{ $customer->full_name }} Details
                          <a href="{{ route('user.customer.index') }}" class="btn btn-outline-dark float-right" >
                          <span class="align-middle" data-feather="chevron-left" ></span>back
                          </a>
                        </h2>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-responsive">
                            <tr>
                                <th>First Name:</th>
                                <td>{{ $customer->first_name }}</td>
                            </tr>
                            <tr>
                                <th>Last Name:</th>
                                <td>{{ $customer->last_name }}</td>
                            </tr>
                            <tr>
                                <th>Username:</th>
                                <td>{{ $customer->username }}</td>
                            </tr>
                            <tr>
                                <th>Email Address:</th>
                                <td>{{ $customer->email }}</td>
                            </tr>

                            <tr>
                                <th>Sms Amount:</th>
                                <td>{{ $customer->UserData->has_sms }}</td>
                            </tr>

                            <tr>
                                <th>Price:</th>
                                <td>{{ $customer->UserData->price_per_sms }}</td>
                            </tr>

                            <tr>
                                <th>Total no.Sms used:</th>
                                <td>{{ $customer->getAllMessages()->count() }}</td>
                            </tr>

                            @if($customer->getResellerMasking && count($customer->getResellerMasking) > 0)
                            <tr>
                                <th>Masking:</th>
                                <td>@foreach($customer->getResellerMasking as $masking) 
                                    <span class="badge badge-dark">{{ $masking->title}}</span>
                                @endforeach
                                </td>
                            </tr>
                            @endif

                            <tr>
                                <th>Sms Api:</th>
                                <td >{{ $api_url}} <br/>
                                {{ $api_username}}<br/>
                                {{ $api_pass}}<br/>{{ $message}}<br/>{{ $orginator}} 
                                </td>
                            </tr>
                    
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

</x-dashboard>

