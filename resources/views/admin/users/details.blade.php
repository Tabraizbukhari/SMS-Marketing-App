@extends('layouts.adminlayout')
@section('content')
    <main class="content">
        <div class="container-fluid p-0">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title text-capitalize">{{ $customer->full_name }} Details
                        <a href="{{ route('user.customer.index') }}" class="btn btn-outline-dark float-right">
                            <span class="align-middle" data-feather="chevron-left"></span>back
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

                        @if ($customer->getResellerMasking && count($customer->getResellerMasking) > 0)
                            <tr>
                                <th>Masking:</th>
                                <td>
                                    @foreach ($customer->getResellerMasking as $masking)
                                        <span class="badge badge-dark">{{ $masking->title }}</span>
                                    @endforeach
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <th>Sms Api:</th>
                            <td>{{ $api_url }} <br />
                                {{ $api_username }}<br />
                                {{ $api_pass }}<br />{{ $message }}<br />{{ $orginator }}
                            </td>
                        </tr>

                    </table>
                </div>
            </div>

        </div>
    </main>


    <!-- Modal Add Reseller -->
    <div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.user.create', 'masking') }}" class="btn btn-outline-info">
                                <div class="card btn-outline-info">
                                    <div class="card-body">
                                        <div class="card-title">
                                            <p class="h1">Masking Reseller</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.user.create', 'code') }}" class="btn btn-outline-info">
                                <div class="card btn-outline-info">
                                    <div class="card-body">
                                        <div class="card-title">
                                            <p class="h1">Code Reseller</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Add Reseller End -->
@endsection
